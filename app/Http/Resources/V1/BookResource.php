<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "publishedDate" => $this->published_date,
            "availableCopies" => $this->available_copies,
            "coverImage" => $this->cover_image,
            "pages" => $this->pages,
            "notes" => $this->notes,
            "genre" => optional($this->genre)->exists ? new GenreResource($this->genre) : null,
            "author" => optional($this->author)->exists ? new AuthorResource($this->author) : null,
        ];
    }
}
