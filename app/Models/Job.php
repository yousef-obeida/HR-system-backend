<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'job_posts';

    protected $fillable = [
        'title',
        'description',
        'requirments',
        'status',
        'Location',
        'salary',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
