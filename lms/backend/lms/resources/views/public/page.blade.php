@extends('layouts.public')

@section('title', $page->title . ' — ' . config('app.name', 'DSA Edu'))

@section('content')
    <article class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
        <div class="mt-6 space-y-3 leading-relaxed text-slate-700">
            {!! $page->body !!}
        </div>
    </article>
@endsection
