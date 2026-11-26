<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::query()->active()->ordered()->get();

        // Kota dengan cabang terbanyak jadi tombol utama; sisanya di tombol "Lainnya".
        $counts = $branches->countBy('city')->sortDesc();
        $topCities = $counts->keys()->take(4)->values();
        $otherCities = $counts->keys()->slice(4)->sort()->values();

        return view('branches', [
            'branches' => $branches,
            'topCities' => $topCities,
            'otherCities' => $otherCities,
        ]);
    }
}