<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function interviews()
{
    return $this->hasMany(Interview::class);
}

}

