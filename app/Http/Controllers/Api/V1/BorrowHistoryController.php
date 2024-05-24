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
            $borrowers = BorrowHistoryResource::collection(BorrowHistory::with('book', 'library_patron', 'user')->get());
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
            'borrowed_copies' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $book = Book::findOrFail($request->book_id);
            if (intval($request->borrowed_copies) > intval($book->available_copies)) {
                return response()->json(['status' => Response::HTTP_UNPROCESSABLE_ENTITY, 'message' => 'Quantity cannot be greater than stock quantity !!!'], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                BorrowHistory::create($request->all());
                $book->decreaseAvailableCopies($request->borrowed_copies);
                return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'Borrowed book issued successfully !!!'], Response::HTTP_CREATED);
            }
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
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Borrowed book record updated successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }


    public function destroy(String $id)
    {
        try {
            BorrowHistory::findOrFail($id)->delete();
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Borrowed book record deleted successfully !!!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
