<div class="relative" dir="rtl" x-data="{ open: false }">
    <button 
        @click="open = !open"
        class="relative p-2 text-gray-700 hover:text-blue-600 transition"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        
        @if($itemCount > 0)
            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                @persian($itemCount)
            </span>
        @endif
    </button>

    <div 
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50"
    >
        @if($itemCount > 0)
            <div class="p-4">
                <h3 class="text-lg font-bold mb-3">سبد خرید</h3>
                
                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    @foreach($items as $item)
                        <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                            <div class="flex-1">
                                <p class="font-medium text-sm">{{ $item['listing']['title'] ?? 'محصول' }}</p>
                                <p class="text-xs text-gray-600">تعداد: @persian($item['quantity'])</p>
                            </div>
                            <p class="text-sm font-bold">
                                @currency($item['price_snapshot'] * $item['quantity'])
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-3 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold">جمع کل:</span>
                        <span class="text-lg font-bold text-green-600">
                            @currency($total)
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <a 
                        href="{{ route('cart.index') }}"
                        class="block text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                    >
                        مشاهده سبد خرید
                    </a>
                    <a 
                        href="{{ route('checkout.show') }}"
                        class="block text-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
                    >
                        تسویه حساب
                    </a>
                </div>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                سبد خرید شما خالی است
            </div>
        @endif
    </div>
</div>
