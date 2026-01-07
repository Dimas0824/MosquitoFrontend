<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'App')</title>
    <!-- Minimal styles: prefer project stylesheet if available -->
    <link rel="stylesheet" href="/resources/css/app.css">
</head>

<body class="antialiased bg-slate-50">
    @yield('content')

    @stack('scripts')
</body>

</html>
