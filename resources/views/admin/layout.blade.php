<!doctype html>
<html lang="en">
<head>
  
  <meta charset="utf-8">
  <title>Admin • Urban Roast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles
</head>
<body class="min-h-screen bg-neutral-100 text-neutral-900">
  <div class="mx-auto max-w-7xl p-4 md:p-6">
    @yield('content')
  </div>

  @livewireScripts
</body>
</html>
