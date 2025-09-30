<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign in • Urban Roast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-100 text-neutral-900 flex items-center justify-center p-4">
  <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-0 rounded-3xl overflow-hidden shadow-2xl bg-white">

    {{-- Left: Image --}}
    <div class="relative hidden md:block">
      <img
        src="{{ asset('img/coffee.png') }}"
        alt="Coffee"
        class="absolute inset-0 w-full h-full object-cover"
      >
    </div>

    {{-- Right: Form --}}
    <div class="flex items-center justify-center p-8 md:p-12">
      <div class="w-full max-w-md">
        <div class="mb-8">
          <h1 class="text-3xl font-semibold tracking-tight">Sign in</h1>
          <p class="mt-1 text-sm text-neutral-500">Welcome back! let’s make today delicious.</p>
        </div>

        {{-- Session status --}}
        @if (session('status'))
          <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
            {{ session('status') }}
          </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
          <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg p-3">
            <ul class="list-disc ps-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
          @csrf

          <div>
            <label for="email" class="block text-sm font-medium">Email address</label>
            <input id="email" name="email" type="email" autocomplete="email" required
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="remember" class="rounded">
              Remember me
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-sm text-[#6B4F3A] hover:underline">
                Forgot password?
              </a>
            @endif
          </div>

          <button
            class="w-full rounded-xl bg-[#6B4F3A] hover:bg-[#6B4F3A] text-white font-semibold py-2.5 transition">
            Signin
          </button>
        </form>

        <p class="mt-6 text-sm text-neutral-500">
          Don’t have an account?
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-[#6B4F3A] hover:underline">Sign up</a>
          @endif
        </p>

        {{-- Back to role picker --}}
        <p class="mt-2 text-xs">
          <a href="{{ route('auth.role') }}" class="text-neutral-500 hover:underline">← pick a different role</a>
        </p>
      </div>
    </div>

  </div>
</body>
</html>
