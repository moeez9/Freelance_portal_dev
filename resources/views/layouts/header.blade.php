@if(request()->routeIs('admin.*'))
<header id="header" class="header -style-white sticky top-0 z-[80] bg-white">
    <div class="header_inner absolute flex items-center justify-center top-0 left-0 right-0 z-[80] w-full sm:h-20 h-16 min-[1600px]:px-15 lg:px-9 px-4 border-b border-light bg-white">
        <a href="{{ url('/') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="logo-black md:h-[42px] h-8 w-auto" />
        </a>
    </div>
</header>
@else
<header id="header" class="header -style-white sticky top-0 z-[80] bg-white"
        x-data="{
            mobileMenuOpen: false,
            notificationOpen: false,
            openMobileMenu() { this.mobileMenuOpen = true; document.body.classList.add('overflow-hidden'); },
            closeMobileMenu() { this.mobileMenuOpen = false; document.body.classList.remove('overflow-hidden'); }
        }"
        @keydown.escape.window="closeMobileMenu()">
    <div class="header_inner absolute flex items-center justify-between top-0 left-0 right-0 z-[80] w-full sm:h-20 h-16 min-[1600px]:px-15 lg:px-9 px-4 border-b border-light bg-white">
        <h1>
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="logo-black md:h-[42px] h-8 w-auto" />
            </a>
        </h1>

        <div class="right flex items-center gap-6 h-full">
            <nav class="navigator h-full max-md:hidden">
                <ul class="list flex items-center gap-5 h-full">
                    @foreach(config('navigation.primary') as $item)
                        @if(!isset($item['auth']) || auth()->check())
                            <li>
                                <a href="{{ isset($item['route']) ? route($item['route']) : url($item['url']) }}" class="text-title hover:text-primary duration-300">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach

                    @auth
                        @php
                            $roleLinks = auth()->user()->role === 'candidate'
                                ? config('navigation.candidate')
                                : config('navigation.employer');
                            $roleTitle = auth()->user()->role === 'candidate' ? 'Freelancer Menu' : 'Employer Menu';
                        @endphp
                        <li class="h-full relative group">
                            <a href="#!" class="flex items-center gap-1 h-full text-title group-hover:text-primary duration-300">
                                <span>{{ $roleTitle }}</span>
                                <span class="ph-bold ph-caret-down"></span>
                            </a>
                            <div class="absolute hidden group-hover:block p-3 -left-10 w-max bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                                <ul>
                                    @foreach($roleLinks as $link)
                                        @if(isset($link['route']) && Route::has($link['route']))
                                            <li><a href="{{ route(\Illuminate\Support\Arr::get($link, 'route')) }}" class="link block text-button py-2 px-6 rounded hover:bg-gray-50">{{ $link['label'] }}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endauth
                </ul>
            </nav>

            <div class="list_action flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-title hover:text-primary duration-300 font-bold">Login</a>
                    <a href="{{ route('register') }}" class="button-main py-2 px-6 rounded-full">Register</a>
                @else
                    <div class="relative" @click.outside="notificationOpen = false">
                        <button type="button" @click="notificationOpen = !notificationOpen" class="relative text-title hover:text-primary duration-300">
                            <span class="ph-bold ph-bell text-xl"></span>
                            @if(($headerUnreadNotificationCount ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-1.5 min-w-4 h-4 px-1 rounded-full bg-red text-white text-[10px] leading-4 text-center">
                                    {{ $headerUnreadNotificationCount > 9 ? '9+' : $headerUnreadNotificationCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="notificationOpen" x-cloak x-transition class="absolute right-0 mt-2 w-[340px] max-w-[85vw] bg-white border border-line rounded-lg shadow-xl z-[120]">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-line">
                                <strong class="text-title">Notifications</strong>
                                <a href="{{ route('notifications.index') }}" class="text-primary text-sm">View all</a>
                            </div>
                            <ul class="max-h-[320px] overflow-y-auto">
                                @forelse(($headerNotifications ?? collect()) as $notification)
                                    <li class="border-b border-line last:border-b-0 {{ $notification->is_read ? '' : 'bg-surface/70' }}">
                                        <a href="{{ route('notifications.read', $notification) }}" class="block px-4 py-3 hover:bg-surface duration-200">
                                            <p class="text-sm font-semibold text-title">{{ $notification->title }}</p>
                                            <p class="text-xs text-secondary mt-1 line-clamp-2">{{ $notification->message }}</p>
                                            <p class="text-[11px] text-secondary mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-6 text-sm text-secondary text-center">No data found</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <a href="{{ route('messages.index') }}" class="text-title hover:text-primary duration-300 hidden sm:block">Inbox</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-title hover:text-primary duration-300">
                            <span class="ph-bold ph-sign-out text-xl"></span>
                        </button>
                    </form>
                @endguest

                <button @click="mobileMenuOpen ? closeMobileMenu() : openMobileMenu()"
                        :aria-expanded="mobileMenuOpen"
                        aria-controls="mobile-drawer-menu"
                        class="md:hidden text-3xl">
                    <span :class="mobileMenuOpen ? 'ph-bold ph-x' : 'ph-bold ph-list'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen"
         id="mobile-drawer-menu"
         x-transition.opacity
         class="md:hidden fixed inset-0 z-[100]">
        <div class="absolute inset-0 bg-black/35" @click="closeMobileMenu()"></div>
        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-x-6"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-6"
             class="absolute top-0 left-0 h-full min-[320px]:w-[280px] w-[80vw] bg-white shadow-2xl p-4 overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                <h2 class="text-base font-bold text-gray-800">Menu</h2>
                <button type="button" @click="closeMobileMenu()" class="text-2xl text-gray-600 hover:text-gray-900">
                    <span class="ph-bold ph-x"></span>
                </button>
            </div>
            @include('layouts.mobile_menu')
        </div>
    </div>
</header>
@endif
