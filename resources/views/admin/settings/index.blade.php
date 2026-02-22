@extends('layouts.admin')

@section('title', 'تنظیمات سایت')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">تنظیمات سایت</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- تنظیمات سپرده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات سپرده شرکت در مزایده</h2>
            
            <form action="{{ route('admin.settings.deposit.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع محاسبه سپرده</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="deposit_type" value="fixed" 
                                   {{ $depositSettings['type'] === 'fixed' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>مبلغ ثابت</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="deposit_type" value="percentage" 
                                   {{ $depositSettings['type'] === 'percentage' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>درصد از قیمت پایه</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="deposit_fixed_amount" class="block text-gray-700 font-bold mb-2">
                        مبلغ ثابت سپرده (تومان)
                    </label>
                    <input type="number" 
                           id="deposit_fixed_amount" 
                           name="deposit_fixed_amount" 
                           value="{{ $depositSettings['fixed_amount'] }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0">
                </div>

                <div class="mb-6">
                    <label for="deposit_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سپرده (%)
                    </label>
                    <input type="number" 
                           id="deposit_percentage" 
                           name="deposit_percentage" 
                           value="{{ $depositSettings['percentage'] }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات سپرده
                </button>
            </form>
        </div>

        <!-- تنظیمات مدت زمان حراجی -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات مدت زمان حراجی</h2>
            
            <form action="{{ route('admin.settings.auction-duration.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="force_auction_duration" 
                               value="1"
                               id="force_auction_duration"
                               {{ $auctionDurationSettings['force_duration'] ?? false ? 'checked' : '' }}
                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                               onchange="toggleDurationFields()">
                        <div>
                            <span class="font-bold text-gray-700">اجبار مدت زمان ثابت برای حراجی‌ها</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، فروشندگان نمی‌توانند زمان پایان حراجی را خودشان انتخاب کنند و مدت زمان به صورت خودکار محاسبه می‌شود.
                            </p>
                        </div>
                    </label>
                </div>

                <div id="duration-fields" class="{{ ($auctionDurationSettings['force_duration'] ?? false) ? '' : 'opacity-50 pointer-events-none' }}">
                    <div class="mb-6">
                        <label for="auction_duration_days" class="block text-gray-700 font-bold mb-2">
                            مدت زمان حراجی (روز)
                        </label>
                        <input type="number" 
                               id="auction_duration_days" 
                               name="auction_duration_days" 
                               value="{{ $auctionDurationSettings['duration_days'] ?? 7 }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               min="1"
                               max="365">
                        <p class="text-sm text-gray-600 mt-2">
                            حراجی‌ها به صورت خودکار X روز بعد از زمان شروع به پایان می‌رسند.
                        </p>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات مدت زمان
                </button>
            </form>
        </div>

        <!-- تنظیمات فروشندگان -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات فروشندگان</h2>
            
            <form action="{{ route('admin.settings.seller.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="require_seller_approval" 
                               value="1"
                               {{ $sellerSettings['require_approval'] ? 'checked' : '' }}
                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نیاز به تایید دستی فروشندگان</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، درخواست‌های فروشندگی باید توسط ادمین تایید شوند. در غیر این صورت، کاربران بلافاصله پس از درخواست فعال می‌شوند.
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات فروشندگان
                </button>
            </form>
        </div>

        <!-- تنظیمات آگهی‌ها -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات آگهی‌ها</h2>
            
            <form action="{{ route('admin.settings.listing.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="require_listing_approval" 
                               value="1"
                               {{ ($listingSettings['require_approval'] ?? true) ? 'checked' : '' }}
                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نیاز به تایید دستی آگهی‌ها</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، تمام آگهی‌های جدید باید توسط ادمین تایید شوند تا منتشر شوند. در غیر این صورت، آگهی‌ها بلافاصله پس از ثبت منتشر می‌شوند.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="default_show_before_start" 
                               value="1"
                               {{ ($listingSettings['default_show_before_start'] ?? false) ? 'checked' : '' }}
                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نمایش پیش‌فرض حراجی‌ها قبل از شروع</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، حراجی‌های جدید به صورت پیش‌فرض در لیست‌های سایت قبل از زمان شروع نمایش داده می‌شوند (با برچسب "هنوز شروع نشده"). فروشندگان می‌توانند این تنظیم را برای هر حراجی تغییر دهند.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 text-xl">info</span>
                        <div class="text-sm text-yellow-800">
                            <p class="font-bold mb-1">توجه:</p>
                            <p>اگر گزینه "نیاز به تایید دستی" را غیرفعال کنید، آگهی‌های جدید بدون بررسی منتشر می‌شوند. توصیه می‌شود این گزینه را فعال نگه دارید تا کیفیت آگهی‌ها کنترل شود.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات آگهی‌ها
                </button>
            </form>
        </div>

        <!-- تنظیمات کمیسیون -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات کمیسیون سایت</h2>
            
            <form action="{{ route('admin.settings.commission.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع محاسبه کمیسیون</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="fixed" 
                                   {{ $commissionSettings['type'] === 'fixed' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>مبلغ ثابت</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="percentage" 
                                   {{ $commissionSettings['type'] === 'percentage' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>درصد از قیمت نهایی</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="category" 
                                   {{ $commissionSettings['type'] === 'category' ? 'checked' : '' }}
                                   class="ml-2">
                            <div>
                                <span>بر اساس دسته‌بندی</span>
                                <p class="text-sm text-gray-600 mt-1">
                                    کمیسیون بر اساس دسته‌بندی محصول محاسبه می‌شود. می‌توانید برای هر دسته‌بندی کمیسیون جداگانه تعریف کنید.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="commission_fixed_amount" class="block text-gray-700 font-bold mb-2">
                        مبلغ ثابت کمیسیون (تومان)
                    </label>
                    <input type="number" 
                           id="commission_fixed_amount" 
                           name="commission_fixed_amount" 
                           value="{{ $commissionSettings['fixed_amount'] }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0">
                </div>

                <div class="mb-6">
                    <label for="commission_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد کمیسیون (%)
                    </label>
                    <input type="number" 
                           id="commission_percentage" 
                           name="commission_percentage" 
                           value="{{ $commissionSettings['percentage'] }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">پرداخت کننده کمیسیون</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="buyer" 
                                   {{ $commissionSettings['payer'] === 'buyer' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>خریدار</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="seller" 
                                   {{ $commissionSettings['payer'] === 'seller' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>فروشنده</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="both" 
                                   {{ $commissionSettings['payer'] === 'both' ? 'checked' : '' }}
                                   class="ml-2">
                            <span>هر دو (تقسیم بین خریدار و فروشنده)</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="commission_split_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سهم خریدار از کمیسیون (%) - فقط برای حالت "هر دو"
                    </label>
                    <input type="number" 
                           id="commission_split_percentage" 
                           name="commission_split_percentage" 
                           value="{{ $commissionSettings['split_percentage'] }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                    <p class="text-sm text-gray-600 mt-2">
                        مثال: اگر 60 وارد کنید، 60% از کمیسیون از خریدار و 40% از فروشنده کسر می‌شود.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات کمیسیون
                </button>
                
                @if($commissionSettings['type'] === 'category')
                    <a href="{{ route('admin.category-commissions.index') }}" class="inline-block mr-4 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                        مدیریت کمیسیون دسته‌بندی‌ها
                    </a>
                @endif
            </form>
        </div>

        <!-- تنظیمات بازندگان مزایده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات بازندگان مزایده</h2>
            
            <form action="{{ route('admin.settings.loser-fee.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="loser_fee_enabled" 
                               value="1"
                               id="loser_fee_enabled"
                               {{ ($loserFeeSettings['enabled'] ?? false) ? 'checked' : '' }}
                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                               onchange="toggleLoserFeeFields()">
                        <div>
                            <span class="font-bold text-gray-700">کسر کارمزد از بازندگان مزایده</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، درصدی از سپرده کاربرانی که در مزایده برنده نشدند به عنوان کارمزد کسر می‌شود.
                            </p>
                        </div>
                    </label>
                </div>

                <div id="loser-fee-fields" class="{{ ($loserFeeSettings['enabled'] ?? false) ? '' : 'opacity-50 pointer-events-none' }}">
                    <div class="mb-6">
                        <label for="loser_fee_percentage" class="block text-gray-700 font-bold mb-2">
                            درصد کارمزد از سپرده (%)
                        </label>
                        <input type="number" 
                               id="loser_fee_percentage" 
                               name="loser_fee_percentage" 
                               value="{{ $loserFeeSettings['percentage'] ?? 5 }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               min="0" 
                               max="100" 
                               step="0.01">
                        <p class="text-sm text-gray-600 mt-2">
                            مثال: اگر 5 وارد کنید، 5% از سپرده کاربران بازنده کسر می‌شود و مابقی به آنها برگردانده می‌شود.
                        </p>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات بازندگان
                </button>
            </form>
        </div>

        <!-- تنظیمات سپرده ضبط شده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات سپرده ضبط شده</h2>
            
            <form action="{{ route('admin.settings.forfeit.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="forfeit_to_site_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سهم سایت از سپرده ضبط شده (%)
                    </label>
                    <input type="number" 
                           id="forfeit_to_site_percentage" 
                           name="forfeit_to_site_percentage" 
                           value="{{ $forfeitSettings['to_site_percentage'] ?? 100 }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                    <p class="text-sm text-gray-600 mt-2">
                        وقتی برنده مزایده در مهلت مقرر پرداخت نکند، سپرده او ضبط می‌شود. این درصد به سایت می‌رسد و مابقی به فروشنده.
                        <br>
                        مثال: اگر 60 وارد کنید، 60% به سایت و 40% به فروشنده می‌رسد.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات سپرده ضبط شده
                </button>
            </form>
        </div>

        <!-- تنظیمات کیف پول -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات کیف پول</h2>
            
            <form action="{{ route('admin.settings.wallet.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="wallet_min_deposit" class="block text-gray-700 font-bold mb-2">
                        حداقل مبلغ شارژ حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_min_deposit" 
                           name="wallet_min_deposit" 
                           value="{{ $walletSettings['min_deposit'] ?? 10000 }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند کمتر از این مبلغ به کیف پول خود شارژ کنند.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="wallet_max_deposit" class="block text-gray-700 font-bold mb-2">
                        حداکثر مبلغ شارژ حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_max_deposit" 
                           name="wallet_max_deposit" 
                           value="{{ $walletSettings['max_deposit'] ?? 100000000 }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="10000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند بیشتر از این مبلغ در هر بار به کیف پول خود شارژ کنند.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="wallet_min_withdraw" class="block text-gray-700 font-bold mb-2">
                        حداقل مبلغ برداشت از حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_min_withdraw" 
                           name="wallet_min_withdraw" 
                           value="{{ $walletSettings['min_withdraw'] ?? 50000 }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند کمتر از این مبلغ از کیف پول خود برداشت کنند.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات کیف پول
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDurationFields() {
    const checkbox = document.getElementById('force_auction_duration');
    const fields = document.getElementById('duration-fields');
    
    if (checkbox.checked) {
        fields.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        fields.classList.add('opacity-50', 'pointer-events-none');
    }
}

function toggleLoserFeeFields() {
    const checkbox = document.getElementById('loser_fee_enabled');
    const fields = document.getElementById('loser-fee-fields');
    
    if (checkbox.checked) {
        fields.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        fields.classList.add('opacity-50', 'pointer-events-none');
    }
}
</script>
@endsection
