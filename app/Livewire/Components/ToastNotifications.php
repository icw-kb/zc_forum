<?php
// app/Livewire/Components/ToastNotifications.php

namespace App\Livewire\Components;

use Livewire\Component;

class ToastNotifications extends Component
{
    public $notifications = [];

    protected $listeners = ['show-toast' => 'addNotification'];

    public function addNotification($data)
    {
        $notification = [
            'id' => uniqid(),
            'type' => $data['type'] ?? 'success',
            'message' => $data['message'] ?? 'Success!',
            'duration' => $data['duration'] ?? 4000,
        ];

        $this->notifications[] = $notification;

        // Auto-remove after duration
        $this->dispatch('auto-remove-toast', [
            'id' => $notification['id'],
            'duration' => $notification['duration']
        ]);
    }

    public function removeNotification($id)
    {
        $this->notifications = array_filter($this->notifications, function($notification) use ($id) {
            return $notification['id'] !== $id;
        });
    }

    public function render()
    {
        return view('livewire.components.toast-notifications');
    }
}