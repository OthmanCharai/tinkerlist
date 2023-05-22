<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'date' => Carbon::now()->addYears($this->faker->randomDigit()+1),
            'time' => $this->faker->time,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
            'location' => $this->faker->unique()->city, //todo: need existing countries
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
