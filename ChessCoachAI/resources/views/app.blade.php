<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- CSRF token in meta tag — Vue reads this for fetch() requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Chess Coach AI</title>

    {{-- Inertia injects any <Head> tags set in Vue components --}}
    @inertiaHead

    {{-- Vite compiles and hot-reloads resources/js/app.js + resources/css/app.css --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{--
        Inertia mounts the Vue app here.
        Equivalent to <component id="app" /> in a Blazor host page.
    --}}
    @inertia
</body>
</html>
