{{-- PWA: web manifest, theme color, and iOS home-screen meta. Included in every layout head. --}}
<link rel="manifest" href="/manifest.webmanifest">
{{-- Per-page top chrome: pages can override via <x-slot name="themeColor"> etc. so the PWA
     status bar blends into whatever the page renders at the top (white topbar vs. teal hero). --}}
@php
    $themeColorLight = $themeColor     ?? '#ffffff';   // matches the white topbar
    $themeColorDark  = $themeColorDark  ?? '#111827';   // matches dark:bg-gray-900 topbar
    $iosStatusBar    = $iosStatusBarStyle ?? 'default';
@endphp
<meta name="theme-color" media="(prefers-color-scheme: light)" content="{{ $themeColorLight }}">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="{{ $themeColorDark }}">

{{-- iOS / Safari add-to-home-screen --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="{{ $iosStatusBar }}">
<meta name="apple-mobile-web-app-title" content="LifeOS">

{{-- Icons --}}
<link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png?v=2">
<link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png?v=2">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png?v=2">
