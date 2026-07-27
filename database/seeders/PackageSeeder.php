<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Wedding Package',
                'description' => 'Complete wedding videography coverage from preparation to reception.',
                'price' => 45000,
                'status' => 'active',
                'services' => [
                    'Pre-nuptial shoot',
                    'Wedding day coverage',
                    'Same-day edit video',
                    'Full-length wedding film',
                    'Highlight reel',
                ],
            ],
            [
                'name' => 'Debut Package',
                'description' => 'Celebrate your 18th birthday with a cinematic debut coverage.',
                'price' => 25000,
                'status' => 'active',
                'services' => [
                    'Pre-debut shoot',
                    'Event day coverage',
                    'Highlight reel',
                ],
            ],
            [
                'name' => 'Event Package',
                'description' => 'Flexible coverage for corporate events, parties, and celebrations.',
                'price' => 15000,
                'status' => 'active',
                'services' => [
                    'Event day coverage',
                    'Event highlight video',
                ],
            ],
        ];

        foreach ($packages as $data) {
            $services = $data['services'];
            unset($data['services']);

            $package = Package::create($data);
            foreach ($services as $index => $service) {
                $package->services()->create([
                    'service_name' => $service,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
