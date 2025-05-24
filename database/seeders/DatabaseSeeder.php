<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call(RoleAndPermissionSeeder::class);
            $this->call(UserSeeder::class);
            $this->call(CurrencySeeder::class);
            $this->call(DefaultStatusesSeeder::class);
            // $this->call(TaskSeeder::class);
    }
}
