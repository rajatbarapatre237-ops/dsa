@extends('layouts.public')

@section('title', 'Home — ' . config('app.name', 'DSA Edu'))

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-slate-900">Welcome to {{ config('app.name', 'DSA Edu') }}</h1>
        <p class="mt-3 max-w-2xl text-slate-600">Learning management for students, teachers, and parents.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('pages.about') }}" class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">About Us</a>
            <a href="{{ route('contact.show') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Contact Us</a>
        </div>
    </div>
@endsection
