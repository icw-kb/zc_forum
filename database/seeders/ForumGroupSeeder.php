<?php

namespace Database\Seeders;

use App\Models\ForumGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [['name' => 'News and Announcements'], ['name' => 'Security Issues and Reports'], ['name' => 'Documentation']];
        foreach ($data as $entry) {
            ForumGroup::create($entry);
        }
    }
}
