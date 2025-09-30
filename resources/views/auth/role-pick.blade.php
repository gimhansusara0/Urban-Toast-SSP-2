<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Select Role • Urban Roast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-900 text-white flex items-center justify-center p-4">
  <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-0 rounded-3xl overflow-hidden shadow-2xl border border-white/10 bg-neutral-800">

    {{-- Left: Image / Illustration --}}
    <div class="relative bg-neutral-800">
      {{-- Replace the src with your own image (e.g., public/img/coffee.jpg) --}}
      <img
        src="{{ asset('img/coffee.png') }}"
        alt="A warm cup of coffee"
        class="h-full w-full object-cover"
        onerror="this.style.display='none'; this.parentElement.classList.add('p-10'); this.parentElement.innerHTML+='<div class=&quot;text-neutral-300&quot;>Add a coffee image at <code>public/img/coffee.jpg</code></div>';"
      >
    </div>

    {{-- Right: Role pick --}}
    <div class="flex items-center justify-center p-8 md:p-12">
      <div class="w-full max-w-md space-y-8">
        <div class="space-y-2">
          <h1 class="text-3xl font-semibold tracking-tight">Select Role</h1>
          <p class="text-sm text-neutral-400">Choose how you want to sign in today.</p>
        </div>

        <div class="space-y-4">
          {{-- Admin button -> /admin/login --}}
          <a href="{{ route('admin.login') }}"
             class="block w-full rounded-full border border-white/20 hover:border-white/40 px-6 py-3 text-center font-medium transition">
            Admin
          </a>

          {{-- Customer button -> Jetstream /login --}}
          <a href="{{ route('login') }}"
             class="block w-full rounded-full border border-white/20 hover:border-white/40 px-6 py-3 text-center font-medium transition">
            Customer
          </a>
        </div>

        <div class="pt-6 text-xs text-neutral-400 text-center">
          © {{ date('Y') }} Urban Roast
        </div>
      </div>
    </div>

  </div>
</body>
</html>
