<?php

namespace App\Http\Controllers;

use App\Models\OutsourcedStaff;
use Illuminate\Http\Request;

class OutsourcedStaffController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        OutsourcedStaff::create($validated);

        return back()->with('success', "Outsourced staff \"{$validated['name']}\" added.");
    }

    public function update(Request $request, OutsourcedStaff $outsourcedStaff)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        $outsourcedStaff->update($validated);

        return back()->with('success', 'Outsourced staff updated.');
    }

    public function destroy(OutsourcedStaff $outsourcedStaff)
    {
        $outsourcedStaff->delete();

        return back()->with('success', 'Outsourced staff record deleted.');
    }
}
