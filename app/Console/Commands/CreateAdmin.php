<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Creating admin with CLI';

    public function handle()
    {
        $username = $this->ask('enter admin username');
        $password = $this->ask('enter admin password');

        $this->info("username: $username");
        $this->info("password: $password");

        if (!$this->confirm("Are you sure create admin with this data? ")) {

            $this->info('creating admin is canceled');
            return;
        }

        try {
            $admin = Admin::create([
                "username" => $username,
                "password" => $password,
            ]);
        } catch (\Exception $e) {

            $this->info("\nAdmin already is created");
            return;
        }

        $this->info($admin->makeVisible("password")->toJson(JSON_PRETTY_PRINT));

        $this->info("\nAdmin is created :)");

    }
}
