@extends('layouts.app')

@section('content')
<section class="services_detail lg:pb-20 sm:pb-14 pb-10 pt-32">
    <div class="container flex max-lg:flex-col gap-y-10">
        <div class="services_content w-full lg:w-2/3 lg:pr-15">
            <div class="flex flex-col gap-4 pb-10 border-b border-line">
                <div class="flex items-center gap-2 text-sm text-gray-500 font-bold uppercase tracking-wider">
                    <span>{{ $gig->category }}</span>
                    <span class="ph ph-caret-right"></span>
                    <span>{{ $gig->sub_category }}</span>
                </div>
                <h3 class="services_name heading3">{{ $gig->title }}</h3>
                <div class="flex items-center gap-4">
                    <div class="overflow-hidden w-12 h-12 rounded-full">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($gig->freelancer->name) }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <span class="font-bold">{{ $gig->freelancer->name }}</span>
                        <span class="text-secondary block text-sm">{{ $gig->service_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Media (Video or Image) -->
            <div class="images mt-8">
                @if($gig->video_path)
                    <video controls class="w-full rounded-xl shadow-lg aspect-video">
                        <source src="{{ asset('storage/' . $gig->video_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div class="relative max-w-[600px] w-full">
                        <img
                            src="{{ $gig->thumbnail ? asset('storage/' . $gig->thumbnail) : asset('assets/images/blog/1.webp') }}"
                            data-gallery-item
                            data-index="0"
                            class="w-full h-[220px] sm:h-[260px] lg:h-[300px] object-cover rounded-xl shadow-lg cursor-pointer"
                        >
                        <button type="button" data-gallery-open-main class="absolute top-3 right-3 bg-black/70 text-white text-xs font-semibold px-3 py-1.5 rounded">
                            Full View
                        </button>
                    </div>
                @endif
            </div>

            <!-- Gallery Images -->
            @if($gig->gallery && count($gig->gallery) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4">
                    @foreach($gig->gallery as $index => $image)
                        <img
                            src="{{ asset('storage/' . $image) }}"
                            data-gallery-item
                            data-index="{{ $gig->video_path ? $index : $index + 1 }}"
                            class="w-full h-32 object-cover rounded-lg shadow cursor-pointer hover:opacity-80 transition"
                        >
                    @endforeach
                </div>
            @endif

            <div class="description mt-10">
                <h6 class="heading6">About This Gig</h6>
                <p class="mt-3 body2 text-secondary whitespace-pre-line">{{ $gig->description }}</p>
            </div>

            <!-- Tags -->
            @if($gig->search_tags && count($gig->search_tags) > 0)
                <div class="mt-10">
                    <h6 class="heading6 mb-3">Related Tags</h6>
                    <div class="flex flex-wrap gap-2">
                        @foreach($gig->search_tags as $tag)
                            <span class="bg-gray-100 text-gray-600 px-4 py-1 rounded-full text-sm font-bold border border-gray-200">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Requirements -->
            @if(($gig->requirements && $gig->requirements->count() > 0) || ($gig->requirement_questions && count($gig->requirement_questions) > 0))
                <div class="mt-10 p-6 bg-gray-50 rounded-xl">
                    <h6 class="heading6 mb-3">Requirements from Buyer</h6>
                    <ul class="space-y-3">
                        @php
                            $reqList = ($gig->requirements && $gig->requirements->count() > 0)
                                ? $gig->requirements->pluck('question')->all()
                                : $gig->requirement_questions;
                        @endphp
                        @foreach($reqList as $req)
                            <li class="flex gap-3 text-gray-600">
                                <span class="text-primary font-bold">•</span>
                                <span>{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Documents -->
            @if($gig->document_paths && count($gig->document_paths) > 0)
                <div class="mt-10">
                    <h6 class="heading6 mb-3">Documents</h6>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($gig->document_paths as $doc)
                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <span class="ph ph-file-pdf text-3xl text-red-500"></span>
                                <span class="text-sm font-bold text-gray-700 truncate">View Document</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="sidebar -relative w-full lg:w-1/3">
            <div
                x-data="{
                    activePackage: '{{ $selectedPackage->type ?? ($packages->first()->type ?? 'basic') }}',
                    activePackageId: {{ (int) ($selectedPackage->id ?? ($packages->first()->id ?? 0)) }}
                }"
                class="bg-white rounded-lg shadow-xl border border-line overflow-hidden sticky top-24"
            >
                @if($packages->count() > 0)
                    <div class="flex border-b border-line">
                        @foreach($packages as $package)
                            <button type="button" @click="activePackage = '{{ $package->type }}'; activePackageId = {{ (int) $package->id }}"
                                    :class="activePackage === '{{ $package->type }}' ? 'border-b-2 border-[#04b2b2] text-[#04b2b2]' : 'text-gray-500'"
                                    class="flex-1 py-4 font-bold uppercase text-sm transition-all">
                                {{ $package->type }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="p-6">
                    @forelse($packages as $package)
                        <div x-show="activePackage === '{{ $package->type }}'" class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-lg">{{ $package->name }}</h4>
                                <span class="text-2xl font-bold text-[#04b2b2]">${{ $package->price }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $package->description }}</p>
                            <div class="flex items-center gap-4 text-sm font-bold text-gray-700">
                                <span class="flex items-center gap-1"><span class="ph ph-clock"></span> {{ $package->delivery_days ?? $package->delivery_time }} Days Delivery</span>
                                <span class="flex items-center gap-1"><span class="ph ph-arrows-clockwise"></span> {{ $package->revisions ?? 0 }} Revisions</span>
                            </div>

                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No packages are available for this gig yet.</p>
                    @endforelse

                    @if($packages->count() > 0)
                        @auth
                            @if(Auth::user()->role === 'employer')
                                <form action="{{ route('orders.store', $gig->slug) }}" method="POST" class="space-y-3 mt-5">
                                    @csrf
                                    <input type="hidden" name="gig_package_id" :value="activePackageId">
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Payment Method</label>
                                        <select name="payment_method" class="w-full border border-line rounded-lg h-11 px-3" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="debit_card">Debit Card</option>
                                            <option value="jazzcash">JazzCash</option>
                                            <option value="easypaisa">EasyPaisa</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Payer Name</label>
                                        <input type="text" name="payer_name" class="w-full border border-line rounded-lg h-11 px-3" placeholder="Enter payer name" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Contact / Card Last 4 / Wallet No.</label>
                                        <input type="text" name="payer_contact" class="w-full border border-line rounded-lg h-11 px-3" placeholder="e.g. 03XXXXXXXXX or ****1234" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Transaction Reference</label>
                                        <input type="text" name="transaction_reference" class="w-full border border-line rounded-lg h-11 px-3" placeholder="TXN/UTR/Ref ID" required>
                                    </div>
                                    <div class="rounded-lg border border-line p-3 text-sm bg-surface">
                                        <p><strong>Manual Payment Account:</strong></p>
                                        <p>Name: {{ config('payments.manual_admin.name') }}</p>
                                        <p>EasyPaisa: {{ config('payments.manual_admin.easypaisa_number') }}</p>
                                        <p>Email: {{ config('payments.manual_admin.email') }}</p>
                                    </div>
                                    <button type="submit" class="button-main w-full py-4 rounded-lg font-bold text-lg">Order Now</button>
                                </form>
                                @if(strtolower((string) Auth::user()->email) === strtolower((string) config('payments.manual_admin.email')))
                                    <a href="{{ route('admin.demo.payments') }}" class="button-main w-full block text-center py-3 rounded-lg font-bold text-base bg-slate-700 hover:bg-slate-800 mt-3">
                                        Open Admin Demo Payments
                                    </a>
                                @endif
                                <form action="{{ route('conversations.store') }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="context_type" value="gig">
                                    <input type="hidden" name="context_id" value="{{ $gig->id }}">
                                    <button type="submit" class="button-main w-full py-3 rounded-lg font-bold text-base bg-gray-600 hover:bg-gray-700">Message Seller</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="button-main w-full block text-center py-4 rounded-lg font-bold text-lg mt-5">Login to Order</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if(!$gig->video_path || ($gig->gallery && count($gig->gallery) > 0))
    <div id="gig-gallery-modal" class="fixed inset-0 z-[120] bg-black/80 hidden items-center justify-center p-4">
        <button type="button" data-gallery-close class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white text-black flex items-center justify-center">
            <span class="ph ph-x text-xl"></span>
        </button>
        <button type="button" data-gallery-prev class="absolute left-4 md:left-8 w-11 h-11 rounded-full bg-white text-black flex items-center justify-center">
            <span class="ph ph-caret-left text-2xl"></span>
        </button>
        <img id="gig-gallery-modal-image" src="" alt="Gig preview" class="max-w-[95vw] max-h-[85vh] rounded-lg shadow-2xl object-contain">
        <button type="button" data-gallery-next class="absolute right-4 md:right-8 w-11 h-11 rounded-full bg-white text-black flex items-center justify-center">
            <span class="ph ph-caret-right text-2xl"></span>
        </button>
    </div>
@endif
@endsection

@push('scripts')
@if(!$gig->video_path || ($gig->gallery && count($gig->gallery) > 0))
<script>
    (function () {
        const modal = document.getElementById('gig-gallery-modal');
        if (!modal) return;

        const imageEl = document.getElementById('gig-gallery-modal-image');
        const thumbs = Array.from(document.querySelectorAll('[data-gallery-item]'));
        const closeBtn = modal.querySelector('[data-gallery-close]');
        const prevBtn = modal.querySelector('[data-gallery-prev]');
        const nextBtn = modal.querySelector('[data-gallery-next]');
        const mainOpenBtn = document.querySelector('[data-gallery-open-main]');
        const images = thumbs.map((thumb) => thumb.getAttribute('src'));
        let currentIndex = 0;

        const render = () => {
            imageEl.src = images[currentIndex] || '';
        };

        const openModal = (index) => {
            currentIndex = index;
            render();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        const prev = () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            render();
        };

        const next = () => {
            currentIndex = (currentIndex + 1) % images.length;
            render();
        };

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => openModal(index));
        });
        if (mainOpenBtn) {
            mainOpenBtn.addEventListener('click', () => openModal(0));
        }
        prevBtn.addEventListener('click', prev);
        nextBtn.addEventListener('click', next);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (modal.classList.contains('hidden')) return;
            if (event.key === 'Escape') closeModal();
            if (event.key === 'ArrowLeft') prev();
            if (event.key === 'ArrowRight') next();
        });
    })();
</script>
@endif
@endpush
