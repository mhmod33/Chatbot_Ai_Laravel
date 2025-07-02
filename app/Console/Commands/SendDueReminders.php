<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use App\Mail\ReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due reminders via email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $dueReminders = Reminder::where('sent', false)
            ->where('remind_at', '<=', $now)
            ->get();
        foreach ($dueReminders as $reminder) {
            Mail::to($reminder->email)->send(new ReminderMail($reminder));
            $reminder->sent = true;
            $reminder->save();
            $this->info('Sent reminder to ' . $reminder->email . ' for task: ' . $reminder->task);
        }
        $this->info('Done.');
    }
}
