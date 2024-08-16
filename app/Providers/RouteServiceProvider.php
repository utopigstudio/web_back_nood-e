<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::model('discussion', Discussion::class);
        Route::model('topic', Topic::class);
        Route::model('comment', Comment::class);
    }   
}
