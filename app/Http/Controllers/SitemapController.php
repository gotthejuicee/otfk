<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Page;
use App\Models\Specialty;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            url('/'),
            route('news.index'),
            route('specialties.index'),
            route('documents.index'),
            route('structure.index'),
            route('staff.administration'),
            route('galleries.index'),
            route('video.index'),
            route('contacts'),
            route('events'),
            route('faq'),
            route('bells'),
            route('applicants.create'),
        ];

        foreach (News::published()->get() as $n) {
            $urls[] = route('news.show', $n);
        }
        foreach (Page::published()->get() as $p) {
            $urls[] = url('/' . $p->slug);
        }
        foreach (Specialty::published()->get() as $s) {
            $urls[] = route('specialties.show', $s);
        }
        foreach (Gallery::published()->get() as $g) {
            $urls[] = route('galleries.show', $g);
        }
        foreach (DocumentCategory::all() as $c) {
            $urls[] = route('documents.category', $c);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (array_unique($urls) as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($url) . '</loc></url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
