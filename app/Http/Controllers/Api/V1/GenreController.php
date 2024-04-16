<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GenreResource;
use Illuminate\Http\Response;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function index()
    {
        try {
            $genres = GenreResource::collection(Genre::paginate());
            return response($genres, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $genre = new GenreResource(Genre::findOrFail($id));
            return response($genre, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
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
            return $this->handleException($e);
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
            return $this->handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            Genre::findOrFail($id)->delete();
            return response('Genre record deleted successfully !!!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function handleException(\Exception $e)
    {
        switch (true) {
            case $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException:
                $statusCode = Response::HTTP_NOT_FOUND;
                $customErrorMessage = 'Resource not found.';
                break;
            case $e instanceof \Illuminate\Validation\ValidationException:
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $customErrorMessage = 'Validation failed.';
                break;
            case $e instanceof \Illuminate\Database\QueryException:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                $customErrorMessage = 'Database error occurred.';
                break;
            case $e instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $customErrorMessage = 'Bad request.';
                break;
            case $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException:
                $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
                $customErrorMessage = 'Method not allowed.';
                break;
            case $e instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException:
                $statusCode = Response::HTTP_UNAUTHORIZED;
                $customErrorMessage = 'Unauthorized.';
                break;
            case $e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException:
                $statusCode = Response::HTTP_TOO_MANY_REQUESTS;
                $customErrorMessage = 'Too many requests.';
                break;
            case $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Illuminate\Database\Eloquent\MassAssignmentException ||
                $e instanceof \Illuminate\Validation\UnauthorizedException:
                $statusCode = Response::HTTP_FORBIDDEN;
                $customErrorMessage = 'Forbidden.';
                break;
            case $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                $statusCode = Response::HTTP_NOT_FOUND;
                $customErrorMessage = 'Resource not found.';
                break;
            case $e instanceof \Illuminate\Routing\Exceptions\UrlGenerationException:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                $customErrorMessage = 'URL generation error.';
                break;
            default:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                $customErrorMessage = 'An unexpected error occurred.';
                break;
        }

        return response(['error' => $customErrorMessage, 'code' => $statusCode], $statusCode);
    }
}
