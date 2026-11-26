<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** Alamat halaman tetap: [jalur, prioritas, seberapa sering berubah]. */
    private const PAGES = [
        ['/', '1.0', 'weekly'],
        ['/menu', '0.9', 'weekly'],
        ['/cabang', '0.9', 'weekly'],
        ['/tentang', '0.6', 'monthly'],
        ['/artikel', '0.7', 'weekly'],
        ['/karir', '0.6', 'weekly'],
        ['/kemitraan', '0.7', 'monthly'],
        ['/kontak', '0.5', 'yearly'],
        ['/kebijakan-privasi', '0.3', 'yearly'],
    ];

    public function index(): Response
    {
        $urls = collect(self::PAGES)->map(fn (array $p) => [
            'loc' => url($p[0]), 'priority' => $p[1], 'freq' => $p[2], 'lastmod' => null,
        ]);

        $articles = Article::query()->published()->latest('published_at')->get()->map(fn (Article $a) => [
            'loc' => url($a->url()), 'priority' => '0.6', 'freq' => 'monthly',
            'lastmod' => optional($a->updated_at)->toAtomString(),
        ]);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls->concat($articles) as $u) {
            $xml .= "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if ($u['lastmod']) {
                $xml .= '    <lastmod>' . e($u['lastmod']) . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $u['freq'] . "</changefreq>\n    <priority>" . $u['priority'] . "</priority>\n  </url>\n";
        }

        return response($xml . '</urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
