<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'name' => 'Drone Aerial Coverage',
                'description' => 'Additional cinematic drone shots of the event for a dramatic aerial perspective.',
                'price' => 5000,
                'status' => 'active',
            ],
            [
                'name' => 'Same-Day Edit (SDE) Video',
                'description' => 'Edited highlight video presented during the event.',
                'price' => 8000,
                'status' => 'active',
            ],
            [
                'name' => 'Teaser Video',
                'description' => 'Short promotional video for social media posting.',
                'price' => 3000,
                'status' => 'active',
            ],
            [
                'name' => 'Social Media Content Package',
                'description' => 'Reels, short clips, and social media-ready content.',
                'price' => 4000,
                'status' => 'active',
            ],
            [
                'name' => 'Pre-Event Shoot',
                'description' => 'Additional engagement, prenup, or preparation photoshoot.',
                'price' => 6000,
                'status' => 'active',
            ],
            [
                'name' => 'Live Streaming Service',
                'description' => 'Livestream coverage for remote guests.',
                'price' => 7000,
                'status' => 'active',
            ],
            [
                'name' => 'Rush Delivery',
                'description' => 'Faster delivery of final outputs.',
                'price' => 3500,
                'status' => 'active',
            ],
            [
                'name' => 'Premium Album Upgrade',
                'description' => 'Upgraded album design and materials.',
                'price' => 5000,
                'status' => 'active',
            ],
            [
                'name' => 'Print Package Upgrade',
                'description' => 'Additional printed photos and physical outputs.',
                'price' => 2500,
                'status' => 'active',
            ],
            [
                'name' => 'Cloud Storage Extension',
                'description' => 'Extended access to delivered files.',
                'price' => 1500,
                'status' => 'active',
            ],
        ];

        foreach ($addons as $addon) {
            Addon::create($addon);
        }
    }
}
