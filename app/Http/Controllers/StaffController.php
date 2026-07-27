<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Mail\BookingCredentialsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->latest()->paginate(10)->withQueryString();

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $inactiveStaff = Staff::where('status', 'inactive')->count();

        return view('dashboard.staff', compact('staff', 'totalStaff', 'activeStaff', 'inactiveStaff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email',
            'contact_number' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Staff::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'password' => $validated['password'],
            'status' => 'active',
        ]);

        return back()->with('success', 'Staff account created successfully.');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email,' . $staff->id,
            'contact_number' => 'nullable|string|max:50',
        ]);

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
        ]);

        return back()->with('success', 'Staff account updated successfully.');
    }

    public function resetPassword(Request $request, Staff $staff)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $staff->update(['password' => $request->new_password]);

        return back()->with('success', "Password reset successfully for {$staff->name}.");
    }

    public function generatePassword(Request $request, Staff $staff)
    {
        $tempPassword = self::generateTempPassword();

        $staff->update(['password' => $tempPassword]);

        try {
            Mail::to($staff->email)->send(
                new BookingCredentialsEmail(
                    $staff->name,
                    'STAFF',
                    $tempPassword,
                    'Staff Account'
                )
            );
        } catch (\Exception $e) {
            // Mail may fail
        }

        return back()->with('success', "New temporary password generated and sent to {$staff->email}.");
    }

    public function toggleStatus(Staff $staff)
    {
        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->update(['status' => $newStatus]);

        return back()->with('success', "Staff account {$newStatus}.");
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return back()->with('success', 'Staff account deleted permanently.');
    }

    public static function generateTempPassword(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $password = '';
        for ($i = 0; $i < 8; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
