<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Models\Attendance;
use App\Http\Resources\V1\AttendanceResource;
use App\Models\LibraryPatron;
use App\Services\V1\AttendanceFilterQuery;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = new AttendanceFilterQuery();
            $filterQuery = $query->transform($request);
            if (count($filterQuery) == 0) {
                $attendances = AttendanceResource::collection(Attendance::get());
                return response()->json($attendances, Response::HTTP_OK);
            } else {
                $attendances = AttendanceResource::collection(Attendance::where($filterQuery)->get());
                return response()->json($attendances, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $attendance = new AttendanceResource(Attendance::findOrFail($id));
            return response()->json($attendance, Response::HTTP_OK);
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
            Attendance::create($request->all());
            $patron = LibraryPatron::findOrFail($request->library_patron_id);
            $patron->changeEngagementStatus("yes");
            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'Patron checked-in successfully !!!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_out' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $attendance = Attendance::findOrFail($request->id);
            $attendance->update($request->all());

            $patron = LibraryPatron::findOrFail($attendance->library_patron_id);
            $patron->changeEngagementStatus("no");
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
