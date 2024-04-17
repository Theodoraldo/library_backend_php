<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\V1\BorrowHistoryResource;
use Illuminate\Support\Facades\Validator;
use App\Models\BorrowHistory;

class BorrowHistoryController extends Controller
{
    public function index()
    {
        try {
            $borrowers = BorrowHistoryResource::collection(BorrowHistory::paginate());
            return response($borrowers, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $borrower = new BorrowHistoryResource(BorrowHistory::findOrFail($id));
            return response($borrower, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrow_date' => 'required',
            'book_id' => 'required',
            'library_patron_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            BorrowHistory::create([
                'borrow_date' => $request->borrow_date,
                'book_id' =>  $request->book_id,
                'library_patron_id' => $request->library_patron_id,
                'user_id' => $request->user_id,
            ]);
            return response('Borrowed book issued successfully !!!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrow_date' => $request->borrow_date,
            'book_id' =>  $request->book_id,
            'library_patron_id' => $request->library_patron_id,
            'user_id' => $request->user_id,
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            BorrowHistory::findOrFail($request->id)->update($request->all());
            return response('Borrowed book returned successfully !!!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function destroy(String $id)
    {
        try {
            BorrowHistory::findOrFail($id)->delete();
            return response('Borrowed book record deleted successfully !!!', Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
