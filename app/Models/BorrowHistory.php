<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LibraryPatron;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BorrowHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'borrowed_copies',
        'library_patron_id',
        'user_id',
        'instore',
        'comment',
        'borrow_date',
        'return_date',
        'book_state',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($borrowHistory) {
            $borrowHistory->book->decreaseAvailableCopies($borrowHistory->borrowed_copies);
        });

        static::deleted(function ($borrowHistory) {
            $borrowHistory->book->increaseAvailableCopies($borrowHistory->borrowed_copies);
        });

        // // static::updated(function ($borrowHistory) {
        // //     if ($borrowHistory->instore('yes')) {
        // //         $borrowHistory->book->increaseAvailableCopies($borrowHistory->borrowed_copies);
        // //     }
        // // });

        // static::updated(function ($borrowHistory) {
        //     Log::info('Updated event fired for borrowHistory id: ' . $borrowHistory->id);

        //     if ($borrowHistory->instore === 'yes') {
        //         Log::info('Increasing available copies for book id: ' . $borrowHistory->book->id);
        //         Log::info('Increasing available copies by: ' . $borrowHistory->borrowed_copies);
        //         $borrowHistory->book->increaseAvailableCopies($borrowHistory->borrowed_copies);
        //     }
        // });
    }

    public function library_patron(): BelongsTo
    {
        return $this->belongsTo(LibraryPatron::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
