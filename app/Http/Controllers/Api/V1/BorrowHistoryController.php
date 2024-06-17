<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\V1\BorrowHistoryResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\BorrowHistory;
use App\Models\Book;
use Carbon\Carbon;

class BorrowHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $borrowers = BorrowHistoryResource::collection(BorrowHistory::with('book', 'library_patron', 'user')
                ->when($request->has(['bsDate', 'beDate']), function ($borrowers) use ($request) {
                    $borrowers->whereBetween('borrow_date', [$request->bsDate, $request->beDate]);
                })->when($request->has(['rsDate', 'reDate']), function ($borrowers) use ($request) {
                    $borrowers->whereBetween('return_date', [$request->startDate, $request->endDate]);
                })->when($request->rdate === 'null', function ($borrowers) {
                    $borrowers->whereNull('return_date');
                })
                ->orderBy('created_at', 'desc')
                ->get());
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
            $borrowHistory = BorrowHistory::findOrFail($id);
            $borrowHistory->update($request->all());

            if ($request->instore === "yes") {
                $borrowHistory->book->increaseAvailableCopies($request->borrowed_copies);
            }

            return response()->json(['status' => Response::HTTP_OK, 'message' => 'Borrowed book returned successfully !!!'], Response::HTTP_OK);
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

    public function getMostBooksBorrowed()
    {
        try {
            $result = BorrowHistory::select('book_id', DB::raw('SUM(borrowed_copies) as COUNT_Borrowed'))
                ->groupBy('book_id')
                ->orderBy('COUNT_Borrowed', 'desc')
                ->with(['book' => function ($query) {
                    $query->select('id', 'title');
                }])
                ->limit(5)
                ->get();

            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function getMonthlyBooksBorrowed()
    {
        try {
            $currentYear = Carbon::now()->year;

            $borrowedBooksByMonth = BorrowHistory::selectRaw('MONTH(borrow_date) as month, SUM(borrowed_copies) as total_borrowed')
                ->whereYear('borrow_date', $currentYear)
                ->groupByRaw('MONTH(borrow_date)')
                ->orderByRaw('MONTH(borrow_date)')
                ->get()
                ->map(function ($record) {
                    $record->month = date('F', mktime(0, 0, 0, $record->month, 10));
                    return $record;
                });
            return response()->json($borrowedBooksByMonth, Response::HTTP_OK);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
