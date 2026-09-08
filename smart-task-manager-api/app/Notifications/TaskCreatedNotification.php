<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New task created")
            ->greeting("Hello ".$notifiable->name."!")
            ->line("New task has been created successfully")
            ->line('Task name : '.$this->task->title)
            ->line('Status : '.ucfirst($this->task->status))
            ->action('View Tasks', url('/'))
            ->line('Thank you for using Smart manager task!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "task title"=>$this->task->title,
            'task id'=>$this->task->id,
            "status"=>$this->task->status,
            "description"=>$this->task->description
        ];
    }
}
