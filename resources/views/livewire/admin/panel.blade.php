

<div class="grid grid-cols-12 gap-4"><!-- SINGLE ROOT -->
  {{-- Sidebar --}}
  <aside class="col-span-12 md:col-span-3 lg:col-span-2">
    <div class="sticky top-4 space-y-2 bg-white rounded-2xl shadow p-3">
      <button wire:click="setTab('home')"
        class="w-full text-left px-4 py-2 rounded-xl {{ $tab==='home' ? 'bg-[#6B4F3A] text-white' : 'hover:bg-neutral-100' }}">
        Home
      </button>
      <button wire:click="setTab('customers')"
        class="w-full text-left px-4 py-2 rounded-xl {{ $tab==='customers' ? 'bg-[#6B4F3A] text-white' : 'hover:bg-neutral-100' }}">
        Customers
      </button>
      <button wire:click="setTab('products')"
        class="w-full text-left px-4 py-2 rounded-xl {{ $tab==='products' ? 'bg-[#6B4F3A] text-white' : 'hover:bg-neutral-100' }}">
        Products
      </button>
      <button wire:click="setTab('categories')"
        class="w-full text-left px-4 py-2 rounded-xl {{ $tab==='categories' ? 'bg-[#6B4F3A] text-white' : 'hover:bg-neutral-100' }}">
        Categories
      </button>
      <a href="{{ route('admin.orders.index') }}"
  class="w-full text-left px-4 py-2 rounded-xl {{ request()->routeIs('admin.orders.index') ? 'bg-[#6B4F3A] text-white' : 'hover:bg-neutral-100' }}">
  Orders
</a>

    </div>
  </aside>

  {{-- Content --}}
  <section class="col-span-12 md:col-span-9 lg:col-span-10">
    @if ($tab === 'customers')
      <livewire:admin.customers-table key="customers-table" />
    @elseif ($tab === 'products')
      <livewire:admin.products-table key="products-table" />
    @elseif ($tab === 'categories')
      <livewire:admin.categories-table key="categories-table" />
    @elseif ($tab === 'orders')
    <livewire:orders.new-order />
  @elseif ($tab === 'home')
  <div class="grid gap-4 md:grid-cols-2">
      <!-- At a glance -->
      <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-semibold mb-4">At a glance</h3>
          <ul class="space-y-2 text-sm text-neutral-700">
              <li><strong>Customers:</strong> {{ $totalCustomers }}</li>
              <li><strong>Orders:</strong> {{ $totalOrders }}</li>
              <li><strong>Revenue:</strong> ${{ number_format($totalRevenue,2) }}</li>
          </ul>
      </div>

      <!-- Top products -->
      <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-semibold mb-4">Most Bought Products</h3>
          <ul class="space-y-2 text-sm text-neutral-700">
              @foreach ($topProducts as $p)
                  <li>
                      {{ $p->product->name ?? 'Unknown' }} — {{ $p->qty }} sold
                  </li>
              @endforeach
          </ul>
      </div>

      <!-- Top categories -->
      <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-semibold mb-4">Most Sold Categories</h3>
          <ul class="space-y-2 text-sm text-neutral-700">
              @foreach ($topCategories as $c)
                  <li>
                      {{ $c->name }} — {{ $c->qty }} sold
                  </li>
              @endforeach
          </ul>
      </div>

      <!-- Recent activity -->
      <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-semibold mb-4">Empty</h3>
          <p class="text-sm text-neutral-600">Empty</p>
      </div>
  </div>
@endif



  </section>
</div>
