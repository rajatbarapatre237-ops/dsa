<div>
    <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
    <p class="mt-1 text-slate-600">Manage public pages and review contact form submissions.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-400">CMS Pages</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $pages->count() }}</p>
            <p class="mt-1 text-sm text-slate-500">Privacy, Terms, About</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-400">Unread queries</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $unreadCount }}</p>
            <a href="{{ route('dashboard.contact-queries') }}" class="mt-2 inline-block text-sm font-semibold text-sky-700 hover:underline">View inbox</a>
        </div>
    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-lg font-bold">Public links</h2>
        <ul class="mt-3 space-y-2 text-sm">
            <li><a href="{{ route('pages.privacy') }}" target="_blank" class="text-sky-700 hover:underline">Privacy Policy</a></li>
            <li><a href="{{ route('pages.terms') }}" target="_blank" class="text-sky-700 hover:underline">Terms</a></li>
            <li><a href="{{ route('pages.about') }}" target="_blank" class="text-sky-700 hover:underline">About Us</a></li>
            <li><a href="{{ route('contact.show') }}" target="_blank" class="text-sky-700 hover:underline">Contact Us</a></li>
        </ul>
    </div>
</div>
