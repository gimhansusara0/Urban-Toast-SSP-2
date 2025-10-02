<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Livewire\Component;

class CustomersTable extends Component
{
    use WithPagination;

   
    //  Applied filters (used by the query)

    public string $search = '';           // applied search (name only)
    public string $status = 'all';        // all|active|inactive

   
    //   UI inputs (what the user is typing/selecting)
    //  We apply them explicitly so Enter / changes feel deterministic.
     
    public string $searchInput = '';      // text in the search box

    public array $selected = [];

    public ?int $editingId = null;
    public array $form = [
        'name' => '',
        'email' => '',
        'address' => '',
        'status' => 'active',
    ];


    //  Persist filters in URL

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        // On initial load, reflect applied search into the input
        $this->searchInput = $this->search;
    }

 
    //   Submit the search
  
    public function applySearch(): void
    {
        $this->search = trim($this->searchInput);
        $this->resetPage();
    }

 
    //   Change status via dropdown
 
    public function changeStatus(string $value): void
    {
        $value = strtolower($value);
        $this->status = in_array($value, ['all','active','inactive'], true) ? $value : 'all';
        $this->resetPage();
    }

    public function toggleSelectAll(): void
    {
        if (!empty($this->selected)) {
            $this->selected = [];
            return;
        }
        // Select all IDs from the CURRENT filtered set 
        $ids = (clone $this->query())->pluck('id')->toArray();
        $this->selected = $ids;
    }

    public function edit(int $id): void
    {
        $c = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'name' => $c->name,
            'email' => $c->email,
            'address' => $c->address,
            'status' => $c->status,
        ];
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function save(): void
    {
        $c = Customer::findOrFail($this->editingId);

        $this->validate([
            'form.name' => ['required','string','max:255'],
            'form.email' => ['required','email','max:255', Rule::unique('customers','email')->ignore($c->id)],
            'form.address' => ['nullable','string','max:255'],
            'form.status' => ['required', Rule::in(['active','inactive'])],
        ]);

        $c->update($this->form);
        $this->editingId = null;
        session()->flash('ok', 'Customer updated.');
    }

    public function bulkDeactivate(): void
    {
        if (!empty($this->selected)) {
            Customer::whereIn('id', $this->selected)->update(['status' => 'inactive']);
            $this->selected = [];
            session()->flash('ok', 'Selected customers deactivated.');
        }
    }

    public function bulkRemove(): void
    {
        if (!empty($this->selected)) {
            Customer::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            session()->flash('ok', 'Selected customers removed.');
            $this->resetPage();
        }
    }

    
    //  filters by applied $status and $search
 
    protected function query()
    {
        $s = trim($this->search);

        return Customer::query()
            ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
            ->when($s !== '', fn($q) => $q->where('name', 'like', '%'.$s.'%'))
            ->orderByDesc('id');
    }

    public function render()
    {
        $customers = $this->query()->paginate(10);
        return view('livewire.admin.customers-table', compact('customers'));
    }
}
