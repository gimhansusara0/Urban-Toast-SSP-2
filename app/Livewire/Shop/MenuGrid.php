<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;

class MenuGrid extends Component
{
    use WithPagination;

    protected string $pageName = 'menuPage';

    public string $category = 'all';
    public array $categories = [];
    public int $perPage = 6;

    // Products in the cart
    public array $inCart = [];

    // No url
    protected $queryString = [];

    public function mount(): void
    {
        $this->categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get(['id','name','slug'])
            ->toArray();
    }

    public function hydrate(): void
    {
        // refresh the cart list
        $this->inCart = auth()->check()
            ? OrderItem::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->pluck('product_id')
                ->map(fn($id) => (int)$id)->all()
            : [];
    }

    public function selectCategory(string $value): void
    {
        $this->category = $value === 'all' ? 'all' : (string) $value;
        $this->resetPage($this->pageName);
        $this->dispatch('menu-grid-scroll');
    }

    // pagination
    public function goPrev(): void { $this->previousPage($this->pageName); $this->dispatch('menu-grid-scroll'); }
    public function goNext(): void { $this->nextPage($this->pageName); $this->dispatch('menu-grid-scroll'); }
    public function goTo(int $page): void { $this->gotoPage($page, $this->pageName); $this->dispatch('menu-grid-scroll'); }

    public function addToCart(int $productId): void
    {
        if (!auth()->check()) {
            // if not logged in then,  go to role picker 
            $this->redirectRoute('auth.role');
            return;
        }

        // this doesn't allow for duplicate items in cart
        $already = OrderItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('status', 'pending')
            ->exists();

        if ($already) {
            return; 
        }

        $product = Product::where('status', 'active')->findOrFail($productId);

        OrderItem::create([
            'user_id'     => auth()->id(),
            'product_id'  => $product->id,
            'quantity'    => 1,
            'price_each'  => $product->price, // snapshot the current price
            'status'      => 'pending',
        ]);

        $this->dispatch('cart-updated');     
        $this->dispatch('menu-grid-scroll'); 
    }

    protected function query()
    {
        $q = Product::query()
            ->with('category:id,name,slug')
            ->where('status', 'active');

        if ($this->category !== 'all') {
            $q->where('category_id', (int) $this->category);
        }

        return $q->orderBy('name');
    }

    public function render()
    {
        $products = $this->query()->paginate($this->perPage, pageName: $this->pageName);
        return view('livewire.shop.menu-grid', compact('products'));
    }
}
