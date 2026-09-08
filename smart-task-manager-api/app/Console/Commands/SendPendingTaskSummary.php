<?php

namespace App\Console\Commands;

use App\Mail\SendPendingTaskSummaryMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPendingTaskSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:pending-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily pending task summary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users=User::all();
        foreach($users as $user)
        {
            $tasks=$user->tasks()
            ->where('status','pending')
            ->get();

            if($tasks->isEmpty())
            {
                continue;
            }

            Mail::to($user->email)
            ->queue(new SendPendingTaskSummaryMail($user,$tasks));
        }
        $this->info("Pending task summary send successfully!");
}
}
