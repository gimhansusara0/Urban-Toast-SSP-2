<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Livewire\Component;

class ProductsTable extends Component
{
    use WithPagination;

    /** Filters persisted in URL */
    public string $search   = '';
    public string $status   = 'all';   // all|active|inactive|archived
    public string $category = 'all';   // 'all' or category_id (string)

    /** UI inputs */
    public string $searchInput = '';

    /** UI state */
    public array $selected = [];
    public ?int $editingId = null;
    public bool $showCreate = false;

    /** Inline edit form (no stock) */
    public array $form = [
        'category_id' => null,
        'name'        => '',
        'price'       => '',
        'status'      => 'active',  // active=Available, inactive=Unavailable
        'image'       => '',
        'description' => '',
    ];

    /** Create form (modal) — no stock */
    public array $create = [
        'category_id' => null,
        'name'        => '',
        'price'       => '',
        'status'      => 'active',
        'image'       => '',
        'description' => '',
    ];

    /** Dropdown data */
    public $categories = [];

    protected $queryString = [
        'search'   => ['except' => ''],
        'status'   => ['except' => 'all'],
        'category' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $this->categories  = Category::orderBy('name')->get(['id','name']);
        $this->searchInput = $this->search;
    }

    /** Apply search on Enter / button */
    public function applySearch(): void
    {
        $this->search = trim($this->searchInput);
        $this->resetPage();
    }

    public function changeStatus(string $value): void
    {
        $value = strtolower($value);
        $this->status = in_array($value, ['all','active','inactive','archived'], true) ? $value : 'all';
        $this->resetPage();
    }

    public function changeCategory($value): void
    {
        $this->category = ($value === 'all' || $value === null || $value === '') ? 'all' : (string) $value;
        $this->resetPage();
    }

    public function toggleSelectAll(): void
    {
        if (!empty($this->selected)) { $this->selected = []; return; }
        $ids = (clone $this->query())->pluck('id')->toArray();
        $this->selected = $ids;
    }

    /** ----- Create (modal) ----- */
    public function openCreate(): void
    {
        $this->resetValidation();
        $this->create = [
            'category_id' => $this->category !== 'all' ? (int) $this->category : null,
            'name'        => '',
            'price'       => '',
            'status'      => 'active',
            'image'       => '',
            'description' => '',
        ];
        $this->showCreate = true;
    }

    public function closeCreate(): void
    {
        $this->showCreate = false;
    }

    public function saveCreate(): void
    {
        $data = $this->validate([
            'create.category_id' => ['required','integer','exists:categories,id'],
            'create.name'        => ['required','string','max:255'],
            'create.price'       => ['required','numeric','min:0'],
            'create.status'      => ['required', Rule::in(['active','inactive','archived'])],
            'create.image'       => ['nullable','string','max:1024'],
            'create.description' => ['nullable','string'],
        ])['create'];

        Product::create($data);

        $this->showCreate = false;
        session()->flash('ok', 'Product added.');
        $this->resetPage();
    }

    /** ----- Edit row ----- */
    public function edit(int $id): void
    {
        $p = Product::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'category_id' => $p->category_id,
            'name'        => $p->name,
            'price'       => (string) $p->price,
            'status'      => $p->status,
            'image'       => $p->image ?? '',
            'description' => $p->description ?? '',
        ];
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function save(): void
    {
        $p = Product::findOrFail($this->editingId);

        $this->validate([
            'form.category_id' => ['required','integer','exists:categories,id'],
            'form.name'        => ['required','string','max:255'],
            'form.price'       => ['required','numeric','min:0'],
            'form.status'      => ['required', Rule::in(['active','inactive','archived'])],
            'form.image'       => ['nullable','string','max:1024'],
            'form.description' => ['nullable','string'],
        ]);

        $p->update($this->form);
        $this->editingId = null;
        session()->flash('ok', 'Product updated.');
    }

    /** ----- Delete ----- */
    public function delete(int $id): void
    {
        Product::whereKey($id)->delete();
        session()->flash('ok', 'Product removed.');
        $this->resetPage();
    }

    public function bulkDeactivate(): void
    {
        if (!empty($this->selected)) {
            Product::whereIn('id', $this->selected)->update(['status' => 'inactive']);
            $this->selected = [];
            session()->flash('ok', 'Selected products marked Unavailable.');
        }
    }

    public function bulkRemove(): void
    {
        if (!empty($this->selected)) {
            Product::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            session()->flash('ok', 'Selected products removed.');
            $this->resetPage();
        }
    }

    /** Base query */
    protected function query()
    {
        $s = trim($this->search);

        return Product::query()
            ->with('category:id,name,slug')
            ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
            ->when($this->category !== 'all', fn($q) => $q->where('category_id', (int) $this->category))
            ->when($s !== '', fn($q) => $q->where('name', 'like', '%'.$s.'%'))
            ->orderByDesc('id');
    }

    public function render()
    {
        $products = $this->query()->paginate(7); // pagination stays; height of table is fixed in Blade
        return view('livewire.admin.products-table', compact('products'));
    }
}
