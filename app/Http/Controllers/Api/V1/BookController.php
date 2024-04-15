<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\V1\BookResource;

class BookController extends Controller
{
    public function index()
    {
        return BookResource::collection(Book::with('author', 'genre')->get());
    }

    public function show(String $id)
    {
        return new BookResource(Book::findOrFail($id));
    }
}
