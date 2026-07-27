<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }

        if ($request->filled('condition_status')) {
            $query->where('condition_status', $request->condition_status);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        $totalItems = InventoryItem::count();
        $availableItems = InventoryItem::where('availability_status', 'available')->count();
        $inUseItems = InventoryItem::where('availability_status', 'in_use')->count();
        $maintenanceItems = InventoryItem::where('condition_status', 'maintenance')->count();
        $lowStockItems = InventoryItem::where('quantity', '<=', 2)->count();

        return view('dashboard.inventory', compact(
            'items',
            'totalItems',
            'availableItems',
            'inUseItems',
            'maintenanceItems',
            'lowStockItems'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:equipment,material',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'condition_status' => 'required|in:new,good,maintenance,damaged',
            'availability_status' => 'required|in:available,in_use,reserved,unavailable',
        ]);

        InventoryItem::create($validated);

        return back()->with('success', 'Item added successfully.');
    }

    public function show(InventoryItem $inventoryItem)
    {
        return response()->json($inventoryItem);
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:equipment,material',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'condition_status' => 'required|in:new,good,maintenance,damaged',
            'availability_status' => 'required|in:available,in_use,reserved,unavailable',
        ]);

        $inventoryItem->update($validated);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();

        return back()->with('success', 'Item deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');

        $items = InventoryItem::query()
            ->where('availability_status', '!=', 'unavailable')
            ->where('condition_status', '!=', 'damaged')
            ->where('quantity', '>', 0)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'category', 'quantity', 'unit']);

        return response()->json($items);
    }
}
