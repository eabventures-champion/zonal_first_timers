<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FirstTimer;
use App\Models\User;

class LinkFirstTimerUsers extends Command
{
    protected $signature = 'app:link-first-timer-users';
    protected $description = 'Backfill user_id on first_timers by matching primary_contact to user phone';

    public function handle(): int
    {
        $linked = 0;

        $firstTimers = FirstTimer::whereNull('user_id')->get();

        foreach ($firstTimers as $ft) {
            $user = User::where('phone', $ft->primary_contact)->first();
            if ($user) {
                $ft->user_id = $user->id;
                $ft->save();
                $this->info("Linked: {$ft->full_name} (FT#{$ft->id}) -> User#{$user->id}");
                $linked++;
            } else {
                $this->warn("No user found for: {$ft->full_name} ({$ft->primary_contact})");
            }
        }

        $this->info("Done. Linked {$linked} records.");

        return Command::SUCCESS;
    }
}
