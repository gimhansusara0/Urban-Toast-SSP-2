<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
  <form method="POST" action="{{ route('admin.login.post') }}" class="bg-white p-6 rounded-xl shadow w-full max-w-sm space-y-4">
    @csrf
    <h1 class="text-xl font-semibold">Admin Login</h1>

    @error('email')
      <div class="text-sm text-red-600">{{ $message }}</div>
    @enderror

    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" type="email" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block text-sm font-medium">Password</label>
      <input name="password" type="password" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>

    <label class="inline-flex items-center gap-2 text-sm">
      <input type="checkbox" name="remember" class="rounded">
      Remember me
    </label>

    <button class="w-full bg-gray-900 text-white py-2 rounded-lg">Login</button>
  </form>
</body>
</html>