<footer class="footer">
    <div class="footer_inner bg-feature">
        <div class="container">
            <div class="footer_heading flex flex-wrap items-center justify-between gap-4 w-full md:pt-10 pt-7 md:pb-5 pb-4 border-b border-light">
                <a href="{{ url('/') }}" class="footer_logo">
                    <img src="{{ asset('assets/images/logo-white.png') }}" alt="logo-white" class="h-[42px] w-auto" />
                </a>
            </div>
            <div class="footer_content grid md:grid-cols-3 gap-8 md:py-10 py-7">
                <div>
                    <strong class="nav_heading text-button-sm text-white">For Candidates</strong>
                    <ul class="list_nav flex flex-col gap-3 mt-4">
                        @foreach(config('navigation.footer.for_candidates') as $link)
                            <li><a class="caption1 text-placehover hover:text-white duration-300" href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <strong class="nav_heading text-button-sm text-white">For Employers</strong>
                    <ul class="list_nav flex flex-col gap-3 mt-4">
                        @foreach(config('navigation.footer.for_employers') as $link)
                            <li><a class="caption1 text-placehover hover:text-white duration-300" href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <strong class="nav_heading text-button-sm text-white">Pages</strong>
                    <ul class="list_nav flex flex-col gap-3 mt-4">
                        @foreach(config('navigation.footer.pages') as $link)
                            <li>
                                <a class="caption1 text-placehover hover:text-white duration-300" href="{{ isset($link['route']) ? route($link['route']) : url($link['url']) }}">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="footer_bottom py-3 border-t border-light">
                <div class="copyright text-placehover caption1">&copy;2026 FreelanHub. All Rights Reserved.</div>
            </div>
        </div>
    </div>
</footer>
