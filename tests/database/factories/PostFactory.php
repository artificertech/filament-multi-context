<?php

namespace Artificertech\FilamentMultiContext\Tests\Database\Factories;

use Artificertech\FilamentMultiContext\Tests\App\Models\Post;
use Artificertech\FilamentMultiContext\Tests\App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->title(),
            'body' => fake()->paragraphs(asText: true),
        ];
    }
}
