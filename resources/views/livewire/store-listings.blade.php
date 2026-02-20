<div dir="rtl">
    <div class="mb-6 flex gap-3">
        <button 
            wire:click="setFilter('all')"
            class="px-4 py-2 rounded-lg transition {{ $filterType === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
        >
            همه
        </button>
        <button 
            wire:click="setFilter('auction')"
            class="px-4 py-2 rounded-lg transition {{ $filterType === 'auction' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
        >
            مزایده
        </button>
        <button 
            wire:click="setFilter('direct_sale')"
            class="px-4 py-2 rounded-lg transition {{ $filterType === 'direct_sale' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
        >
            فروش مستقیم
        </button>
        <button 
            wire:click="setFilter('hybrid')"
            class="px-4 py-2 rounded-lg transition {{ $filterType === 'hybrid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
        >
            ترکیبی
        </button>
    </div>

    @if($listings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($listings as $listing)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    @if($listing->images->count() > 0)
                        <img 
                            src="{{ url('storage/' . $listing->images->first()->file_path) }}" 
                            alt="{{ $listing->title }}"
                            class="w-full h-48 object-cover"
                        >
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">بدون تصویر</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <div class="mb-2">
                            @if($listing->type === 'auction')
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">مزایده</span>
                            @elseif($listing->type === 'direct_sale')
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">فروش مستقیم</span>
                            @else
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">ترکیبی</span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold mb-2">{{ $listing->title }}</h3>
                        
                        @if($listing->type === 'auction' || $listing->type === 'hybrid')
                            <p class="text-gray-600 mb-2">
                                قیمت پایه: <span class="font-bold">@currency($listing->base_price)</span>
                            </p>
                        @endif

                        @if($listing->type === 'direct_sale' || $listing->type === 'hybrid')
                            <p class="text-gray-600 mb-2">
                                قیمت: <span class="font-bold text-green-600">@currency($listing->price)</span>
                            </p>
                        @endif

                        <a 
                            href="{{ route('listings.show', $listing->id) }}"
                            class="block text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                        >
                            مشاهده جزئیات
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500 text-lg">هیچ آگهی فعالی یافت نشد</p>
        </div>
    @endif
</div>
