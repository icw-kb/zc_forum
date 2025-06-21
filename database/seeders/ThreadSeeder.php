<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some specific PayPal-related threads for search testing
        $forums = \App\Models\Forum::all();
        $users = \App\Models\User::all();
        
        if ($forums->count() > 0 && $users->count() > 0) {
            $paymentForum = $forums->first(); // Use first available forum
            $user = $users->first(); // Use first available user
            
            // PayPal related threads
            $paypalThreads = [
                [
                    'title' => 'PayPal Express Checkout Integration Issues',
                    'forum_id' => $paymentForum->id,
                    'user_id' => $user->id,
                ],
                [
                    'title' => 'PayPal IPN Configuration Help Needed',
                    'forum_id' => $paymentForum->id,
                    'user_id' => $user->id,
                ],
                [
                    'title' => 'PayPal Standard vs Express vs Pro - Which to Choose?',
                    'forum_id' => $paymentForum->id,
                    'user_id' => $user->id,
                ],
                [
                    'title' => 'PayPal Sandbox Testing Not Working',
                    'forum_id' => $paymentForum->id,
                    'user_id' => $user->id,
                ],
            ];
            
            foreach ($paypalThreads as $threadData) {
                \App\Models\Thread::create($threadData);
            }
        }
        
        // Create random threads
        \App\Models\Thread::factory(16)->create(); // Reduced to maintain total of 20
    }
}
