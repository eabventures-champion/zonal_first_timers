<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyncBringersToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bringers = \App\Models\Bringer::whereNull('user_id')->get();
        $this->command->info("Found " . $bringers->count() . " bringers to sync.");

        foreach ($bringers as $bringer) {
            if (!$bringer->contact) {
                $this->command->error("Skipping Bringer: " . $bringer->name . " (No contact)");
                continue;
            }

            // Check if user already exists by phone
            $user = \App\Models\User::where('phone', $bringer->contact)->first();

            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => $bringer->name,
                    'phone' => $bringer->contact,
                    'email' => $bringer->contact . '@zonal.com',
                    'password' => \Illuminate\Support\Facades\Hash::make($bringer->contact),
                    'church_id' => $bringer->church_id,
                ]);
                $this->command->info("Created user for: " . $bringer->name);
            }

            // Ensure they have the Bringer role
            if (!$user->hasRole('Bringer')) {
                $user->assignRole('Bringer');
            }

            // Link bringer to user
            $bringer->update(['user_id' => $user->id]);
        }
    }
}
