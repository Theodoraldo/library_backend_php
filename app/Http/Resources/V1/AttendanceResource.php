<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\LibraryPatronResource;


class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'checkIn' => $this->check_in,
            'checkOut' => $this->check_out,
            'patron' => optional($this->library_patron)->exists ? new LibraryPatronResource($this->library_patron) : null,
        ];
    }
}
