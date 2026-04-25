<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status'           => 'required|in:published,draft',
        ]);

        $data['slug'] = $data['slug']
            ? Page::generateSlug($data['slug'])
            : Page::generateSlug($data['title']);

        $data['content'] = $this->normalizeImageUrls($data['content']);

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'صفحه با موفقیت ایجاد شد.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status'           => 'required|in:published,draft',
        ]);

        $data['slug'] = $data['slug']
            ? Page::generateSlug($data['slug'], $page->id)
            : Page::generateSlug($data['title'], $page->id);

        $data['content'] = $this->normalizeImageUrls($data['content']);

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'صفحه با موفقیت ویرایش شد.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'صفحه حذف شد.');
    }

    public function uploadImage(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => 'required|image|max:4096']);
        $path = $request->file('file')->store('pages', 'public');
        $baseUrl = rtrim(config('app.url'), '/');
        $location = $baseUrl . '/storage/' . $path;
        return response()->json(['location' => $location]);
    }

    private function normalizeImageUrls(string $content): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        // Fix any wrong base URL that might have been stored (e.g. http://localhost/storage)
        $content = preg_replace_callback(
            '/src=["\']([^"\']+\/storage\/pages\/[^"\']+)["\']/',
            function ($matches) use ($appUrl) {
                $url = $matches[1];
                // Extract just the path after /storage/
                if (preg_match('/\/storage\/(pages\/.+)$/', $url, $m)) {
                    return 'src="' . $appUrl . '/storage/' . $m[1] . '"';
                }
                return $matches[0];
            },
            $content
        );
        return $content;
    }
}
