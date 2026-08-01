<?php

namespace App\Http\Controllers;

use App\Models\PostProductionTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class StaffPostProductionController extends Controller
{
    public function showLogin()
    {
        if (auth('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = md5('staff_login' . $request->ip());
        $maxAttempts = 5;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . max(1, ceil($seconds / 60)) . ' minute(s).',
            ])->onlyInput('email');
        }

        $staff = \App\Models\Staff::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            RateLimiter::hit($key, $decaySeconds);
            $remaining = RateLimiter::retriesLeft($key, $maxAttempts);
            return back()->withErrors([
                'email' => "Invalid email or password. You have {$remaining} attempts remaining.",
            ])->withInput($request->only('email'));
        }

        if ($staff->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active.',
            ])->withInput($request->only('email'));
        }

        if ($staff->isExpiredTemp()) {
            return back()->withErrors([
                'email' => 'Your temporary access has expired.',
            ])->withInput($request->only('email'));
        }

        RateLimiter::clear($key);

        auth('staff')->login($staff);
        $staff->update(['last_login_at' => now()]);

        return redirect()->route('staff.dashboard');
    }

    public function logout()
    {
        auth('staff')->logout();
        return redirect()->route('staff.login');
    }

    public function dashboard()
    {
        $staff = auth('staff')->user();

        $tasks = PostProductionTask::with(['postProduction.booking'])
            ->where('staff_id', $staff->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'not_started' => $tasks->where('status', 'not_started')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
        ];

        return view('staff.dashboard', compact('staff', 'tasks', 'stats'));
    }

    public function showTask(PostProductionTask $task)
    {
        $staff = auth('staff')->user();

        if ($task->staff_id !== $staff->id) {
            abort(403);
        }

        $task->load(['postProduction.booking.package', 'postProduction.booking.customerAccount']);

        return view('staff.task', compact('staff', 'task'));
    }

    public function updateTask(Request $request, PostProductionTask $task)
    {
        $staff = auth('staff')->user();

        if ($task->staff_id !== $staff->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed',
            'deliverable_link' => 'nullable|url|max:2000',
            'remarks' => 'nullable|string|max:2000',
        ]);

        if ($validated['status'] === 'completed') {
            if (empty($validated['deliverable_link'])) {
                return back()->with('error', 'You must provide a deliverable link before marking the task as completed.')->withInput();
            }
            $validated['completed_at'] = now();
            $validated['admin_review_status'] = 'pending';
        }

        $task->update($validated);

        // Auto-expire temp account when all their tasks are done
        if ($staff->is_temporary && $validated['status'] === 'completed') {
            $pendingTasks = PostProductionTask::where('staff_id', $staff->id)
                ->whereNotIn('status', ['completed'])
                ->count();

            if ($pendingTasks === 0) {
                $staff->update([
                    'temp_expires_at' => now(),
                    'status' => 'inactive',
                ]);
            }
        }

        return back()->with('success', 'Task updated successfully.');
    }
}
