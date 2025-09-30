<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesTable extends Component
{
    use WithPagination;

    /** Filters persisted in URL */
    public string $search = '';
    public string $status = 'all'; // all|active|inactive

    /** UI inputs */
    public string $searchInput = '';

    /** UI state */
    public array $selected = [];
    public ?int $editingId = null;
    public bool $showCreate = false;

    /** Inline edit form */
    public array $form = [
        'name'   => '',
        'slug'   => '',
        'status' => 'active',
    ];

    /** Create form (modal) */
    public array $create = [
        'name'   => '',
        'slug'   => '',
        'status' => 'active',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function mount(): void
    {
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
        $this->status = in_array($value, ['all','active','inactive'], true) ? $value : 'all';
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
        $this->create = ['name'=>'','slug'=>'','status'=>'active'];
        $this->showCreate = true;
    }

    public function closeCreate(): void
    {
        $this->showCreate = false;
    }

    public function saveCreate(): void
    {
        $data = $this->validate([
            'create.name'   => ['required','string','max:255'],
            'create.slug'   => ['nullable','string','max:255', 'unique:categories,slug'],
            'create.status' => ['required', Rule::in(['active','inactive'])],
        ])['create'];

        // If slug omitted, let model boot() handle or generate here:
        if (empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name']);
        }

        Category::create($data);

        $this->showCreate = false;
        session()->flash('ok', 'Category added.');
        $this->resetPage();
    }

    /** ----- Edit row ----- */
    public function edit(int $id): void
    {
        $c = Category::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'name'   => $c->name,
            'slug'   => $c->slug,
            'status' => $c->status,
        ];
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function save(): void
    {
        $c = Category::findOrFail($this->editingId);

        $this->validate([
            'form.name'   => ['required','string','max:255'],
            'form.slug'   => ['nullable','string','max:255', Rule::unique('categories','slug')->ignore($c->id)],
            'form.status' => ['required', Rule::in(['active','inactive'])],
        ]);

        $payload = $this->form;

        // If slug left empty, regenerate from name
        if (empty($payload['slug'])) {
            $payload['slug'] = $this->uniqueSlug($payload['name'], $c->id);
        }

        $c->update($payload);
        $this->editingId = null;
        session()->flash('ok', 'Category updated.');
    }

    /** ----- Delete / Bulk ----- */
    public function delete(int $id): void
    {
        Category::whereKey($id)->delete();
        session()->flash('ok', 'Category removed.');
        $this->resetPage();
    }

    public function bulkDeactivate(): void
    {
        if (!empty($this->selected)) {
            Category::whereIn('id', $this->selected)->update(['status' => 'inactive']);
            $this->selected = [];
            session()->flash('ok', 'Selected categories marked Inactive.');
        }
    }

    public function bulkRemove(): void
    {
        if (!empty($this->selected)) {
            Category::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            session()->flash('ok', 'Selected categories removed.');
            $this->resetPage();
        }
    }

    /** Base query */
    protected function query()
    {
        $s = trim($this->search);

        return Category::query()
            ->withCount('products')
            ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
            ->when($s !== '', fn($q) => $q->where('name', 'like', '%'.$s.'%'))
            ->orderByDesc('id');
    }

    /** Unique slug helper */
    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'cat';
        $slug = $base;
        $i = 1;
        $exists = fn($s) => Category::where('slug', $s)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
        while ($exists($slug)) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    public function render()
    {
        $categories = $this->query()->paginate(10);
        return view('livewire.admin.categories-table', compact('categories'));
    }
}
