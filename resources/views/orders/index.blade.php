<x-dashboard-layout>
    <x-slot name="title">سفارشات من</x-slot>
    <x-slot name="pageTitle">سفارشات من</x-slot>

    <div class="space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
                <div class="flex items-center justify-between border-b border-gray-200 px-2">
                    <nav class="flex -mb-px">
                        <a href="{{ route('orders.index', ['role' => 'buyer']) }}" 
                           class="py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $role === 'buyer' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <span class="material-symbols-outlined text-xl">shopping_bag</span>
                            <span>خریدهای من</span>
                            @if($role === 'buyer')
                                <span class="mr-2 bg-blue-100 text-blue-600 py-1 px-2 rounded-full text-xs font-bold">@persian($orders->count())</span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index', ['role' => 'seller']) }}" 
                           class="py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors flex items-center gap-2 {{ $role === 'seller' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <span class="material-symbols-outlined text-xl">store</span>
                            <span>فروش‌های من</span>
                            @if($role === 'seller')
                                <span class="mr-2 bg-blue-100 text-blue-600 py-1 px-2 rounded-full text-xs font-bold">@persian($orders->count())</span>
                            @endif
                        </a>
                    </nav>
                    @if(!$orders->isEmpty())
                    <a href="{{ route('orders.export', request()->query()) }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors ml-2">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        خروجی Excel
                    </a>
                    @endif
                </div>
            </div>

            <!-- Search & Filter -->
            <form method="GET" action="{{ route('orders.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 flex flex-wrap gap-3 items-end">
                <input type="hidden" name="role" value="{{ $role }}">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">جستجو</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="شماره سفارش یا نام محصول..." class="w-full pr-3 pl-9 py-2 border border-gray-300 rounded-lg text-sm focus:border-primary focus:ring-1 focus:ring-primary/20">
                    </div>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs text-gray-500 mb-1">وضعیت</label>
                    <select name="status" class="no-arrow w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-primary">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach(['pending' => 'در انتظار', 'processing' => 'در حال پردازش', 'shipped' => 'ارسال شده', 'delivered' => 'تحویل داده شده', 'completed' => 'تکمیل شده', 'cancelled' => 'لغو شده'] as $val => $label)
                            <option value="{{ $val }}" {{ ($status ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    اعمال فیلتر
                </button>
                @if($search || $status)
                <a href="{{ route('orders.index', ['role' => $role]) }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    پاک کردن
                </a>
                @endif
            </form>

            @if($orders->isEmpty())
                <!-- Empty State -->
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-gray-400" style="font-size: 60px;">receipt_long</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">
                            @if($role === 'buyer')
                                هنوز سفارشی ثبت نکرده‌اید
                            @else
                                هنوز سفارشی دریافت نکرده‌اید
                            @endif
                        </h3>
                        <p class="text-gray-600 mb-6">
                            @if($role === 'buyer')
                                برای خرید محصولات، به صفحه اصلی بروید و مزایده‌ها را مشاهده کنید.
                            @else
                                وقتی کاربران از شما خرید کنند، سفارشات اینجا نمایش داده می‌شود.
                            @endif
                        </p>
                        @if($role === 'buyer')
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                                <span class="material-symbols-outlined">home</span>
                                رفتن به صفحه اصلی
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Orders List -->
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- Order Header -->
                                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-4 space-x-reverse">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">
                                                سفارش #{{ $order->order_number }}
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                {{ \Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <!-- Status Badge -->
                                        @php
                                            $statusConfig = [
                                                'pending' => ['text' => 'در انتظار پردازش', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'processing' => ['text' => 'در حال پردازش', 'class' => 'bg-blue-100 text-blue-800'],
                                                'shipped' => ['text' => 'ارسال شده', 'class' => 'bg-purple-100 text-purple-800'],
                                                'delivered' => ['text' => 'تحویل داده شده', 'class' => 'bg-green-100 text-green-800'],
                                                'cancelled' => ['text' => 'لغو شده', 'class' => 'bg-red-100 text-red-800'],
                                            ];
                                            $status = $statusConfig[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $status['class'] }}">
                                            {{ $status['text'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Info -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-blue-600">person</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                @if($role === 'buyer')
                                                    فروشنده
                                                @else
                                                    خریدار
                                                @endif
                                            </p>
                                            <p class="text-sm font-medium text-gray-800">
                                                @if($role === 'buyer')
                                                    {{ $order->seller->name }}
                                                @else
                                                    {{ $order->buyer->name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-green-600">inventory_2</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">تعداد اقلام</p>
                                            <p class="text-sm font-medium text-gray-800">@persian($order->items->count()) مورد</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-purple-600">payments</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">مبلغ کل</p>
                                            <p class="text-sm font-bold text-gray-800">@persian(number_format($order->total)) تومان</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items Preview -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="space-y-2">
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    @if($item->listing->images->isNotEmpty())
                                                        <img src="{{ url('storage/' . $item->listing->images->first()->file_path) }}" 
                                                             alt="{{ $item->listing->title }}"
                                                             class="w-12 h-12 object-cover rounded ml-3">
                                                    @else
                                                        <div class="w-12 h-12 bg-gray-200 rounded ml-3 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400">image</span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">{{ $item->listing->title }}</p>
                                                        <p class="text-xs text-gray-500">تعداد: @persian($item->quantity)</p>
                                                    </div>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">@persian(number_format($item->subtotal)) تومان</p>
                                            </div>
                                        @endforeach
                                        
                                        @if($order->items->count() > 2)
                                            <p class="text-xs text-gray-500 text-center pt-2">
                                                و @persian($order->items->count() - 2) مورد دیگر...
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        @if($order->tracking_number)
                                            <span class="text-sm text-gray-600 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">local_shipping</span>
                                                کد رهگیری: <span class="font-medium">{{ $order->tracking_number }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium flex items-center gap-1 shadow-lg shadow-blue-500/20">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                            مشاهده جزئیات
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
    </div>
</x-dashboard-layout>