<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\V1\BookResource;
use Illuminate\Http\Response;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        try {
            $books = BookResource::collection(Book::get());
            return response()->json($books, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $book = new BookResource(Book::findOrFail($id));
            return response()->json($book, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author_id' => 'required',
            'genre_id' => 'required',
            'available_copies' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $newImagePath = date('Y-m-d_H-i-s') . '.' . $image->getClientOriginalExtension();
                Storage::disk('images')->put($newImagePath, file_get_contents($image));
            } else {
                $defaultImagePath = 'defaultcover.png';
                $newImagePath = date('Y-m-d_H-i-s') . '.png';

                Storage::disk('images')->copy($defaultImagePath, $newImagePath);
            }

            Book::create([
                'title' => $request->title,
                'author_id' => $request->author_id,
                'genre_id' => $request->genre_id,
                'notes' => $request->notes,
                'published_date' => $request->published_date,
                'available_copies' => $request->available_copies,
                'cover_image' => $newImagePath,
                'pages' => $request->pages,
            ]);
            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'New book created successfully !!!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author_id' => 'required',
            'genre_id' => 'required',
            'available_copies' => 'required',
            'cover_image' => 'image|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($request->cover_image) {
                $getData = Book::findOrFail($id);
                Storage::delete($getData->cover_image);
                $image = $request->file('cover_image');
                $newImagePath = date('Y-m-d_H-i-s') . '.' . $image->getClientOriginalExtension();
                Storage::disk('images')->put($newImagePath, file_get_contents($image));

                Book::findOrFail($id)->update([
                    'title' => $request->title,
                    'author_id' => $request->author_id,
                    'genre_id' => $request->genre_id,
                    'notes' => $request->notes,
                    'published_date' => $request->published_date,
                    'available_copies' => $request->available_copies,
                    'cover_image' => $newImagePath,
                    'pages' => $request->pages,
                ]);
            } else {

                Book::findOrFail($id)->update($request->all());
            }
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Book record updated successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            Book::findOrFail($id)->delete();
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Book record deleted successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
