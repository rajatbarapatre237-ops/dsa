<?php

namespace App\Http\Controllers;

use App\Models\ContactQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('public.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactQuery::query()->create($validated);

        return redirect()
            ->route('contact.show')
            ->with('success', 'Thank you! We received your message and will get back to you soon.');
    }
}
