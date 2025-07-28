<?php

namespace Database\Factories;

use App\Models\{ Thread, User, ThreadStatus };
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
         return [
         'user_id' => User::first()->id,
         'title' => $this->faker->sentence(5),
         'body' => $this->faker->paragraph(2),
         'status' => ThreadStatus::first()->id,
       ];
    }   
}
