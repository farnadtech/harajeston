<div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
    <h3 class="text-xl font-bold mb-4">شرکت در مزایده</h3>
    
    <div class="mb-4">
        <p class="text-gray-600 mb-2">مبلغ سپرده مورد نیاز:</p>
        <p class="text-2xl font-bold text-green-600">
            @currency($listing->required_deposit)
        </p>
        <p class="text-sm text-gray-500 mt-1">
            (۱۰٪ از قیمت پایه)
        </p>
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

    @auth
        @if($hasParticipated)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>شما در این مزایده شرکت کرده‌اید</span>
            </div>
        @else
            @if($listing->status === 'active')
                <button 
                    wire:click="participate"
                    class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>شرکت در مزایده</span>
                    <span wire:loading>در حال پردازش...</span>
                </button>
                
                <p class="text-sm text-gray-500 mt-3 text-center">
                    با کلیک بر روی دکمه، مبلغ سپرده از کیف پول شما کسر می‌شود
                </p>
            @else
                <div class="bg-gray-100 text-gray-600 px-4 py-3 rounded">
                    مزایده فعال نیست
                </div>
            @endif
        @endif
    @else
        <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded">
            برای شرکت در مزایده ابتدا وارد شوید
        </div>
    @endauth
</div>
