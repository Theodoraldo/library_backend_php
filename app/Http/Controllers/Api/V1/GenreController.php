<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GenreResource;
use Illuminate\Http\Response;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ExceptionHandler;

class GenreController extends Controller
{
    public function index()
    {
        try {
            $genres = GenreResource::collection(Genre::orderBy('genre_name')->get());
            return response($genres, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $genre = new GenreResource(Genre::findOrFail($id));
            return response($genre, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            Genre::create($request->all());
            return response('New genre created successfully !!!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            Genre::findOrFail($request->id)->update($request->all());
            return response('Genre record updated successfully !!!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            Genre::findOrFail($id)->delete();
            return response('Genre record deleted successfully !!!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
