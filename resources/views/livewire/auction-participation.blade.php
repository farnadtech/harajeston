<div class="space-y-4">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-blue-600 text-2xl">lock</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-2">شرکت در مزایده</h3>
                <p class="text-sm text-gray-700 mb-3">
                    برای شرکت در این مزایده، ابتدا باید مبلغ سپرده را پرداخت کنید. این مبلغ در صورت عدم برنده شدن، به کیف پول شما بازگردانده می‌شود.
                </p>
                <div class="bg-white rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">مبلغ سپرده مورد نیاز:</span>
                        <span class="text-2xl font-black text-blue-600">
                            @price($listing->required_deposit) تومان
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        ({{ \App\Services\PersianNumberService::convertToPersian(10) }}٪ از قیمت پایه)
                    </p>
                </div>
            </div>
        </div>

        @if($successMessage)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                <span>{{ $successMessage }}</span>
            </div>
        @endif

        @if($errorMessage)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        @if($hasParticipated)
            <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
                <div class="flex-1">
                    <p class="font-bold">شما در این مزایده شرکت کرده‌اید</p>
                    <p class="text-sm mt-1">اکنون می‌توانید پیشنهادات خود را ثبت کنید</p>
                </div>
            </div>
        @else
            @if($listing->status === 'active')
                <button 
                    wire:click="participate"
                    class="w-full h-14 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all transform active:scale-[0.99]"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove class="material-symbols-outlined">how_to_reg</span>
                    <span wire:loading.remove>پرداخت سپرده و شرکت در مزایده</span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                        در حال پردازش...
                    </span>
                </button>
                
                <p class="text-xs text-center text-gray-600 mt-3">
                    با کلیک بر روی دکمه، مبلغ سپرده از کیف پول شما کسر می‌شود
                </p>
            @else
                <div class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl text-center">
                    مزایده فعال نیست
                </div>
            @endif
        @endif
    </div>
</div>
