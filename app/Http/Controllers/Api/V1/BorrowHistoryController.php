<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\V1\BorrowHistoryResource;
use Illuminate\Support\Facades\Validator;
use App\Models\BorrowHistory;
use App\Models\Book;

class BorrowHistoryController extends Controller
{
    public function index()
    {
        try {
            $borrowers = BorrowHistoryResource::collection(BorrowHistory::with('book', 'library_patron', 'user')->paginate());
            return response($borrowers, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function show(String $id)
    {
        try {
            $borrower = new BorrowHistoryResource(BorrowHistory::with('book', 'library_patron', 'user')->findOrFail($id));
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
            BorrowHistory::create($request->all());
            $book = Book::findOrFail($request->book_id);
            $book->decreaseAvailableCopies($request->borrowed_copies);
            return response('Borrowed book issued successfully !!!', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request, String $id)
    {
        try {
            BorrowHistory::findOrFail($id)->update($request->all());
            $updatedObj = Book::findOrFail($request->book_id);

            if ($request->instore === "yes") {
                $updatedObj->increaseAvailableCopies($request->borrowed_copies);
            }
            return response('Borrowed book record updated successfully !!!', Response::HTTP_OK);
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
