<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = Carbon::instance($this->faker->dateTimeBetween('-1 month', '+1 month'));
        $endTime = (clone $startTime)->addHours($this->faker->numberBetween(2, 4));
        
        return [
            'title' => 'Webinar BKN: ' . $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'live_token' => Str::random(8),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
