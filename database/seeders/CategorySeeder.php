<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('categories')->insert([
            [
                'name' => 'Кофе',
                'description' => 'Кофейные напитки на основе эспрессо и свежесваренного кофе.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Чай',
                'description' => 'Чёрный и зелёный листовой чай.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Холодные напитки',
                'description' => 'Освежающие напитки со льдом.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Какао и десерты',
                'description' => 'Сладкие горячие напитки и угощения.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
