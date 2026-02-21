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

        <!-- تنظیمات کمیسیون -->
        <div class="bg-white rounded-lg shadow-md p-6">
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
            </form>
        </div>
    </div>
</div>
@endsection
