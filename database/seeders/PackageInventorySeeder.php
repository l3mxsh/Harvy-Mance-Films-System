<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class PackageInventorySeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            'Wedding Package' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 2],
                ['name' => 'Canon EOS R5 Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 2],
                ['name' => 'Sony 70-200mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'Sony 35mm f/1.4 Lens', 'quantity' => 1],
                ['name' => 'DJI RS 3 Gimbal Stabilizer', 'quantity' => 1],
                ['name' => 'Manfrotto 502AH Video Tripod', 'quantity' => 2],
                ['name' => 'Godox SL150II LED Light', 'quantity' => 2],
                ['name' => 'Godox AD200 Pro Flash', 'quantity' => 2],
                ['name' => 'Rode VideoMic Pro+', 'quantity' => 2],
                ['name' => 'Zoom H6 Audio Recorder', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 4],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 6],
                ['name' => 'Atomos Ninja V Monitor', 'quantity' => 1],
                ['name' => 'Muslin Backdrop (White)', 'quantity' => 1],
                ['name' => 'Premium Photo Album (20x30)', 'quantity' => 1],
                ['name' => 'USB Drive (64GB)', 'quantity' => 1],
                ['name' => 'Gift Box Packaging', 'quantity' => 1],
            ],
            'Debut Package' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Canon EOS R5 Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'Sony 70-200mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'DJI RS 3 Gimbal Stabilizer', 'quantity' => 1],
                ['name' => 'Manfrotto 502AH Video Tripod', 'quantity' => 1],
                ['name' => 'Godox SL150II LED Light', 'quantity' => 2],
                ['name' => 'Rode VideoMic Pro+', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 3],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 4],
                ['name' => 'Classic Photo Album (11x14)', 'quantity' => 1],
                ['name' => 'USB Drive (32GB)', 'quantity' => 1],
                ['name' => 'Gift Box Packaging', 'quantity' => 1],
            ],
            'Event Package' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'Manfrotto 502AH Video Tripod', 'quantity' => 1],
                ['name' => 'Godox SL150II LED Light', 'quantity' => 1],
                ['name' => 'Rode VideoMic Pro+', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 2],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 3],
            ],
        ];

        foreach ($assignments as $packageName => $items) {
            $package = Package::where('name', $packageName)->first();
            if (!$package) continue;

            $itemsToSync = [];
            foreach ($items as $item) {
                $inventoryItem = InventoryItem::where('name', $item['name'])->first();
                if ($inventoryItem) {
                    $itemsToSync[$inventoryItem->id] = ['quantity' => $item['quantity']];
                }
            }

            $package->inventory()->sync($itemsToSync);
        }
    }
}
