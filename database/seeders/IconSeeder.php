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
        $typeIconsPath = 'icons/activity-types/';
        $icons = [
            'running'   => $typeIconsPath . 'running.png',
            'cycling'   => $typeIconsPath . 'cycling.png',
            'walking '  => $typeIconsPath . 'walking.png',
            'swimming'  => $typeIconsPath . 'swimming.png',
            'workout '  => $typeIconsPath . 'workout.png',
            'route'     => $typeIconsPath . 'route.png'
        ];

        foreach ($icons as $name => $path) {
            Icon::factory()->create([
                'name' => $name,
                'path' => $path
            ]);
        }
    }
}
