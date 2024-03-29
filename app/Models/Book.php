<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Author;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'genre_id',
        'notes',
        'published_date',
        'available_copies',
        'cover_image',
        'pages',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function borrow_history(): HasMany
    {
        return $this->hasMany(BorrowHistory::class);
    }
}
