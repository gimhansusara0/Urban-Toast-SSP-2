@php
  $brand = '#eb3d22';
@endphp

<div class="mx-auto max-w-5xl">

  <h1 class="text-3xl font-extrabold mb-4">Your Cart</h1>

  <div id="cart-message"></div>

  <div class="bg-white rounded-2xl shadow border border-neutral-200 overflow-hidden">
    {{-- list --}}
    <div id="cart-list" class="max-h-[calc(100vh-260px)] overflow-y-auto divide-y">
      <div class="p-8 text-center text-neutral-500">Loading...</div>
    </div>

    {{-- footer --}}
    <div class="flex items-center justify-between p-4">
      <div class="text-lg">
        <span class="text-neutral-500">Total:</span>
        <span id="cart-total" class="font-extrabold">Rs 0.00</span>
      </div>
      <button onclick="checkoutCart()"
              id="checkout-btn"
              class="px-6 py-3 rounded-md text-white font-semibold disabled:opacity-50"
              style="background: {{ $brand }};"
              disabled>
        Checkout
      </button>
    </div>
  </div>

</div>

{{-- Axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const token = "{{ auth()->user()?->currentAccessToken()?->plainTextToken ?? '' }}";

    function loadCart() {
        axios.get('/api/v1/cart', {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            const items = res.data.data || [];
            let html = '';
            let total = 0;

            if (items.length === 0) {
                html = '<div class="p-8 text-center text-neutral-500">Your cart is empty.</div>';
                document.getElementById('cart-list').innerHTML = html;
                document.getElementById('cart-total').innerText = 'Rs 0.00';
                document.getElementById('checkout-btn').disabled = true;
                return;
            }

            items.forEach(i => {
                const line = (i.price_each * i.quantity).toFixed(2);
                total += parseFloat(line);

                const available = i.available;

                html += `
                  <div class="flex items-center gap-4 p-4 ${available ? '' : 'bg-red-50'}">
                    <img src="${i.image ?? '/img/placeholder.png'}"
                         class="h-16 w-16 rounded-lg object-cover border" alt="">
                    <div class="flex-1">
                      <div class="font-semibold">${i.name}</div>
                      ${!available ? `<div class="text-xs text-red-600">Unavailable</div>` : ''}
                      ${available ? `<div class="text-xs text-neutral-500">Rs ${i.price_each} each</div>` : ''}
                    </div>

                    ${available ? `
                      <div class="flex items-center gap-2">
                        <button onclick="updateQty(${i.id}, ${i.quantity - 1})"
                                class="h-8 w-8 rounded-md border border-neutral-300 hover:bg-neutral-100">–</button>
                        <div class="w-10 text-center">${i.quantity}</div>
                        <button onclick="updateQty(${i.id}, ${i.quantity + 1})"
                                class="h-8 w-8 rounded-md border border-neutral-300 hover:bg-neutral-100">+</button>
                      </div>
                      <div class="w-28 text-right font-semibold">Rs ${line}</div>
                    ` : `
                      <div class="w-28 text-right font-semibold text-neutral-400">—</div>
                    `}

                    <button onclick="removeItem(${i.id})"
                            class="ml-3 text-red-600 hover:underline">Remove</button>
                  </div>
                `;
            });

            document.getElementById('cart-list').innerHTML = html;
            document.getElementById('cart-total').innerText = 'Rs ' + total.toFixed(2);
            document.getElementById('checkout-btn').disabled = total <= 0;
        })
        .catch(() => {
            document.getElementById('cart-list').innerHTML =
              '<div class="p-8 text-center text-red-500">Failed to load cart.</div>';
        });
    }

    function updateQty(itemId, qty) {
        if (qty < 1) return;
        axios.put(`/api/v1/cart/${itemId}`, { quantity: qty }, {
            headers: { Authorization: `Bearer ${token}` }
        }).then(loadCart);
    }

    function removeItem(itemId) {
        axios.delete(`/api/v1/cart/${itemId}`, {
            headers: { Authorization: `Bearer ${token}` }
        }).then(loadCart);
    }

    function checkoutCart() {
        axios.post('/api/v1/orders/checkout', {}, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(() => {
            document.getElementById('cart-message').innerHTML =
              `<div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
                ✅ Thanks! Your order was placed.
              </div>`;
            loadCart();
        })
        .catch(() => {
            document.getElementById('cart-message').innerHTML =
              `<div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
                ❌ Checkout failed. Try again.
              </div>`;
        });
    }

    document.addEventListener('DOMContentLoaded', loadCart);
</script>
