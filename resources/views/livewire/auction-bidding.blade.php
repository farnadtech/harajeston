<div class="space-y-4">
    @if($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ $errorMessage }}
        </div>
    @endif

    @if($listing->status === 'active')
        <form wire:submit.prevent="placeBid">
            <label class="block text-sm font-bold text-gray-700 mb-3">پیشنهاد خود را وارد کنید</label>
            <div class="relative mb-4">
                <input 
                    type="number" 
                    wire:model="bidAmount"
                    class="block w-full text-left ltr h-14 pr-4 pl-16 bg-white border-2 border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-primary text-xl font-bold transition-colors"
                    placeholder="{{ number_format($currentHighestBid + 100000) }}"
                    min="{{ $currentHighestBid + 1 }}"
                    step="1000"
                />
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-medium pointer-events-none">تومان</span>
            </div>
            @error('bidAmount') 
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror
            
            <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar mb-4">
                <button type="button" wire:click="incrementBid(50000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                    + 50,000
                </button>
                <button type="button" wire:click="incrementBid(100000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                    + 100,000
                </button>
                <button type="button" wire:click="incrementBid(200000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                    + 200,000
                </button>
            </div>
            
            <button 
                type="submit"
                class="w-full h-14 bg-primary hover:bg-blue-600 text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all transform active:scale-[0.99]"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove class="material-symbols-outlined">gavel</span>
                <span wire:loading.remove>ثبت پیشنهاد</span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined animate-spin">progress_activity</span>
                    در حال ثبت...
                </span>
            </button>
            
            <p class="text-xs text-center text-gray-500 mt-2">
                با ثبت پیشنهاد، <a class="text-primary hover:underline" href="#">قوانین مزایده</a> را می‌پذیرید.
            </p>
        </form>
    @else
        <div class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl text-center">
            مزایده پایان یافته است
        </div>
    @endif
</div>
