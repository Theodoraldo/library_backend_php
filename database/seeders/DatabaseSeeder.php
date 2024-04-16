<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\GenreFactory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $genreFactory = new GenreFactory();
        $genreFactory->createGenres();
    }
}
