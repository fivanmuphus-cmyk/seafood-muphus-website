<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobOpening::query()->open()->orderBy('sort_order')->orderBy('id')->get();

        $filter = in_array($request->query('jenis'), ['outlet', 'office'], true)
            ? $request->query('jenis')
            : 'all';

        return view('careers', [
            'outlet' => $jobs->where('type', 'outlet')->values(),
            'office' => $jobs->where('type', 'office')->values(),
            'faqs' => Faq::query()->where('page', 'karir')->where('is_active', true)->orderBy('sort_order')->get(),
            'filter' => $filter,
        ]);
    }
}