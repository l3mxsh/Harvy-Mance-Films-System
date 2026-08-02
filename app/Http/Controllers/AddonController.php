<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::with('inventory')->latest()->get();
        return response()->json($addons);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'inventory_items' => 'nullable|array',
            'inventory_items.*' => 'required|integer|exists:inventory_items,id',
            'inventory_quantities' => 'nullable|array',
            'inventory_quantities.*' => 'required|integer|min:1',
        ]);

        $addon = Addon::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['inventory_items'])) {
            foreach ($validated['inventory_items'] as $index => $itemId) {
                $qty = $validated['inventory_quantities'][$index] ?? 1;
                $addon->inventory()->attach($itemId, ['quantity' => $qty]);
            }
        }

        return redirect()->route('package')->with('success', 'Add-on created successfully.');
    }

    public function edit(Addon $addon)
    {
        $addon->load('inventory');
        return view('dashboard.addon-edit', compact('addon'));
    }

    public function show(Addon $addon)
    {
        $addon->load('inventory');
        return response()->json($addon);
    }

    public function update(Request $request, Addon $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'inventory_items' => 'nullable|array',
            'inventory_items.*' => 'required|integer|exists:inventory_items,id',
            'inventory_quantities' => 'nullable|array',
            'inventory_quantities.*' => 'required|integer|min:1',
        ]);

        $addon->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        $addon->inventory()->detach();
        if (!empty($validated['inventory_items'])) {
            foreach ($validated['inventory_items'] as $index => $itemId) {
                $qty = $validated['inventory_quantities'][$index] ?? 1;
                $addon->inventory()->attach($itemId, ['quantity' => $qty]);
            }
        }

        return redirect()->route('package')->with('success', 'Add-on updated successfully.');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();
        return redirect()->route('package')->with('success', 'Add-on deleted successfully.');
    }
}
