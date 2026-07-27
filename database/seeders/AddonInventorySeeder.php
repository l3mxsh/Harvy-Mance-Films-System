<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class AddonInventorySeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            'Drone Aerial Coverage' => [
                ['name' => 'DJI Mavic 3 Pro Drone', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 1],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 2],
            ],
            'Same-Day Edit (SDE) Video' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'MacBook Pro 16" M3', 'quantity' => 1],
                ['name' => 'Samsung T7 2TB SSD', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 1],
            ],
            'Teaser Video' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'DJI RS 3 Gimbal Stabilizer', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 1],
            ],
            'Social Media Content Package' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Sony 35mm f/1.4 Lens', 'quantity' => 1],
                ['name' => 'DJI RS 3 Gimbal Stabilizer', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 1],
                ['name' => 'Samsung T7 2TB SSD', 'quantity' => 1],
            ],
            'Pre-Event Shoot' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Canon EOS R5 Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'Sony 35mm f/1.4 Lens', 'quantity' => 1],
                ['name' => 'Manfrotto 502AH Video Tripod', 'quantity' => 1],
                ['name' => 'Godox SL150II LED Light', 'quantity' => 2],
                ['name' => 'Godox AD200 Pro Flash', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 2],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 4],
                ['name' => 'Muslin Backdrop (White)', 'quantity' => 1],
            ],
            'Live Streaming Service' => [
                ['name' => 'Sony A7IV Camera', 'quantity' => 1],
                ['name' => 'Sony 24-70mm f/2.8 Lens', 'quantity' => 1],
                ['name' => 'Manfrotto 502AH Video Tripod', 'quantity' => 1],
                ['name' => 'Rode VideoMic Pro+', 'quantity' => 1],
                ['name' => 'Zoom H6 Audio Recorder', 'quantity' => 1],
                ['name' => 'Atomos Ninja V Monitor', 'quantity' => 1],
                ['name' => 'SanDisk 128GB CFexpress Card', 'quantity' => 2],
                ['name' => 'Sony NP-FZ100 Battery', 'quantity' => 3],
            ],
            'Premium Album Upgrade' => [
                ['name' => 'Premium Photo Album (20x30)', 'quantity' => 1],
                ['name' => 'Gift Box Packaging', 'quantity' => 1],
                ['name' => 'Tissue Paper & Ribbon Set', 'quantity' => 1],
            ],
            'Print Package Upgrade' => [
                ['name' => 'Photo Print Paper (A4 Glossy)', 'quantity' => 20],
                ['name' => 'Canvas Print Material', 'quantity' => 2],
                ['name' => 'Sticker Labels (Custom)', 'quantity' => 5],
            ],
        ];

        foreach ($assignments as $addonName => $items) {
            $addon = Addon::where('name', $addonName)->first();
            if (!$addon) continue;

            foreach ($items as $item) {
                $inventoryItem = InventoryItem::where('name', $item['name'])->first();
                if ($inventoryItem) {
                    $addon->inventory()->attach($inventoryItem->id, ['quantity' => $item['quantity']]);
                }
            }
        }
    }
}
