<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('published_date');
            $table->integer('available_copies');
            $table->string('cover_image')->nullable();
            $table->integer('pages');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('author_id')->constrained();
            $table->unsignedBigInteger('genre_id')->constrained();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
