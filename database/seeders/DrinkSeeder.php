<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrinkSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $categoryIds = DB::table('categories')->pluck('id', 'name');
        $coffee = $categoryIds['Кофе'];
        $tea = $categoryIds['Чай'];
        $cold = $categoryIds['Холодные напитки'];
        $sweets = $categoryIds['Какао и десерты'];

        $drinks = [
            [$coffee, 'Капучино', 1400.00, 250, 'Эспрессо с горячим молоком и нежной молочной пеной.', true, 120],
            [$coffee, 'Латте', 1600.00, 350, 'Мягкий кофейный напиток с молоком и тонким слоем пены.', true, 180],
            [$coffee, 'Американо', 1100.00, 250, 'Эспрессо, разбавленный горячей водой.', true, 15],
            [$coffee, 'Эспрессо', 900.00, 40, 'Насыщенный кофе с плотным вкусом и золотистой крема.', true, 5],
            [$coffee, 'Флэт уайт', 1700.00, 200, 'Двойной эспрессо с бархатистым тонким слоем молока.', true, 120],
            [$tea, 'Чёрный чай', 700.00, 300, 'Крепкий листовой чёрный чай.', true, 0],
            [$tea, 'Зелёный чай', 750.00, 300, 'Ароматный зелёный чай с мягким вкусом.', true, 0],
            [$cold, 'Домашний лимонад', 1300.00, 400, 'Освежающий лимонад с лимоном и мятой.', true, null],
            [$cold, 'Кофейный фраппе', 1800.00, 350, 'Холодный кофейный напиток со льдом и молоком.', true, 220],
            [$sweets, 'Какао', 1450.00, 300, 'Горячее какао на молоке.', true, 230],
            [$sweets, 'Горячий шоколад', 1900.00, 250, 'Густой шоколадный напиток.', true, null],
        ];

        DB::table('drinks')->insert(array_map(
            fn (array $drink): array => [
                'category_id' => $drink[0],
                'title' => $drink[1],
                'price' => $drink[2],
                'volume' => $drink[3],
                'description' => $drink[4],
                'in_stock' => $drink[5],
                'calories' => $drink[6],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $drinks,
        ));
    }
}
