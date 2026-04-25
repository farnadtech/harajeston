@php
    $siteName    = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
    $siteTagline = \App\Models\SiteSetting::get('site_tagline', '');
    $siteDesc    = \App\Models\SiteSetting::get('site_description', '');
    $siteFavicon = \App\Models\SiteSetting::get('site_favicon', '');
    $faviconUrl  = $siteFavicon ? rtrim(config('app.url'), '/') . '/storage/' . $siteFavicon : '';
    $separator   = $siteTagline ? ' | ' . $siteName . ' - ' . $siteTagline : ' | ' . $siteName;
@endphp
<title>@yield('title', $siteName){{ $separator }}</title>
<meta name="description" content="@yield('meta_description', $siteDesc)">
@if($faviconUrl)
<link rel="icon" type="image/png" href="{{ $faviconUrl }}">
<link rel="shortcut icon" href="{{ $faviconUrl }}">
@endif
<meta property="og:title" content="@yield('title', $siteName){{ $separator }}">
<meta property="og:description" content="@yield('meta_description', $siteDesc)">
<meta property="og:site_name" content="{{ $siteName }}">
