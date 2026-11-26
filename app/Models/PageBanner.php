<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBanner extends Model
{
    /** Halaman yang punya foto banner (kunci => nama tampilan di admin). */
    public const PAGES = [
        'beranda' => 'Beranda (Hero)',
        'tentang' => 'Tentang',
        'menu' => 'Menu',
        'cabang' => 'Cabang',
        'artikel' => 'Artikel',
        'karir' => 'Karir',
        'kontak' => 'Kontak',
        'kemitraan' => 'Kemitraan',
    ];

    protected $fillable = ['page', 'title', 'subtitle', 'image_path'];
}