<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PostProduction;
use App\Models\PostProductionTask;
use App\Models\Staff;
use Illuminate\Http\Request;

class PostProductionController extends Controller
{
    public function index()
    {
        $postProductions = PostProduction::with(['booking.package', 'tasks.staff'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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

        return view('dashboard.post-production-create', compact('booking', 'staff'));
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
            'tasks.*.staff_id' => 'required|exists:staff,id',
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
                'post_production_id' => $postProduction->id,
                'staff_id' => $taskData['staff_id'],
                'task_type' => $taskData['task_type'],
                'instructions' => $taskData['instructions'] ?? null,
                'status' => 'not_started',
                'admin_review_status' => 'pending',
            ]);
        }

        $booking->update(['post_production_status' => 'in_progress']);

        return redirect()->route('post-production.show', $postProduction->id)
            ->with('success', "Post-production started for booking {$booking->booking_ref} with " . count($validated['tasks']) . " task(s).");
    }

    public function show(PostProduction $postProduction)
    {
        $postProduction->load(['booking.package', 'booking.addons', 'booking.customerAccount', 'tasks.staff']);

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

    private function checkAllApproved(PostProduction $postProduction): void
    {
        $allApproved = $postProduction->tasks->count() > 0
            && $postProduction->tasks->every(fn($t) => $t->admin_review_status === 'approved');

        if ($allApproved) {
            $postProduction->update(['status' => 'ready']);
            $postProduction->booking->update(['post_production_status' => 'ready']);
        }
    }
}
