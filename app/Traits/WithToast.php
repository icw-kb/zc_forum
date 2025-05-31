<?php
// app/Traits/WithToast.php

namespace App\Traits;

trait WithToast
{
    public function toast($message, $type = 'success', $duration = 4000)
    {
        $this->dispatch('show-toast', [
            'type' => $type,
            'message' => $message,
            'duration' => $duration
        ]);
    }

    public function toastSuccess($message, $duration = 4000)
    {
        $this->toast($message, 'success', $duration);
    }

    public function toastError($message, $duration = 6000)
    {
        $this->toast($message, 'error', $duration);
    }

    public function toastWarning($message, $duration = 5000)
    {
        $this->toast($message, 'warning', $duration);
    }

    public function toastInfo($message, $duration = 4000)
    {
        $this->toast($message, 'info', $duration);
    }
}