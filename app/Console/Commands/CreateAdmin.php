<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create a new admin account';

    public function handle(): int
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email');

        if (Admin::where('email', $email)->exists()) {
            $this->error('An admin with that email already exists.');
            return self::FAILURE;
        }

        $password = $this->secret('Password (min 8 characters)');
        $confirm = $this->secret('Confirm password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $role = $this->choice('Role', ['super_admin', 'platform_admin'], 0);

        $platform = Platform::first();

        Admin::create([
            'platform_id' => $platform->id,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => true,
        ]);

        $this->info("Admin '{$name}' created successfully.");
        return self::SUCCESS;
    }
}