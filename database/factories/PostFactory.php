<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->company,
            'slug' => $this->faker->address,
            'short_description' => $this->faker->text(150),
            'content' => $this->faker->paragraphs(5, true),
            'image' => $this->faker->imageUrl(800, 600, 'business'),
            'author_id' => \App\Models\User::factory(), // hoặc random số
            'status' => $this->faker->randomElement(['draft', 'published']),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];

    }
}
