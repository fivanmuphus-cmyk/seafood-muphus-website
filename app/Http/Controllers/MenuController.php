<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\MenuItem;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $items = MenuItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $present = $items->pluck('category')->unique()->all();

        // Urutan tombol mengikuti urutan di config, dan hanya kategori yang punya menu.
        $categories = collect(config('muphus.menu_categories'))->only($present);

        $subcategories = collect(config('muphus.menu_subcategories'))
            ->only($items->where('category', 'seafood')->pluck('subcategory')->filter()->unique()->all());

        return view('menu', [
            'cards' => $items->where('show_as_card', true)->values(),
            'list' => $items->where('show_in_list', true)->values(),
            'categories' => $categories,
            'subcategories' => $subcategories,
            'branchCount' => Branch::query()->active()->count(),
        ]);
    }
}