<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $query = Article::query()->published()->latest('published_at');

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
            $query->where(fn ($w) => $w
                ->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('body', 'like', $like));
        }

        $articles = $query->get();

        // Artikel unggulan hanya tampil saat tidak sedang mencari.
        $featured = $q === ''
            ? ($articles->firstWhere('is_featured', true) ?? $articles->first())
            : null;

        $rest = $featured
            ? $articles->reject(fn (Article $a) => $a->id === $featured->id)->values()
            : $articles;

        return view('articles', compact('q', 'featured', 'rest', 'articles'));
    }

    public function show(string $slug): View
    {
        $article = Article::query()->published()->where('slug', $slug)->firstOrFail();

        $related = Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('article', compact('article', 'related'));
    }
}