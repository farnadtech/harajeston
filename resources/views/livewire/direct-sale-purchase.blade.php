<div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
    <h3 class="text-xl font-bold mb-4">خرید مستقیم</h3>
    
    <div class="mb-4">
        <p class="text-gray-600 mb-2">قیمت:</p>
        <p class="text-3xl font-bold text-green-600">
            @currency($listing->price)
        </p>
    </div>

    <div class="mb-4">
        <p class="text-gray-600 mb-2">موجودی انبار:</p>
        @if($stock > 0)
            <p class="text-lg font-bold text-blue-600">
                @persian($stock) عدد
            </p>
        @else
            <p class="text-lg font-bold text-red-600">
                ناموجود
            </p>
        @endif
    </div>

    @if($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errorMessage }}
        </div>
    @endif

    @if($stock > 0)
        <div class="mb-4">
            <label for="quantity" class="block text-gray-700 mb-2">تعداد:</label>
            <input 
                type="number" 
                id="quantity"
                wire:model="quantity"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                min="1"
                max="{{ $stock }}"
            >
            @error('quantity') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <div class="space-y-3">
            <button 
                wire:click="addToCart"
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="addToCart">افزودن به سبد خرید</span>
                <span wire:loading wire:target="addToCart">در حال افزودن...</span>
            </button>

            <button 
                wire:click="buyNow"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="buyNow">خرید سریع</span>
                <span wire:loading wire:target="buyNow">در حال پردازش...</span>
            </button>
        </div>
    @else
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded">
            این محصول در حال حاضر موجود نیست
        </div>
    @endif
</div>
