<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status',
    ];
}
