<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // panggil seeder demo Power BI
        $this->call([
            PowerBiDemoSeeder::class,
        ]);
    }
}
