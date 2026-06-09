<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard Login</h1>
        <p class="mt-1 text-sm text-slate-500">Sign in with your admin panel username and password</p>

        <form wire:submit="login" class="mt-6 space-y-4">
            <div>
                <label for="username" class="mb-1 block text-sm font-semibold">Username</label>
                <input id="username" type="text" wire:model="username" autocomplete="username"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">
                @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                <input id="password" type="password" wire:model="password" autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                Sign in
            </button>
        </form>
    </div>
</div>
