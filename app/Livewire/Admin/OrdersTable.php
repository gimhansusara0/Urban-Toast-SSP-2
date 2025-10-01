<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

class OrdersTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Call your API (since you said axios in Blade, but here we can fetch directly)
        // If you want to use DB directly instead of API, replace with Order::query()
        $response = Http::withCookies(request()->cookies->all(), config('session.domain'))
            ->get(url('/api/v1/admin/orders'), ['search' => $this->search, 'page' => $this->page]);

        $orders = $response->json();

        return view('livewire.admin.orders-table', [
            'orders' => $orders['data'] ?? [],
            'pagination' => $orders['meta'] ?? null,
        ]);
    }
}
