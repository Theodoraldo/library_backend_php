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
            'borrowedCopies' => $this->borrowed_copies,
            'borrowDate' => $this->borrow_date,
            'returnDate' => $this->return_date,
            'bookState' => $this->book_state,
            'instore' => $this->instore,
            'comment' => $this->comment,
            'book' => new BookResource($this->whenLoaded('book')),
            'patron' => new LibraryPatronResource($this->whenLoaded('library_patron')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
