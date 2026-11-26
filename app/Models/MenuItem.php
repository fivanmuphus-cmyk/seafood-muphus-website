<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name', 'category', 'subcategory', 'description', 'variants',
        'price', 'price_max', 'price_note', 'image_path', 'badge',
        'show_on_home', 'show_as_card', 'show_in_list', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'price_max' => 'integer',
            'variants' => 'array',
            'show_on_home' => 'boolean',
            'show_as_card' => 'boolean',
            'show_in_list' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function categoryLabel(): string
    {
        return config('muphus.menu_categories')[$this->category] ?? 'Lainnya';
    }

    public function subcategoryLabel(): ?string
    {
        return $this->subcategory ? (config('muphus.menu_subcategories')[$this->subcategory] ?? null) : null;
    }

    /** [harga terendah, harga tertinggi]; diambil dari kolom harga, atau dari varian bila kosong. */
    public function priceRange(): array
    {
        $prices = collect($this->variants ?? [])->pluck('price')->filter()->map(fn ($p) => (int) $p);

        $min = $this->price ?: $prices->min();
        $max = $this->price_max ?: $prices->max();

        return [$min ?: null, $max ?: null];
    }

    /** Contoh: "Rp 50.000 – 65.000", "Rp 188.000", "Rp 50.000 / ons". Null bila tidak ada harga. */
    public function priceLabel(): ?string
    {
        [$min, $max] = $this->priceRange();

        if (! $min) {
            return null;
        }

        $format = fn (int $n): string => number_format($n, 0, ',', '.');
        $label = ($max && $max > $min) ? 'Rp ' . $format($min) . ' – ' . $format($max) : 'Rp ' . $format($min);

        return $this->price_note ? $label . ' / ' . $this->price_note : $label;
    }

    /** Harga singkat seperti di menu cetak: 35000 menjadi "35K". */
    public static function shortPrice(?int $price): ?string
    {
        if (! $price) {
            return null;
        }

        return $price % 1000 === 0 ? ($price / 1000) . 'K' : number_format($price, 0, ',', '.');
    }
}