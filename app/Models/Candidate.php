<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        "id",
        'full_name',
        'email',
        'phone_number',
        'cv_path',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function analysis()
    {
        return $this->hasOne(CvAnalysis::class);
    }
}
