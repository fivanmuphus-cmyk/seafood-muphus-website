<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Faq;
use Illuminate\View\View;

class PartnershipController extends Controller
{
    public function index(): View
    {
        return view('partnership', [
            'branchCount' => Branch::query()->active()->count(),
            'faqs' => Faq::query()->where('page', 'kemitraan')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}