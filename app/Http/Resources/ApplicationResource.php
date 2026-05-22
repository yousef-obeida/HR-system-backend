<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'candidate_id' => $this->candidate_id,
            'job_post_id'  => $this->job_post_id,
            'stage_id'     => $this->stage_id,
            'status'       => $this->status,
            'applied_at'   => $this->applied_at,
            'candidate'    => new CandidateResource($this->whenLoaded('candidate')),
            'job'          => new JobResource($this->whenLoaded('job')),
            'stage'        => $this->whenLoaded('stage', function () {
                return [
                    'id'    => $this->stage->id,
                    'name'  => $this->stage->name,
                    'order' => $this->stage->order,
                ];
            }),
            'interviews'   => InterviewResource::collection($this->whenLoaded('interviews')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
