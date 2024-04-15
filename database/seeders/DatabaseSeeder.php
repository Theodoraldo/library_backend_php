<?php

namespace Database\Seeders;

use App\Models\Genre;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            [
                'genre_name' => 'Action',
                'description' => 'Exciting and fast-paced content with intense scenes and thrilling plot twists.',
            ],
            [
                'genre_name' => 'Drama',
                'description' => 'Emotionally charged narratives focusing on interpersonal conflicts and character development.',
            ],

            [
                'genre_name' => 'Comedy',
                'description' => 'Humorous and light-hearted content designed to entertain and make the audience laugh.',
            ],

            [
                'genre_name' => 'Science Fiction',
                'description' => 'Speculative fiction exploring futuristic concepts, advanced technology, and otherworldly settings.',
            ],

            [
                'genre_name' => 'Mystery',
                'description' => 'Intriguing and suspenseful stories often involving the solving of a crime or uncovering hidden secrets.',
            ],

            [
                'genre_name' => 'Fantasy',
                'description' => 'Imaginative narratives set in fantastical worlds with magical elements and mythical creatures.',
            ],

            [
                'genre_name' => 'Romance',
                'description' => 'Love stories centering around romantic relationships and the complexities of human emotions.',
            ],

            [
                'genre_name' => 'Horror',
                'description' => 'Scary and suspenseful content aimed at evoking fear and unease in the audience.',
            ],

            [
                'genre_name' => 'Documentary',
                'description' => 'Factual and informative content based on real events, people, and subjects.',
            ],

            [
                'genre_name' => 'Adventure',
                'description' => 'Exciting and daring experiences in various settings.',
            ],

            [
                'genre_name' => 'Thriller',
                'description' => 'Suspenseful and intense plots to keep the audience on the edge of their seats.',
            ],

            [
                'genre_name' => 'Animation',
                'description' => 'Visual storytelling using animated characters and environments.',
            ],

            [
                'genre_name' => 'Historical',
                'description' => 'Set in the past, exploring historical events, figures, and cultures.',
            ],

            [
                'genre_name' => 'Western',
                'description' => 'Stories set in the American Old West, featuring cowboys and frontier life.',
            ],

            [
                'genre_name' => 'Graphic Novels/Comics',
                'description' => 'Visual storytelling through a combination of images and text.',
            ],

            [
                'genre_name' => 'Manga',
                'description' => 'Japanese comic books and graphic novels with a distinctive art style.',
            ],

            [
                'genre_name' => 'Poetry',
                'description' => 'Expressive and rhythmic literary works often exploring emotions and experiences.',
            ],
            [
                'genre_name' => 'Religious/Spiritual',
                'description' => 'Works centered around religious or spiritual themes and beliefs.',
            ],
            [
                'genre_name' => 'Business/Finance',
                'description' => 'Content related to business, finance, and economic principles.',
            ]
        ];

        foreach ($genres as $genre) {
            Genre::factory()->create($genre);
        }
    }
}
