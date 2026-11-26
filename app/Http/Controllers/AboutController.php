<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('about', [
            'branchCount' => Branch::query()->active()->count(),
        ]);
    }
}