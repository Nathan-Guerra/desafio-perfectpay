<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Checkout' }}</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{ $head ?? '' }}
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
{{ $slot }}
</body>
</html>
