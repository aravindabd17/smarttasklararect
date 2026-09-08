<?php
namespace App\Pipelines;

use Closure;

class StatusFilter
{
    public function handle($query,Closure $next)
    {
        if(request()->filled('status'))
        {
            $query->where("status","like","%".request('status')."%");
        }
        return $next($query);
    }
}