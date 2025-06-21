<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some specific PayPal-related posts for search testing
        $threads = \App\Models\Thread::all();
        $users = \App\Models\User::all();
        
        if ($threads->count() > 0 && $users->count() > 0) {
            $paypalThreads = $threads->where('title', 'like', '%PayPal%');
            $user = $users->first();
            
            if ($paypalThreads->count() > 0) {
                // Create initial posts for PayPal threads (these are the opening posts)
                $threadContents = [
                    'PayPal Express Checkout Integration Issues' => 'I\'m having trouble integrating PayPal Express Checkout with my Zen Cart store. The payments are not processing correctly and customers are getting error messages. Has anyone successfully implemented PayPal Express Checkout recently?',
                    'PayPal IPN Configuration Help Needed' => 'Need help configuring PayPal IPN (Instant Payment Notification) for my Zen Cart store. The IPN messages are not being received properly and order statuses are not updating automatically. Any guidance would be appreciated.',
                    'PayPal Standard vs Express vs Pro - Which to Choose?' => 'I\'m setting up a new Zen Cart store and trying to decide between PayPal Standard, Express, and Pro payment methods. What are the pros and cons of each? Which one would you recommend for a small to medium business?',
                    'PayPal Sandbox Testing Not Working' => 'I\'ve configured my Zen Cart store to use PayPal sandbox for testing, but the test transactions are failing. The error logs show connection timeouts. Has anyone else experienced this issue with PayPal sandbox recently?',
                ];
                
                foreach ($paypalThreads as $thread) {
                    // Create initial post for each thread
                    if (isset($threadContents[$thread->title])) {
                        \App\Models\Post::create([
                            'content' => $threadContents[$thread->title],
                            'thread_id' => $thread->id,
                            'forum_id' => $thread->forum_id,
                            'user_id' => $thread->user_id,
                        ]);
                    }
                }
                
                // Create some reply posts
                $firstPaypalThread = $paypalThreads->first();
                $replyPosts = [
                    [
                        'content' => 'I had the same PayPal Express Checkout issue last month. The problem was in the API credentials configuration. Make sure you\'re using the correct PayPal API username, password, and signature from your PayPal business account.',
                        'thread_id' => $firstPaypalThread->id,
                        'forum_id' => $firstPaypalThread->forum_id,
                        'user_id' => $user->id,
                    ],
                    [
                        'content' => 'For PayPal integration, I always recommend using the latest PayPal Express Checkout module. It\'s more secure and provides better customer experience. Also, make sure your SSL certificate is properly configured.',
                        'thread_id' => $firstPaypalThread->id,
                        'forum_id' => $firstPaypalThread->forum_id,
                        'user_id' => $user->id,
                    ],
                    [
                        'content' => 'Has anyone tried the new PayPal Checkout experience? I\'m wondering if it\'s compatible with Zen Cart and if it offers any advantages over the traditional PayPal Express Checkout.',
                        'thread_id' => $firstPaypalThread->id,
                        'forum_id' => $firstPaypalThread->forum_id,
                        'user_id' => $user->id,
                    ],
                ];
                
                foreach ($replyPosts as $postData) {
                    \App\Models\Post::create($postData);
                }
            }
        }
        
        // Create random posts
        \App\Models\Post::factory(93)->create(); // Reduced to account for PayPal posts (4 initial + 3 replies = 7 posts)
    }
}
