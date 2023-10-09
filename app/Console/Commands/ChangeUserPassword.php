<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask("enter the username");
        $newPassword = $this->ask("enter the new password");

        $user = User::where('username', $username)->first();

        if ($user == null) {
            $this->info("A user not found with this username");
            return;
        }

        $this->info("new password: $newPassword");

        if (!$this->confirm("Are you sure change password to this")) {
            $this->info("changing password is cancelled.");
            return;
        }

        $user->password = $newPassword;

        if (!$user->save()) {
            $this->info("An error is occurred");
            return;
        }

        $this->info("Password is changed to $newPassword");

    }
}
