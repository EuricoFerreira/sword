<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationTaskCreated implements ShouldQueue
{
    use InteractsWithQueue;
   
    /**
     * Handle the event.
     *
     * @param  TaskCreated  $event
     * @return void
     */

    public function handle(TaskCreated $event)
    {
        echo "The Tech ".$event->task->user->name." perform the task ". $event->task->summary. " on date ". $event->task->completed_at;

    }
}
