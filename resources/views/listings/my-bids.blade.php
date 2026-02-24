<x-dashboard-layout>
    <x-slot name="title">پیشنهادات من</x-slot>
    <x-slot name="pageTitle">مزایده‌هایی که شرکت کرده‌ام</x-slot>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="flex items-center gap-2 p-2">
            <a href="{{ route('my-bids', ['status' => 'all']) }}" 
               class="px-6 py-3 rounded-xl font-medium transition-colors {{ request('status', 'all') === 'all' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                همه ({{ \App\Services\PersianNumberService::convertToPersian($counts['all']) }})
            </a>
            <a href="{{ route('my-bids', ['status' => 'active']) }}" 
               class="px-6 py-3 rounded-xl font-medium transition-colors {{ request('status') === 'active' ? 'bg-green-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                فعال ({{ \App\Services\PersianNumberService::convertToPersian($counts['active']) }})
            </a>
            <a href="{{ route('my-bids', ['status' => 'completed']) }}" 
               class="px-6 py-3 rounded-xl font-medium transition-colors {{ request('status') === 'completed' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                تمام شده ({{ \App\Services\PersianNumberService::convertToPersian($counts['completed']) }})
            </a>
        </div>
    </div>

    <!-- Listings Grid -->
    @if($listings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($listings as $listing)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-100">
                        @if($listing->images->count() > 0)
                            <img src="{{ $listing->images->first()->url }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-300 text-6xl">image</span>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        @php
                            $statusColors = [
                                'active' => 'bg-green-500',
                                'pending' => 'bg-yellow-500',
                                'completed' => 'bg-blue-500',
                                'cancelled' => 'bg-red-500',
                            ];
                            $statusLabels = [
                                'active' => 'فعال',
                                'pending' => 'در انتظار',
                                'completed' => 'تمام شده',
                                'cancelled' => 'لغو شده',
                            ];
                        @endphp
                        <span class="absolute top-3 right-3 px-3 py-1 {{ $statusColors[$listing->status] ?? 'bg-gray-500' }} text-white text-xs font-bold rounded-full">
                            {{ $statusLabels[$listing->status] ?? $listing->status }}
                        </span>

                        <!-- Winner Badge -->
                        @if($listing->status === 'completed' && $listing->current_winner_id === auth()->id())
                            <span class="absolute top-3 left-3 px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">emoji_events</span>
                                برنده
                            </span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $listing->title }}</h3>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <span class="material-symbols-outlined text-lg">store</span>
                            <span>{{ $listing->seller->name }}</span>
                        </div>

                        <!-- My Bid Info -->
                        @if($listing->my_bid)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600">پیشنهاد من:</span>
                                    <span class="text-sm font-bold text-blue-600">@price($listing->my_bid->amount) تومان</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">بالاترین پیشنهاد:</span>
                                    <span class="text-sm font-bold text-gray-900">@price($listing->current_price) تومان</span>
                                </div>
                            </div>
                        @endif

                        <!-- Price & Bids -->
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs text-gray-500">قیمت فعلی</p>
                                <p class="text-lg font-bold text-primary">@price($listing->current_price) تومان</p>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-gray-500">تعداد پیشنهادات</p>
                                <p class="text-lg font-bold text-gray-900">{{ \App\Services\PersianNumberService::convertToPersian($listing->bids_count) }}</p>
                            </div>
                        </div>

                        <!-- Time Remaining -->
                        @if($listing->status === 'active')
                            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-orange-500">schedule</span>
                                    <span class="text-gray-600">زمان باقی‌مانده:</span>
                                    <span class="font-bold text-gray-900">{{ $listing->time_remaining }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <a href="{{ route('listings.show', $listing) }}" 
                           class="block w-full bg-primary text-white text-center py-3 rounded-xl hover:bg-blue-700 transition-colors font-medium">
                            مشاهده جزئیات
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($listings->hasPages())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                {{ $listings->links('vendor.pagination.custom') }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4 block">gavel</span>
            <h3 class="text-xl font-bold text-gray-900 mb-2">هیچ مزایده‌ای یافت نشد</h3>
            <p class="text-gray-500 mb-6">شما هنوز در هیچ مزایده‌ای شرکت نکرده‌اید</p>
            <a href="{{ route('listings.index') }}" class="inline-block bg-primary text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition-colors font-medium">
                مشاهده مزایده‌های فعال
            </a>
        </div>
    @endif
</x-dashboard-layout>
