@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)

@if($page->meta_description)
@section('meta_description', $page->meta_description)
@endif

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-l from-primary/5 to-transparent px-8 py-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900">{{ $page->title }}</h1>
        </div>
        <!-- Content Box -->
        <div class="px-8 py-8">
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed page-content">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>

<style>
.page-content h1, .page-content h2, .page-content h3 {
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #111827;
}
.page-content h1 { font-size: 1.5rem; }
.page-content h2 { font-size: 1.25rem; }
.page-content h3 { font-size: 1.1rem; }
.page-content p { margin-bottom: 1rem; }
.page-content ul, .page-content ol { padding-right: 1.5rem; margin-bottom: 1rem; }
.page-content li { margin-bottom: 0.25rem; }
.page-content a { color: var(--color-primary, #3b82f6); text-decoration: underline; }
.page-content img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1rem 0; }
.page-content table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
.page-content th, .page-content td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; }
.page-content th { background: #f9fafb; font-weight: 600; }
.page-content blockquote { border-right: 4px solid #3b82f6; padding-right: 1rem; color: #6b7280; margin: 1rem 0; }
</style>
@endsection
