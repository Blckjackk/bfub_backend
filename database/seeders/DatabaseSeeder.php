<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CabangLombaSeeder::class,
            PesertaSeeder::class,
            TokenSeeder::class,
            SoalSeeder::class,
            JawabanSeeder::class,
            SoalEssaySeeder::class,
            JawabanEssaySeeder::class,
            SoalIsianSingkatSeeder::class,
            JawabanIsianSingkatSeeder::class,
        ]);
    }
}
