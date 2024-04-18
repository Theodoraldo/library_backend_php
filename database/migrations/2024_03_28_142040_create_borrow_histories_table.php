<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrow_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('borrowed_copies');
            $table->date('borrow_date');
            $table->date('return_date')->nullable();
            $table->string('book_state')->default('good');
            $table->string('instore')->default('no');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('book_id')->constrained();
            $table->unsignedBigInteger('library_patron_id')->constrained();
            $table->unsignedBigInteger('user_id')->constrained();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('library_patron_id')->references('id')->on('library_patrons');
            $table->foreign('book_id')->references('id')->on('books');
        });

        DB::statement("ALTER TABLE borrow_histories ADD CONSTRAINT check_book_state CHECK (book_state IN ('good', 'bad', 'average', 'torn'))");
        DB::statement("ALTER TABLE borrow_histories ADD CONSTRAINT check_instore CHECK (instore IN ('yes', 'no'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_histories');
    }
};
