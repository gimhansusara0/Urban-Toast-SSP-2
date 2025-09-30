<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only seed the admin as per your requirement
        $this->call([
            AdminSeeder::class,
        ]);
    }
}
