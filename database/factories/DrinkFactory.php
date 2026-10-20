<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Drink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Drink>
 */
class DrinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->randomElement([
                'Капучино',
                'Латте',
                'Раф',
                'Мокко',
                'Флэт уайт',
                'Американо',
                'Чай',
                'Какао',
                'Лимонад',
                'Смузи',
            ]).' '.fake()->randomElement([
                'классический',
                'ванильный',
                'карамельный',
                'кокосовый',
                'имбирный',
                'сезонный',
            ]),
            'price' => fake()->randomFloat(2, 500, 2500),
            'volume' => fake()->randomElement([200, 250, 300, 350, 400, 500]),
            'description' => fake()->realText(150),
            'in_stock' => fake()->boolean(80),
            'calories' => fake()->optional(0.8)->numberBetween(5, 450),
        ];
    }
}
