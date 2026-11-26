<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'body', 'cover_path',
        'status', 'published_at', 'is_featured', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_featured' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if ($article->status === 'published' && blank($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'menu' => 'Menu',
            'cerita' => 'Cerita Muphus',
            'tips' => 'Tips',
            'kabar' => 'Kabar',
            default => 'Artikel',
        };
    }

    public function url(): string
    {
        return '/artikel/' . $this->slug;
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }

    /**
     * Isi artikel (Markdown) diubah menjadi HTML.
     * HTML mentah di dalam tulisan DIBUANG dan tautan berbahaya ditolak,
     * jadi hasilnya aman ditampilkan. Judul bagian (##) diberi id untuk daftar isi.
     */
    public function bodyHtml(): string
    {
        $html = Str::markdown((string) $this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return preg_replace_callback(
            '/<h2>(.*?)<\/h2>/s',
            fn (array $m) => '<h2 id="' . Str::slug(strip_tags($m[1])) . '">' . $m[1] . '</h2>',
            $html
        );
    }

    /** Daftar judul bagian (##) untuk "Dalam artikel ini". */
    public function tableOfContents(): array
    {
        preg_match_all('/^##\s+(.+)$/m', (string) $this->body, $matches);

        return collect($matches[1])
            ->map(fn (string $text) => ['text' => trim($text), 'id' => Str::slug(trim($text))])
            ->all();
    }
}