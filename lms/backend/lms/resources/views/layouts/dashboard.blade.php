<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name', 'DSA Edu') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 border-r border-slate-200 bg-white p-5">
            <a href="{{ route('dashboard.home') }}" class="mb-2 block text-lg font-bold text-sky-700">DSA Dashboard</a>
            @if (session('dashboard_admin_username'))
                <p class="mb-8 text-xs text-slate-500">Signed in as {{ session('dashboard_admin_username') }}</p>
            @else
                <div class="mb-8"></div>
            @endif
            <nav class="space-y-1 text-sm font-medium">
                <a href="{{ route('dashboard.home') }}" class="block rounded-lg px-3 py-2 hover:bg-sky-50 {{ request()->routeIs('dashboard.home') ? 'bg-sky-50 text-sky-700' : '' }}">Overview</a>
                <p class="px-3 pt-4 text-xs font-bold uppercase tracking-wide text-slate-400">Pages</p>
                <a href="{{ route('dashboard.pages.edit', 'privacy-policy') }}" class="block rounded-lg px-3 py-2 hover:bg-sky-50 {{ request()->is('dashboard/pages/privacy-policy') ? 'bg-sky-50 text-sky-700' : '' }}">Privacy Policy</a>
                <a href="{{ route('dashboard.pages.edit', 'terms') }}" class="block rounded-lg px-3 py-2 hover:bg-sky-50 {{ request()->is('dashboard/pages/terms') ? 'bg-sky-50 text-sky-700' : '' }}">Terms</a>
                <a href="{{ route('dashboard.pages.edit', 'about-us') }}" class="block rounded-lg px-3 py-2 hover:bg-sky-50 {{ request()->is('dashboard/pages/about-us') ? 'bg-sky-50 text-sky-700' : '' }}">About Us</a>
                <p class="px-3 pt-4 text-xs font-bold uppercase tracking-wide text-slate-400">Inbox</p>
                <a href="{{ route('dashboard.contact-queries') }}" class="block rounded-lg px-3 py-2 hover:bg-sky-50 {{ request()->routeIs('dashboard.contact-queries') ? 'bg-sky-50 text-sky-700' : '' }}">Contact Queries</a>
            </nav>
            <form method="POST" action="{{ route('dashboard.logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">Logout</button>
            </form>
        </aside>
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
