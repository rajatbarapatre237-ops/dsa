<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'DSA Edu'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="text-lg font-bold text-sky-700">{{ config('app.name', 'DSA Edu') }}</a>
            <a href="{{ route('contact.show') }}" class="text-sm font-semibold text-sky-700 hover:underline">Contact</a>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-8">
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-4xl flex-wrap gap-4 px-4 py-6 text-sm text-slate-600">
            <a href="{{ route('pages.about') }}" class="hover:text-sky-700">About Us</a>
            <a href="{{ route('pages.privacy') }}" class="hover:text-sky-700">Privacy Policy</a>
            <a href="{{ route('pages.terms') }}" class="hover:text-sky-700">Terms</a>
            <a href="{{ route('contact.show') }}" class="hover:text-sky-700">Contact Us</a>
            <span class="ml-auto text-slate-400">&copy; {{ date('Y') }} {{ config('app.name', 'DSA Edu') }}</span>
        </div>
    </footer>
</body>
</html>
