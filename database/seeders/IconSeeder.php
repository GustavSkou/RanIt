<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeIconsPath = 'images/icons/activity-types/';
        $icons = [
            'running'   => $typeIconsPath . 'running.png',
            'cycling'   => $typeIconsPath . 'cycling.png',
            'walking '  => $typeIconsPath . 'walking.png',
            'swimming'  => $typeIconsPath . 'swimming.png',
            'gym '      => $typeIconsPath . 'gym.png',
        ];

        foreach ($icons as $name => $path) {
            Icon::factory()->create([
                'name' => $name,
                'path' => $path
            ]);
        }
    }
}
