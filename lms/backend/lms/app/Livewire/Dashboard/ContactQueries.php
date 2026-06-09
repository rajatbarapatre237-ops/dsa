<?php

namespace App\Livewire\Dashboard;

use App\Models\ContactQuery;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class ContactQueries extends Component
{
    use WithPagination;

    public function markRead(int $id): void
    {
        ContactQuery::query()->whereKey($id)->update(['is_read' => true]);
    }

    public function markUnread(int $id): void
    {
        ContactQuery::query()->whereKey($id)->update(['is_read' => false]);
    }

    public function deleteQuery(int $id): void
    {
        ContactQuery::query()->whereKey($id)->delete();
    }

    public function render()
    {
        return view('livewire.dashboard.contact-queries', [
            'queries' => ContactQuery::query()->latest('id')->paginate(15),
        ]);
    }
}
