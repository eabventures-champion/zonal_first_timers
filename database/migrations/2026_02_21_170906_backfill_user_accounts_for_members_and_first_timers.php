<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->backfill('first_timers', 'full_name', 'primary_contact', 'email');
        $this->backfill('members', 'full_name', 'primary_contact', 'email');
    }

    private function backfill($table, $nameField, $phoneField, $emailField)
    {
        $records = DB::table($table)->whereNull('user_id')->get();
        foreach ($records as $record) {
            $email = $record->$emailField ?? ($record->$phoneField . '@church.com');

            // Avoid duplicate email or phone if already exists
            $userId = DB::table('users')->where('email', $email)->orWhere('phone', $record->$phoneField)->value('id');

            if (!$userId) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $record->$nameField,
                    'email' => $email,
                    'phone' => $record->$phoneField,
                    'password' => Hash::make($record->$phoneField),
                    'church_id' => $record->church_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Manually assign Member role (using Spatie directly in DB for speed/safety in migration)
                $roleId = DB::table('roles')->where('name', 'Member')->value('id');
                if ($roleId) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleId,
                        'model_type' => 'App\Models\User',
                        'model_id' => $userId,
                    ]);
                }
            }

            DB::table($table)->where('id', $record->id)->update(['user_id' => $userId]);
        }
    }

    public function down(): void
    {
        // No easy way to undo user creation without potentially deleting manual users
    }
};
