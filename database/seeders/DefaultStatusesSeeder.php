<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class DefaultStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try
        {
            Status::insert([
                [
                    'uuid' => fake()->uuid(),
                    'name' => 'todo',
                    'flag' => 'task',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'uuid' => fake()->uuid(),
                    'name' => 'in_progress',
                    'flag' => 'task',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'uuid' => fake()->uuid(),
                    'name' => 'in_review',
                    'flag' => 'task',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'uuid' => fake()->uuid(),
                    'name' => 'completed',
                    'flag' => 'task',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
        DB::commit();
    }
}
