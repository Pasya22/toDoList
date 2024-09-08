<?php
// app/Events/TodoUpdated.php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function broadcastOn()
    {
        return new Channel('todos');
    }
}
