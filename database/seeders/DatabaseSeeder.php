<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Drink;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            DrinkSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Администратор',
            'email' => 'admin@test.kz',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Редактор',
            'email' => 'editor@test.kz',
            'role' => 'editor',
        ]);

        User::factory()->create([
            'name' => 'Пользователь',
            'email' => 'user@test.kz',
            'role' => 'user',
        ]);

        Drink::factory(40)
            ->recycle(Category::all())
            ->create();
    }
}
