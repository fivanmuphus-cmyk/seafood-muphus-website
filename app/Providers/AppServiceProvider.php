<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('partials.footer', function ($view) {
            $view->with('footerBranches', Branch::query()->active()->ordered()->limit(3)->get(['id', 'name']));
        });
    }
}