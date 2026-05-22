<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'application_id' => $this->application_id,
            'date'           => $this->date,
            'time'           => $this->time,
            'interviewer'    => $this->interviewer,
            'type'           => $this->type,
            'status'         => $this->status,
            'application'    => new ApplicationResource($this->whenLoaded('application')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
