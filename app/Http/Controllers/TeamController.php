<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Staff;
use App\Models\OutsourcedStaff;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
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

        $teams = $query->latest()->paginate(10)->withQueryString();
        $allStaff = Staff::where('status', 'active')->orderBy('name')->get();
        $allOutsourced = OutsourcedStaff::orderBy('name')->get();

        $totalTeams = Team::count();
        $activeTeams = Team::where('status', 'active')->count();

        return view('dashboard.teams', compact('teams', 'allStaff', 'allOutsourced', 'totalTeams', 'activeTeams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'nullable|string|max:500',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:staff,id',
            'outsourced_ids' => 'nullable|array',
            'outsourced_ids.*' => 'exists:outsourced_staff,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        if (!empty($validated['member_ids'])) {
            $team->members()->attach($validated['member_ids']);
        }
        if (!empty($validated['outsourced_ids'])) {
            $team->outsourcedMembers()->attach($validated['outsourced_ids']);
        }

        return back()->with('success', 'Team created successfully.');
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string|max:500',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:staff,id',
            'outsourced_ids' => 'nullable|array',
            'outsourced_ids.*' => 'exists:outsourced_staff,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $team->members()->sync($validated['member_ids'] ?? []);
        $team->outsourcedMembers()->sync($validated['outsourced_ids'] ?? []);

        return back()->with('success', 'Team updated successfully.');
    }

    public function toggleStatus(Team $team)
    {
        $newStatus = $team->status === 'active' ? 'inactive' : 'active';
        $team->update(['status' => $newStatus]);

        return back()->with('success', "Team {$newStatus}.");
    }

    public function destroy(Team $team)
    {
        $team->members()->detach();
        $team->delete();

        return back()->with('success', 'Team deleted permanently.');
    }
}
