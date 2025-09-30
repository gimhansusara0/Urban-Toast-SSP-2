@props([
  'title' => 'Urban Roast'
])
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head') {{-- optional per-page head additions --}}
</head>
<body class="min-h-screen bg-[#ebe0ce] text-neutral-800 antialiased">

  {{-- Global navbar --}}
  <x-site.nav />

  {{-- Page content --}}
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{ $slot }}
  </main>

  {{-- Footer --}}
  <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pb-10">
    <div class="h-px w-full bg-neutral-300/70 mb-4"></div>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-neutral-600">
      <div class="flex items-center gap-6">
        <a class="hover:text-[#DB6246]" href="#" aria-label="Instagram">Instagram</a>
        <a class="hover:text-[#DB6246]" href="#" aria-label="Facebook">Facebook</a>
        <a class="hover:text-[#DB6246]" href="#" aria-label="Twitter">Twitter</a>
      </div>
      <p>© {{ date('Y') }} Urban Roast</p>
    </div>
  </footer>

  @stack('scripts') {{-- optional per-page scripts --}}
</body>
</html>
