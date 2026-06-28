<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Hola Santana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
    <aside class="admin-sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">Hola Santana</a>
        <nav>
            <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
            <a href="{{ route('admin.pages.index') }}" @class(['active' => request()->routeIs('admin.pages.*')])>Page Management</a>
            <a href="{{ route('admin.languages.index') }}" @class(['active' => request()->routeIs('admin.languages.*')])>Languages</a>
            <a href="{{ route('admin.owners.index') }}" @class(['active' => request()->routeIs('admin.owners.*')])>Owners</a>
            <a href="{{ route('admin.properties.index') }}" @class(['active' => request()->routeIs('admin.properties.*')])>Properties</a>
            <a href="{{ route('admin.reservations.index') }}" @class(['active' => request()->routeIs('admin.reservations.*')])>Reservations</a>
            <a href="{{ route('admin.activities.index') }}" @class(['active' => request()->routeIs('admin.activities.*')])>Activities</a>
            <a href="{{ route('admin.holiday-homes.index') }}" @class(['active' => request()->routeIs('admin.holiday-homes.*')])>Holiday Homes</a>
            <a href="{{ route('home') }}" target="_blank">View Site</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">Admin panel</p>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button class="button ghost" type="submit">Logout</button>
            </form>
        </header>
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
