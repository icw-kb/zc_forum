<?php

namespace Database\Seeders;

use App\Models\Forum;
use App\Models\ForumGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Zen Cart Release Announcements', 'forum_group_id' => 1],
            ['name' => 'Support Site News', 'forum_group_id' => 1],
            ['name' => 'Development Roadmap', 'forum_group_id' => 1],
            ['name' => 'Concerns about Hack Attempts', 'forum_group_id' => 2],
            ['name' => 'reports about Security Problems', 'forum_group_id' => 2],
            ];
        foreach ($data as $entry) {
            Forum::create($entry);
        }
    }
}
