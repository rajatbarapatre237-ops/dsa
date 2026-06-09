<div>
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit {{ $pageLabel }}</h1>
            <p class="text-sm text-slate-500">Slug: {{ $slug }}</p>
        </div>
        <a href="{{ route(match ($slug) {
            'privacy-policy' => 'pages.privacy',
            'terms' => 'pages.terms',
            'about-us' => 'pages.about',
            default => 'home',
        }) }}" target="_blank" class="text-sm font-semibold text-sky-700 hover:underline">Preview public page</a>
    </div>

    @if ($savedMessage)
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ $savedMessage }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        <div>
            <label class="mb-1 block text-sm font-semibold">Title</label>
            <input type="text" wire:model="title"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">
            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold">Content (HTML allowed)</label>
            <textarea wire:model="body" rows="16"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-sky-500 focus:outline-none"></textarea>
            @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm font-medium">
            <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-sky-700">
            Published on public site
        </label>

        <button type="submit" class="rounded-lg bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
            Save page
        </button>
    </form>
</div>
