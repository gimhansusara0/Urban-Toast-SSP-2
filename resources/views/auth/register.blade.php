<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign up • Urban Roast</title>
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
          <h1 class="text-3xl font-semibold tracking-tight">Create your account</h1>
          <p class="mt-1 text-sm text-neutral-500">Join us and start brewing better days ☕</p>
        </div>

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

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
          @csrf

          <div>
            <label for="name" class="block text-sm font-medium">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div>
            <label for="email" class="block text-sm font-medium">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div>
            <label for="address" class="block text-sm font-medium">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address') }}"
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
          </div>

          <button
            class="w-full rounded-xl bg-[#6B4F3A] hover:bg-[#6B4F3A] text-white font-semibold py-2.5 transition">
            Create account
          </button>
        </form>

        <p class="mt-6 text-sm text-neutral-500">
          Already have an account?
          <a href="{{ route('login') }}" class="text-[#6B4F3A] hover:underline">Sign in</a>
        </p>
      </div>
    </div>

  </div>
</body>
</html>
