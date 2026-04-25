<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        // Fix image URLs: replace wrong base URL with correct APP_URL
        $appUrl = rtrim(config('app.url'), '/');
        $wrongBase = 'http://localhost/storage';
        $correctBase = $appUrl . '/storage';
        $page->content = str_replace($wrongBase, $correctBase, $page->content);

        return view('pages.show', compact('page'));
    }
}
