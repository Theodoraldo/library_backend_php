<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LibraryPatronResource;
use App\Models\LibraryPatron;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ExceptionHandler;

class LibraryPatronController extends Controller
{
    public function index()
    {
        try {
            $patrons = LibraryPatronResource::collection(LibraryPatron::paginate());
            return response($patrons, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $patron = new LibraryPatronResource(LibraryPatron::findOrFail($id));
            return response($patron, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:library_patrons',
            'contact' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'location' => 'required',
            'identity_card' => 'required',
            'identity_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            LibraryPatron::create($request->all());
            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'Library Patron created successfully !!!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'location' => 'required',
            'identity_card' => 'required',
            'identity_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            LibraryPatron::findOrFail($request->id)->update($request->all());
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Library Patron record updated successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            LibraryPatron::findOrFail($id)->delete();
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Patron deleted successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
