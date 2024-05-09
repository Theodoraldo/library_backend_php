<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\LibraryPatronResource;
use DateTime;


class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'checkOut' => $this->check_out,
            'checkInDate' => (new DateTime($this->check_in))->format('Y-m-d'),
            'checkInTime' => (new DateTime($this->check_in))->format('H:i:s'),
            'patron' => optional($this->library_patron)->exists ? new LibraryPatronResource($this->library_patron) : null,
        ];
    }
}
