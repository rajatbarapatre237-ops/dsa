@extends('layouts.public')

@section('title', 'Contact Us — ' . config('app.name', 'DSA Edu'))

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-slate-900">Contact Us</h1>
        <p class="mt-2 text-slate-600">Send us a message and we will respond as soon as possible.</p>

        @if (session('success'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-sm font-semibold">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">
            </div>
            <div>
                <label for="message" class="mb-1 block text-sm font-semibold">Message</label>
                <textarea id="message" name="message" rows="6" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="rounded-lg bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                Send message
            </button>
        </form>
    </div>
@endsection
