<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LibraryPatron;
use App\Models\Book;
use App\Models\User;

class BorrowHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'library_patron_id',
        'user_id',
        'instore',
        'comment',
        'borrow_date',
        'return_date',
        'book_state',
    ];

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
