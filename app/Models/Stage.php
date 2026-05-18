<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = ['name', 'order'];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
