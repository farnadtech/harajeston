<div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg shadow-lg p-6" dir="rtl">
    <h3 class="text-lg font-bold mb-4">موجودی کیف پول</h3>
    
    <div class="space-y-3">
        <div>
            <p class="text-sm opacity-90 mb-1">موجودی قابل استفاده</p>
            <p class="text-3xl font-bold">
                @price($available)
                <span class="text-base mr-2">ریال</span>
            </p>
        </div>
        
        @if($frozen > 0)
            <div class="border-t border-white/20 pt-3">
                <p class="text-sm opacity-90 mb-1">موجودی مسدود شده</p>
                <p class="text-xl font-bold">
                    @price($frozen)
                    <span class="text-sm mr-2">ریال</span>
                </p>
            </div>
        @endif
    </div>
    
    <a href="{{ route('wallet.show') }}" class="block mt-4 text-center bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded">
        مشاهده جزئیات
    </a>
</div>
