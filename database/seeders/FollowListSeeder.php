<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FollowList;

class FollowListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        for ($i = 0; $i < count($users); $i++) {
            $users[$i];
            FollowList::factory(1)->create([
                'user_id' => $users[$i]->id,
                'follows_user_id' => $users[($i + 1) % count($users)]->id
            ]);
        }
    }
}
