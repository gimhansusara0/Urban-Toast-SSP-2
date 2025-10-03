<nav x-data="{ open: false }" class="bg-[#ebe0ce] border-b border-neutral-300/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                        <span class="inline-flex items-center justify-center h-9 w-9 rounded-md text-sm font-semibold tracking-widest">
                            <span class="text-neutral-700 font-extrabold text-2xl">U</span>
                            <span class="text-[#eb3d22] font-extrabold text-2xl">R</span>
                        </span>
                        <span class="sr-only">Urban Roast</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>

                    

                    <x-nav-link href="{{ url('/about') }}" :active="request()->is('about')">
                        {{ __('About') }}
                    </x-nav-link>

                    <x-nav-link href="{{ url('/contact') }}" :active="request()->is('contact')">
                        {{ __('Contact Us') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-3">
                {{-- Cart icon --}}
                <livewire:cart.icon />

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-neutral-700 hover:text-[#eb3d22] focus:outline-none transition">
                                <div>
                                    {{ Auth::user()->name ?? 'Account' }}
                                </div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            @auth
                                <div class="block px-4 py-2 text-xs text-neutral-500">
                                    {{ __('Signed in as') }}<br>
                                    <span class="font-semibold">{{ Auth::user()->email }}</span>
                                </div>

                                <x-dropdown-link href="{{ route('dashboard') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            @endauth

                            @guest
                                <x-dropdown-link href="{{ route('auth.role') }}">
                                    {{ __('Login') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('register') }}">
                                    {{ __('Create Account') }}
                                </x-dropdown-link>
                            @endguest
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger (mobile menu) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-neutral-600 hover:text-[#eb3d22] hover:bg-neutral-200 focus:outline-none focus:bg-neutral-200 focus:text-neutral-800 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ url('/about') }}" :active="request()->is('about')">
                {{ __('About') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ url('/contact') }}" :active="request()->is('contact')">
                {{ __('Contact Us') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-neutral-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-neutral-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-neutral-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('dashboard') }}">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endauth

            @guest
                <x-responsive-nav-link href="{{ route('auth.role') }}">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('register') }}">
                    {{ __('Create Account') }}
                </x-responsive-nav-link>
            @endguest
        </div>
    </div>
</nav>
