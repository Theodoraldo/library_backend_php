<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Models\Attendance;
use App\Http\Resources\V1\AttendanceResource;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index()
    {
        try {
            $attendances = AttendanceResource::collection(Attendance::get());
            return response($attendances, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $attendance = new AttendanceResource(Attendance::findOrFail($id));
            return response($attendance, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in' => 'required',
            'library_patron_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            Attendance::create([
                'check_in' => $request->check_in,
                'check_out' =>  $request->check_out,
                'library_patron_id' => $request->library_patron_id,
            ]);
            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'Patron checked-in successfully !!!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in' => 'required',
            'library_patron_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            Attendance::findOrFail($request->id)->update($request->all());
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Patron checked-out successfully  !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            Attendance::findOrFail($id)->delete();
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Record deleted successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
