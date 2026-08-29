<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Addon;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $packages = Package::with(['services', 'inventory'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $addons = Addon::with('inventory')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'packageRows' => view('dashboard.partials.package-rows', compact('packages'))->render(),
                'packageMobileRows' => view('dashboard.partials.package-mobile-rows', compact('packages'))->render(),
                'addonRows' => view('dashboard.partials.addon-rows', compact('addons'))->render(),
                'addonMobileRows' => view('dashboard.partials.addon-mobile-rows', compact('addons'))->render(),
                'packageTotal' => $packages->count(),
                'addonTotal' => $addons->count(),
            ]);
        }

        return view('dashboard.package', compact('packages', 'addons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'services' => 'required|array|min:1',
            'services.*' => 'required|string|max:255',
            'inventory_items' => 'nullable|array',
            'inventory_items.*' => 'required|integer|exists:inventory_items,id',
            'inventory_quantities' => 'nullable|array',
            'inventory_quantities.*' => 'required|integer|min:1',
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        foreach ($validated['services'] as $index => $service) {
            $package->services()->create([
                'service_name' => $service,
                'sort_order' => $index,
            ]);
        }

        if (!empty($validated['inventory_items'])) {
            foreach ($validated['inventory_items'] as $index => $itemId) {
                $qty = $validated['inventory_quantities'][$index] ?? 1;
                $package->inventory()->attach($itemId, ['quantity' => $qty]);
            }
        }

        return redirect()->route('package')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $package->load(['services', 'inventory']);
        return view('dashboard.package-edit', compact('package'));
    }

    public function show(Package $package)
    {
        $package->load(['services', 'inventory']);
        return response()->json($package);
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'services' => 'required|array|min:1',
            'services.*' => 'required|string|max:255',
            'inventory_items' => 'nullable|array',
            'inventory_items.*' => 'required|integer|exists:inventory_items,id',
            'inventory_quantities' => 'nullable|array',
            'inventory_quantities.*' => 'required|integer|min:1',
        ]);

        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        $package->services()->delete();
        foreach ($validated['services'] as $index => $service) {
            $package->services()->create([
                'service_name' => $service,
                'sort_order' => $index,
            ]);
        }

        $package->inventory()->detach();
        if (!empty($validated['inventory_items'])) {
            foreach ($validated['inventory_items'] as $index => $itemId) {
                $qty = $validated['inventory_quantities'][$index] ?? 1;
                $package->inventory()->attach($itemId, ['quantity' => $qty]);
            }
        }

        return redirect()->route('package')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('package')->with('success', 'Package deleted successfully.');
    }
}
