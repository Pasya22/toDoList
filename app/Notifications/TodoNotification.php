<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TodoNotification extends Notification
{
    use Queueable;

    protected $todo;

    public function __construct($todo)
    {
        $this->todo = $todo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new todo has been added or updated.')
                    ->line('Title: ' . $this->todo->title)
                    ->action('View Todo', url('/todos'))
                    ->line('Thank you for using our application!');
    }
}
