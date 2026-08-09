<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        Platform::updateOrCreate(
            ['slug' => 'poyenn'],
            [
                'name' => 'Poyenn',
                'slug' => 'poyenn',
                'domain' => 'poyenn.com',
                'theme_color' => '#1A2F5A',
                'tagline' => 'Quality Electronics, Delivered.',
                'description' => 'Poyenn is your trusted online destination for quality electronics in Nigeria. We deliver premium products from top brands straight to your doorstep.',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Poyenn platform seeded successfully.');
    }
}