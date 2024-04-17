<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bookId' => $this->book_id,
            'patronId' => $this->patron_id,
            'userId' => $this->user_id,
            'borrowDate' => $this->borrow_date,
            'returnDate' => $this->return_date,
            'bookState' => $this->book_state,
            'instore' => $this->instore,
            'comment' => $this->comment,
        ];
    }
}
