<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Console\Command;

class ChangeAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-admin-password';

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

        $admin = Admin::where('username', $username)->first();

        if ($admin == null) {
            $this->info("A admin not found with this username");
            return;
        }

        $this->info("new password: $newPassword");

        if (!$this->confirm("Are you sure change password to this")) {
            $this->info("changing password is cancelled.");
            return;
        }

        $admin->password = $newPassword;

        if (!$admin->save()) {
            $this->info("An error is occurred");
            return;
        }

        $this->info("Password is changed to $newPassword");
    }
}
