<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InterviewFilter
{
    public static function apply(Builder $query, Request $request): Builder
    {

        if ($request->has('interviewer')) {
            $query->where('interviewer', 'like', '%' . $request->input('interviewer') . '%');
        }

        if ($request->has('start_date')) {
            $query->whereDate('date', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $query->whereDate('date', '<=', $request->input('end_date'));
        }

        return $query;
    }
}
