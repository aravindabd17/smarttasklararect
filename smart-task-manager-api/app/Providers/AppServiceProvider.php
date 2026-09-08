<?php

namespace App\Providers;

use Anthropic\Client;
use App\Models\Task;
use App\Models\User;
use App\Observers\TaskObserver;
use App\Policies\TaskPolicy;
use App\Repositories\Contract\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class,TaskRepository::class);
        $this->app->singleton(Client::class, function () {
            return new Client(
                apiKey: config('services.anthropic.key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Task::class,TaskPolicy::class);
        Gate::define('admin',function(User $user){
            return $user->is_admin;
        });
        RateLimiter::for('task-api',function(Request $request){
            return Limit::perMinute(30)
            ->by($request->user()?->id?:$request->ip());
        });
        RateLimiter::for("login",function(Request $request){
            return Limit::perMinute(5)
            ->by($request->ip());
        });
        Task::observe(TaskObserver::class);
        // RateLimiter::for("admin",function(Request $request){
        //     return Limit::perMinute(30)
        //     ->by($request->user()->id);
        // });
    }
}
