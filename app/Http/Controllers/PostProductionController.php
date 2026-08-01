<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PostProduction;
use App\Models\PostProductionTask;
use App\Models\Staff;
use App\Models\OutsourcedStaff;
use App\Mail\StaffCredentialsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = PostProduction::with(['booking.package', 'tasks.staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('booking_ref', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('event_type', 'like', "%{$search}%");
            });
        }

        $postProductions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            $rowsHtml = view('dashboard.partials.post-production-rows', compact('postProductions'))->render();
            $paginationHtml = $postProductions->hasPages() ? $postProductions->links()->render() : '';

            return response()->json([
                'rows' => $rowsHtml,
                'pagination' => $paginationHtml,
                'total' => $postProductions->total(),
            ]);
        }

        $stats = [
            'total' => PostProduction::count(),
            'in_progress' => PostProduction::whereHas('tasks', fn($q) => $q->where('status', 'in_progress'))->count(),
            'awaiting_review' => PostProduction::whereHas('tasks', fn($q) => $q->where('admin_review_status', 'pending')->where('status', 'completed'))->count(),
            'completed' => PostProduction::whereHas('tasks', fn($q) => $q->where('status', 'completed')->where('admin_review_status', 'approved'))->count(),
        ];

        return view('dashboard.post-production', compact('postProductions', 'stats'));
    }

    public function create(Booking $booking)
    {
        if ($booking->status !== 'completed') {
            return back()->with('error', 'Only completed bookings can be moved to post-production.');
        }

        if ($booking->postProduction) {
            return back()->with('error', 'This booking already has a post-production record.');
        }

        $staff = Staff::where('status', 'active')->get();
        $outsourcedStaff = OutsourcedStaff::orderBy('name')->get();

        return view('dashboard.post-production-create', compact('booking', 'staff', 'outsourcedStaff'));
    }

    public function store(Request $request, Booking $booking)
    {
        if ($booking->status !== 'completed') {
            return back()->with('error', 'Only completed bookings can be moved to post-production.');
        }

        if ($booking->postProduction) {
            return back()->with('error', 'This booking already has a post-production record.');
        }

        $validated = $request->validate([
            'expected_completion_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:2000',
            'tasks' => 'required|array|min:1',
            'tasks.*.assignee_type' => 'required|in:inhouse,outsourced,admin',
            'tasks.*.staff_id' => 'nullable|exists:staff,id',
            'tasks.*.outsourced_staff_id' => 'nullable|exists:outsourced_staff,id',
            'tasks.*.task_type' => 'required|in:photo_editing,video_editing,both',
            'tasks.*.instructions' => 'nullable|string|max:2000',
        ]);

        $postProduction = PostProduction::create([
            'booking_id' => $booking->id,
            'status' => 'in_progress',
            'notes' => $validated['notes'] ?? null,
            'expected_completion_date' => $validated['expected_completion_date'],
            'started_at' => now(),
        ]);

        foreach ($validated['tasks'] as $taskData) {
            PostProductionTask::create([
                'post_production_id'  => $postProduction->id,
                'staff_id'            => $taskData['assignee_type'] === 'inhouse' ? ($taskData['staff_id'] ?? null) : null,
                'outsourced_staff_id' => $taskData['assignee_type'] === 'outsourced' ? ($taskData['outsourced_staff_id'] ?? null) : null,
                'task_type'           => $taskData['task_type'],
                'instructions'        => $taskData['instructions'] ?? null,
                'status'              => $taskData['assignee_type'] === 'admin' ? 'in_progress' : 'not_started',
                'admin_review_status' => 'pending',
            ]);
        }

        $booking->update(['post_production_status' => 'in_progress']);

        return redirect()->route('post-production.show', $postProduction->id)
            ->with('success', "Post-production started for booking {$booking->booking_ref} with " . count($validated['tasks']) . " task(s).");
    }

    public function show(PostProduction $postProduction)
    {
        $postProduction->load(['booking.package', 'booking.addons', 'booking.customerAccount', 'tasks.staff', 'tasks.outsourcedStaff']);

        $allApproved = $postProduction->tasks->count() > 0
            && $postProduction->tasks->every(fn($t) => $t->admin_review_status === 'approved');

        $booking = $postProduction->booking;
        $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $isFullyPaid = $totalPaid >= $booking->total_price;
        $remainingBalance = max(0, $booking->total_price - $totalPaid);

        return view('dashboard.post-production-details', compact('postProduction', 'allApproved', 'totalPaid', 'isFullyPaid', 'remainingBalance'));
    }

    public function approveTask(Request $request, PostProductionTask $task)
    {
        $task->update([
            'admin_review_status' => 'approved',
            'revision_notes' => null,
        ]);

        $this->checkAllApproved($task->postProduction);

        return back()->with('success', 'Task approved successfully.');
    }

    public function requestRevision(Request $request, PostProductionTask $task)
    {
        $validated = $request->validate([
            'revision_notes' => 'required|string|max:2000',
        ]);

        $task->update([
            'admin_review_status' => 'revision_requested',
            'revision_notes' => $validated['revision_notes'],
            'status' => 'in_progress',
            'deliverable_link' => null,
            'remarks' => null,
            'completed_at' => null,
        ]);

        return back()->with('success', 'Revision requested. Staff will be notified.');
    }

    public function updateNotes(Request $request, PostProduction $postProduction)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $postProduction->update(['notes' => $validated['notes'] ?? null]);

        return back()->with('success', 'Post-production notes updated.');
    }

    public function markEventComplete(Booking $booking)
    {
        if ($booking->status !== 'ongoing') {
            return back()->with('error', 'Only ongoing bookings can be marked as completed.');
        }

        $booking->update([
            'status' => 'completed',
            'event_completed_at' => now(),
        ]);

        return back()->with('success', "Event for booking {$booking->booking_ref} has been marked as completed.");
    }

    public function unlockDeliverables(Booking $booking)
    {
        if (!$booking->postProduction) {
            return back()->with('error', 'No post-production record found.');
        }

        $allApproved = $booking->postProduction->tasks->count() > 0
            && $booking->postProduction->tasks->every(fn($t) => $t->admin_review_status === 'approved');

        if (!$allApproved) {
            return back()->with('error', 'All tasks must be approved before unlocking deliverables.');
        }

        $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $isFullyPaid = $totalPaid >= $booking->total_price;

        if (!$isFullyPaid) {
            return back()->with('error', 'Cannot unlock deliverables. The client still has an unpaid balance of ₱' . number_format($booking->total_price - $totalPaid, 2) . '.');
        }

        $booking->update([
            'deliverables_unlocked' => true,
            'final_payment_status' => 'paid',
            'status' => 'completed',
            'post_production_status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $booking->postProduction->update([
            'status' => 'delivered',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Deliverables unlocked and client can download files.');
    }

    public function adminUpdateTaskLink(Request $request, PostProductionTask $task)
    {
        $validated = $request->validate([
            'deliverable_link' => 'required|url|max:2000',
            'remarks'          => 'nullable|string|max:2000',
        ]);

        $task->update([
            'deliverable_link'    => $validated['deliverable_link'],
            'remarks'             => $validated['remarks'] ?? null,
            'status'              => 'completed',
            'completed_at'        => now(),
            'admin_review_status' => 'pending',
        ]);

        return back()->with('success', 'Deliverable link saved.');
    }

    private function checkAllApproved(PostProduction $postProduction): void
    {
        $allApproved = $postProduction->tasks->count() > 0
            && $postProduction->tasks->every(fn($t) => $t->admin_review_status === 'approved');

        if ($allApproved) {
            $postProduction->update(['status' => 'ready']);
            $postProduction->booking->update(['post_production_status' => 'ready']);
        }
    }

    public function createOutsourcedAccount(Request $request, PostProductionTask $task)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Reuse existing staff record if email matches, otherwise create new temp account
        $staff = Staff::where('email', $validated['email'])->first();
        $plainPassword = \App\Http\Controllers\StaffController::generateTempPassword();

        if ($staff) {
            $staff->update([
                'password'       => $plainPassword,
                'is_outsourced'  => true,
                'is_temporary'   => true,
                'temp_expires_at'=> null,
                'status'         => 'active',
            ]);
        } else {
            $staff = Staff::create([
                'name'           => $validated['name'],
                'email'          => $validated['email'],
                'password'       => $plainPassword,
                'is_outsourced'  => true,
                'is_temporary'   => true,
                'temp_expires_at'=> null,
                'status'         => 'active',
            ]);
        }

        // Assign this task to the outsourced staff
        $task->update(['staff_id' => $staff->id]);

        try {
            Mail::to($staff->email)->send(new StaffCredentialsEmail(
                $staff->name,
                $staff->email,
                $plainPassword,
                $task->postProduction->booking->booking_ref ?? null,
            ));
            $mailSent = true;
        } catch (\Exception $e) {
            $mailSent = false;
        }

        $msg = "Temporary account created for {$staff->name}.";
        if (!$mailSent) {
            $msg .= " (Email could not be sent — password: {$plainPassword})";
        }

        return back()->with('success', $msg);
    }
}
