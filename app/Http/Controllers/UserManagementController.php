<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            $rowsHtml = view('dashboard.partials.user-rows', compact('users'))->render();
            $paginationHtml = $users->hasPages() ? $users->links()->render() : '';

            return response()->json([
                'rows' => $rowsHtml,
                'pagination' => $paginationHtml,
                'total' => $users->total(),
            ]);
        }

        return view('dashboard.users', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'status' => 'active',
        ]);

        ActivityLog::log('user.created', "Created admin account {$validated['name']} ({$validated['email']}).");

        return back()->with('success', 'Admin account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($request->filled('new_password')) {
            $rules['new_password'] = 'required|string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->filled('new_password')) {
            $data['password'] = $request->new_password;
        }

        $user->update($data);

        ActivityLog::log('user.updated', "Updated admin account {$user->name} ({$user->email}).", [
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Admin account updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';

        if ($newStatus === 'inactive' && $this->isLastActiveAdmin($user)) {
            return back()->with('error', 'Cannot deactivate the last active admin account.');
        }

        $user->update(['status' => $newStatus]);

        ActivityLog::log('user.status_changed', "Admin account {$user->name} is now {$newStatus}.", [
            'user_id' => $user->id,
            'status' => $newStatus,
        ]);

        return back()->with('success', "Admin account {$user->name} is now {$newStatus}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($this->isLastAdmin($user)) {
            return back()->with('error', 'Cannot delete the last admin account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('user.deleted', "Deleted admin account {$name} permanently.");

        return back()->with('success', "Admin account {$name} deleted permanently.");
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return User::where('role', 'admin')
            ->where('status', 'active')
            ->where('id', '!=', $user->id)
            ->count() === 0;
    }

    private function isLastAdmin(User $user): bool
    {
        return User::where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->count() === 0;
    }
}
