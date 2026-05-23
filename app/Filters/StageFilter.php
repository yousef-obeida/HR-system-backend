<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StageFilter
{
    public static function apply(Builder $query, Request $request): Builder
    {
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return $query;
    }
}
