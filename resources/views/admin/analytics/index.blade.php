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

    <!-- Recent activity placeholder -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="font-semibold mb-4">Recent Activity</h3>
        <p class="text-sm text-neutral-600">Coming soon (orders timeline, reviews, etc.)</p>
    </div>
</div>
