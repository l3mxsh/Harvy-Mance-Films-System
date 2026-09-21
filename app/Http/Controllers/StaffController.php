<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\OutsourcedStaff;
use App\Models\Team;
use App\Mail\StaffCredentialsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $inactiveStaff = Staff::where('status', 'inactive')->count();
        $outsourcedStaff = OutsourcedStaff::orderBy('name')->get();

        if ($tab === 'teams') {
            $query = Team::with('members', 'outsourcedMembers');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $allStaff = Staff::where('status', 'active')->orderBy('name')->get();
            $allOutsourced = OutsourcedStaff::orderBy('name')->get();
            $totalTeams = Team::count();
            $activeTeams = Team::where('status', 'active')->count();

            $teams = $query->latest()->paginate(10)->withQueryString();

            if ($request->ajax()) {
                if ($request->has('content')) {
                    $html = view('dashboard.partials.staff-teams-tab', compact('teams', 'allStaff', 'allOutsourced', 'totalTeams', 'activeTeams', 'totalStaff', 'activeStaff', 'inactiveStaff', 'outsourcedStaff', 'tab'))->render();

                    return response()->json(['html' => $html]);
                }

                $rowsHtml = view('dashboard.partials.team-rows', compact('teams'))->render();
                $paginationHtml = $teams->hasPages() ? $teams->links('vendor.pagination.bootstrap-5')->render() : '';

                return response()->json([
                    'rows' => $rowsHtml,
                    'pagination' => $paginationHtml,
                    'total' => $teams->total(),
                ]);
            }

            return view('dashboard.staff', compact('teams', 'allStaff', 'allOutsourced', 'totalTeams', 'activeTeams', 'totalStaff', 'activeStaff', 'inactiveStaff', 'outsourcedStaff', 'tab'));
        }

        if ($tab === 'outsourced') {
            if ($request->ajax() && $request->has('content')) {
                $html = view('dashboard.partials.staff-outsourced-tab', compact('outsourcedStaff', 'totalStaff', 'activeStaff', 'inactiveStaff', 'tab'))->render();

                return response()->json(['html' => $html]);
            }

            return view('dashboard.staff', compact('outsourcedStaff', 'totalStaff', 'activeStaff', 'inactiveStaff', 'tab'));
        }

        $query = Staff::query();

        if ($tab === 'in-house') {
            $query->where('is_outsourced', false);
        }

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

        if ($request->ajax()) {
            if ($request->has('content')) {
                $html = view('dashboard.partials.staff-list-tab', compact('staff', 'outsourcedStaff', 'totalStaff', 'activeStaff', 'inactiveStaff', 'tab'))->render();

                return response()->json(['html' => $html]);
            }

            $rowsHtml = view('dashboard.partials.staff-rows', compact('staff'))->render();
            $paginationHtml = $staff->hasPages() ? $staff->links('vendor.pagination.bootstrap-5')->render() : '';

            return response()->json([
                'rows' => $rowsHtml,
                'pagination' => $paginationHtml,
                'total' => $staff->total(),
            ]);
        }

        return view('dashboard.staff', compact('staff', 'outsourcedStaff', 'totalStaff', 'activeStaff', 'inactiveStaff', 'tab'));
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
        if ($request->boolean('notify') && !$request->filled('new_password')) {
            return back()->with('error', 'Generate or enter a new password before emailing it.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email,' . $staff->id,
            'contact_number' => 'nullable|string|max:50',
        ];

        if ($request->filled('new_password')) {
            $rules['new_password'] = 'required|string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
        ];

        if ($request->filled('new_password')) {
            $data['password'] = $request->new_password;
        }

        $staff->update($data);

        if ($request->boolean('notify')) {
            try {
                Mail::to($staff->email)->send(
                    new StaffCredentialsEmail(
                        $staff->name,
                        $staff->email,
                        $request->new_password
                    )
                );
            } catch (\Exception $e) {
                // Mail may fail
            }

            return back()->with('success', 'Staff account updated and new password emailed to ' . $staff->email . '.');
        }

        return back()->with('success', 'Staff account updated successfully.');
    }

    public function resetPassword(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $staff->update(['password' => $validated['new_password']]);

        if ($request->boolean('notify')) {
            try {
                Mail::to($staff->email)->send(
                    new StaffCredentialsEmail(
                        $staff->name,
                        $staff->email,
                        $validated['new_password']
                    )
                );
            } catch (\Exception $e) {
                // Mail may fail
            }

            return back()->with('success', "Password updated and emailed to {$staff->email}.");
        }

        return back()->with('success', "Password updated for {$staff->name}. No email sent.");
    }

    public function generatePassword(Request $request, Staff $staff)
    {
        if (!$staff->is_outsourced) {
            return back()->with('error', 'Generate & email temporary password is only for outsourced staff. Use the new password form instead.');
        }

        $tempPassword = self::generateTempPassword();

        $staff->update(['password' => $tempPassword]);

        try {
            Mail::to($staff->email)->send(
                new StaffCredentialsEmail(
                    $staff->name,
                    $staff->email,
                    $tempPassword
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
