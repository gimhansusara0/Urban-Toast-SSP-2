@extends('admin.layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>

    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button class="rounded-xl bg-red-600 hover:bg-red-500 text-white px-4 py-2">
        Logout
      </button>
    </form>
  </div>

  {{-- Right-side content area (tabs live here) --}}
  <livewire:admin.panel />
@endsection
