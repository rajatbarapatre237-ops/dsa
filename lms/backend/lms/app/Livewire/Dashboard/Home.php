<?php

namespace App\Livewire\Dashboard;

use App\Models\CmsPage;
use App\Models\ContactQuery;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.dashboard.home', [
            'pages' => CmsPage::query()->orderBy('title')->get(),
            'unreadCount' => ContactQuery::query()->where('is_read', false)->count(),
        ]);
    }
}
