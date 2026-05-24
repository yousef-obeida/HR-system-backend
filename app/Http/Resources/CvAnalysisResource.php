<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'summary' => $this->summary,
            'skills' => $this->skills,
            'experience_years' => $this->experience_years,
            'score' => $this->score,
            'recommendation' => $this->recommendation,
        ];
    }
}
