<?php

namespace App\Livewire\Dashboard;

use App\Models\CmsPage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class EditPage extends Component
{
    public string $slug = '';

    public string $title = '';

    public string $body = '';

    public bool $is_published = true;

    public string $savedMessage = '';

    public function mount(string $slug): void
    {
        $allowed = array_keys(CmsPage::slugs());
        abort_unless(in_array($slug, $allowed, true), 404);

        $this->slug = $slug;
        $page = CmsPage::query()->firstOrCreate(
            ['slug' => $slug],
            ['title' => CmsPage::slugs()[$slug], 'body' => '', 'is_published' => true],
        );

        $this->title = $page->title;
        $this->body = $page->body ?? '';
        $this->is_published = $page->is_published;
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:190'],
            'body' => ['nullable', 'string'],
            'is_published' => ['boolean'],
        ]);

        CmsPage::query()->updateOrCreate(
            ['slug' => $this->slug],
            [
                'title' => $this->title,
                'body' => $this->body,
                'is_published' => $this->is_published,
            ],
        );

        $this->savedMessage = 'Page saved successfully.';
    }

    public function render()
    {
        return view('livewire.dashboard.edit-page', [
            'pageLabel' => CmsPage::slugs()[$this->slug] ?? $this->title,
        ]);
    }
}
