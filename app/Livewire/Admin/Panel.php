<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Panel extends Component
{
    public string $tab = 'home'; // home|customers|orders|products

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.panel');
    }
}
