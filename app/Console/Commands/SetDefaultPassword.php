<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetDefaultPassword extends Command
{
    protected $signature = 'users:default-password
                            {password=123456 : The password to set}
                            {--role= : Only touch users with this role}
                            {--imported : Only touch accounts imported from the staff sheet}';

    protected $description = 'Set a shared default password on user accounts';

    public function handle(): int
    {
        $password = (string) $this->argument('password');

        $query = User::query();

        if ($role = $this->option('role')) {
            $query->where('role', $role);
        }

        if ($this->option('imported')) {
            $query->whereNotNull('staff_id');
        }

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->warn('No matching users.');

            return self::SUCCESS;
        }

        // Hash once and write it to every row: a mass update skips the model's
        // "hashed" cast, so the value must already be hashed here.
        $query->update(['password' => Hash::make($password)]);

        $this->info("Set the password on {$count} account(s) to \"{$password}\".");
        $this->warn('Everyone who can reach the login page can now sign in with it — change it before this goes anywhere public.');

        return self::SUCCESS;
    }
}
