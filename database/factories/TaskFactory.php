<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Status;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{

    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fetch status IDs
        $statusIds = Status::where('flag', 'task')->pluck('id')->toArray();

        return [
            'uuid' => $this->faker->uuid(),
            'project_id' => $this->faker->numberBetween(1, 4),
            'created_by' => 2,
            'stage_id' => $this->faker->numberBetween(1, 4),
            'task_code' => 'PX-' . $this->faker->numberBetween(10, 100),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->realText(250),
            'status_id' => $statusIds[array_rand($statusIds)],
            'start_date' => Carbon::now()->subDays($this->faker->numberBetween(1, 4)),
            'due_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 4)),
            'estimate_time' => Carbon::now()->addSeconds($this->faker->numberBetween(1800, 18000)),
            'order_number' => $this->faker->numberBetween(0, 10),
        ];
    }
}