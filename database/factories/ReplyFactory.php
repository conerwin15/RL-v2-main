<?php

namespace Database\Factories;

use App\Models\{ Reply, Thread, User };

use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

        'user_id'   => User::first()->id,
        'thread_id' => Thread::first()->id,
        'body'      => $this->faker->paragraph(2)

        ];
    }
}
