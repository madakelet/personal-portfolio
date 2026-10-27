<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. The owner account comes from
     * `php artisan make:owner`, not from here.
     */
    public function run(): void
    {
        $this->call([
            EnglishTextSeeder::class,
            EnglishProjectSeeder::class,
        ]);
    }
}
