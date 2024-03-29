<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'library_patron_id',
        'check_in',
        'check_out',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
