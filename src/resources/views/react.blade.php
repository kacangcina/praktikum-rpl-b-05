<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuBu</title>
    <link rel="icon" href="{{ asset('images/cubu-logo.svg') }}" type="image/svg+xml">
    @viteReactRefresh
    @vite('resources/js/react/main.jsx')
</head>
<body>
    <div id="root"></div>
    <script>
        window.__CUBU_FLASH__ = @json([
            'status' => session('status'),
            'error' => session('error'),
        ]);
    </script>
</body>
</html>
