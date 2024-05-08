<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BorrowHistory;
use App\Models\Attendance;

class LibraryPatron extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'contact',
        'address',
        'city',
        'state',
        'location',
        'identity_card',
        'identity_no',
    ];

    public function changeEngagementStatus($status)
    {
        $this->engagement = $status;
        $this->save();
    }

    public function borrowHistories(): HasMany
    {
        return $this->hasMany(BorrowHistory::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
