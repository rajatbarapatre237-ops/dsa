<?php

namespace App\Livewire\Dashboard;

use App\Models\Admin;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()
            ->where('username', $this->username)
            ->where('password', $this->password)
            ->first();

        if (! $admin) {
            $this->addError('username', 'Invalid username or password.');

            return;
        }

        session([
            'dashboard_authenticated' => true,
            'dashboard_admin_id' => $admin->id,
            'dashboard_admin_username' => $admin->username,
        ]);

        $this->redirect(route('dashboard.home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dashboard.login');
    }
}
