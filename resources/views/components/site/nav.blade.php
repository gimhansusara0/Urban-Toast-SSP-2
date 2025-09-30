<header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 ">
  <nav class="flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
      <span class="inline-flex items-center justify-center h-9 w-9 rounded-md text-sm font-semibold tracking-widest">
        <span class="text-neutral-700 font-extrabold text-2xl">U</span><span class="text-[#eb3d22] font-extrabold text-2xl">R</span>
      </span>
      <span class="sr-only">Urban Roast</span>
    </a>

    {{-- Center nav --}}
    <ul class="hidden md:flex items-center gap-8 text-sm">
      <li class="relative">
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'font-medium' : 'text-neutral-600 hover:text-[#eb3d22]' }} transition">
          Home
        </a>
        @if (request()->routeIs('home'))
          <span class="absolute left-0 -bottom-2 h-0.5 w-7 bg-[#eb3d22] rounded-full"></span>
        @endif
      </li>
      <li>
        <a href="{{ url('/shop') }}"
           class="{{ request()->is('shop*') ? 'text-[#eb3d22] font-medium' : 'text-neutral-600 hover:text-[#eb3d22]' }} transition">
          Menu
        </a>
      </li>
      <li>
        <a href="{{ url('/about') }}"
           class="{{ request()->is('about') ? 'text-[#eb3d22] font-medium' : 'text-neutral-600 hover:text-[#eb3d22]' }} transition">
          About
        </a>
      </li>
      <li>
        <a href="{{ url('/contact') }}"
           class="{{ request()->is('contact') ? 'text-[#eb3d22] font-medium' : 'text-neutral-600 hover:text-[#eb3d22]' }} transition">
          Contact Us
        </a>
      </li>
    </ul>

    {{-- Right: Cart & Profile/Account dropdown --}}
    <div class="flex items-center gap-2">
      <livewire:cart.icon />
      <div class="relative">
        @auth
          <details class="group relative">
            <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
              <div class="h-9 w-9 rounded-full bg-white border border-neutral-300 flex items-center justify-center">
                <span class="text-xs font-bold">
                  {{ strtoupper(mb_substr(Auth::user()->name,0,1)) }}
                </span>
              </div>
              <span class="hidden sm:block text-sm font-medium">
                {{ \Illuminate\Support\Str::limit(Auth::user()->name, 18) }}
              </span>
              <svg class="h-4 w-4 text-neutral-500 group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </summary>
            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow border border-neutral-200 p-2 z-50">
              <div class="px-3 py-2 text-xs text-neutral-500">Signed in as</div>
              <div class="px-3 pb-2 text-sm font-medium">{{ Auth::user()->email }}</div>
              <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-neutral-100 text-sm">Profile</a>
              <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left block px-3 py-2 rounded-lg hover:bg-neutral-100 text-sm text-red-600">
                  Logout
                </button>
              </form>
            </div>
          </details>
        @endauth
        @guest
          <details class="group relative">
            <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
              <div class="h-9 w-9 rounded-full bg-white border border-neutral-300 flex items-center justify-center">
                <svg class="h-4 w-4 text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0"/>
                </svg>
              </div>
              <span class="hidden sm:block text-sm font-medium">Account</span>
              <svg class="h-4 w-4 text-neutral-500 group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </summary>
            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow border border-neutral-200 p-2 z-50">
              <a href="{{ route('auth.role') }}" class="block px-3 py-2 rounded-lg hover:bg-neutral-100 text-sm">Login</a>
              <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg hover:bg-neutral-100 text-sm">Create account</a>
            </div>
          </details>
        @endguest
      </div>
    </div>
  </nav>
</header>