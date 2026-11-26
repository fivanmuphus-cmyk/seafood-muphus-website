<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Branch;
use App\Models\JobOpening;
use App\Models\MenuItem;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'menuItems' => MenuItem::query()
                ->where('is_active', true)->where('show_on_home', true)
                ->orderBy('sort_order')->orderBy('id')->limit(4)->get(),
            'branches' => Branch::query()->active()->ordered()->limit(60)->get(),
            'branchCount' => Branch::query()->active()->count(),
            'jobs' => JobOpening::query()->open()->orderBy('sort_order')->orderBy('id')->limit(3)->get(),
            'articles' => Article::query()->published()->latest('published_at')->limit(3)->get(),
        ]);
    }
}