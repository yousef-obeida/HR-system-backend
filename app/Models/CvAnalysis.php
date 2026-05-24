<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    protected $fillable = [
        'candidate_id',
        'summary',
        'skills',
        'experience_years',
        'score',
        'recommendation',
    ];

    protected $casts = [
        'skills' => 'array',
        'experience_years' => 'integer',
        'score' => 'integer',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
