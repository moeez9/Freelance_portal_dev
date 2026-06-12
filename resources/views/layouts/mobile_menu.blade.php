<ul class="flex flex-col gap-1">
    @foreach(config('navigation.primary') as $item)
        @if(!isset($item['auth']) || auth()->check())
            <li>
                <a href="{{ isset($item['route']) ? route($item['route']) : url($item['url']) }}"
                   @click="closeMobileMenu()"
                   class="text-title font-semibold block py-3 px-3 rounded-md hover:bg-gray-50">
                    {{ $item['label'] }}
                </a>
            </li>
        @endif
    @endforeach

    @auth
        <li>
            <a href="{{ route('notifications.index') }}"
               @click="closeMobileMenu()"
               class="text-title block py-3 px-3 rounded-md hover:bg-gray-50">Notifications</a>
        </li>
        @php
            $roleLinks = auth()->user()->role === 'candidate'
                ? config('navigation.candidate')
                : config('navigation.employer');
        @endphp
        @foreach($roleLinks as $link)
            <li>
                <a href="{{ route($link['route']) }}"
                   @click="closeMobileMenu()"
                   class="text-title block py-3 px-3 rounded-md hover:bg-gray-50">{{ $link['label'] }}</a>
            </li>
        @endforeach
    @endauth
</ul>
