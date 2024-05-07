<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AuthorResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Author;
use App\Exceptions\ExceptionHandler;

class AuthorController extends Controller
{
    public function index()
    {
        try {
            $authors = AuthorResource::collection(Author::get());
            return response($authors, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $author = new AuthorResource(Author::findOrFail($id));
            return response($author, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:authors',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $newImagePath = date('Y-m-d_H-i-s') . '.' . $image->getClientOriginalExtension();
                Storage::disk('images')->put($newImagePath, file_get_contents($image));
            } else {
                $defaultImagePath = 'default.png';
                $newImagePath = date('Y-m-d_H-i-s') . '.png';

                Storage::disk('images')->copy($defaultImagePath, $newImagePath);
            }
            Author::create([
                'firstname' => $request->firstname,
                'lastname' =>  $request->lastname,
                'email' => $request->email,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'profile_picture' => $newImagePath,
            ]);
            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'Author created successfully !!!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $newImagePath = date('Y-m-d_H-i-s') . '.' . $image->getClientOriginalExtension();
                Storage::disk('images')->put($newImagePath, file_get_contents($image));
            } else {
                $newImagePath = $request->file('profile_picture');
            }
            Author::findOrFail($request->id)->update($request->all());
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Author updated successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            Author::findOrFail($id)->delete();
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Author deleted successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
