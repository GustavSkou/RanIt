<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            'running',
            'cycling',
            'virtual ride',
            'walking',
            'workout',
        ];

        foreach ($sports as $sport) {
            Sport::factory()->create([
                'name' => $sport
            ]);
        }
    }
}