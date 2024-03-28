<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
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
        'country',
        'profile_picture'
    ];
}


$table->string('country');
$table->string('profile_picture')->nullable();
