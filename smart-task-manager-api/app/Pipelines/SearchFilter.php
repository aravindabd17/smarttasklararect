<?php
namespace App\Pipelines;

use Closure;

class SearchFilter
{
    public function handle($query,Closure $next)
    {
        if(request()->filled("search"))
        {
            $query->where("title","like","%".request('search')."%");
        }
        return $next($query);
    }
}