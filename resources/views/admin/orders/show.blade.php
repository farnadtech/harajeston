@extends('layouts.admin')

@section('title', 'جزئیات سفارش #' . $order->order_number)

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined text-2xl">arrow_forward</span>
            </a>
            <div>
                <h2 class="text-2xl font-black text-gray-900">جزئیات سفارش #{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">مشاهده و ویرایش اطلاعات سفارش</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">وضعیت سفارش</h3>
                @php
                    $statusConfig = [
                        'pending' => ['text' => 'در انتظار پردازش', 'class' => 'bg-yellow-100 text-yellow-800'],
                        'processing' => ['text' => 'در حال پردازش', 'class' => 'bg-blue-100 text-blue-800'],
                        'shipped' => ['text' => 'در حال ارسال', 'class' => 'bg-purple-100 text-purple-800'],
                        'delivered' => ['text' => 'تحویل داده شده', 'class' => 'bg-green-100 text-green-800'],
                        'cancelled' => ['text' => 'لغو شده', 'class' => 'bg-red-100 text-red-800'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                @endphp
                <div class="flex items-center justify-between mb-4">
                    <span class="px-4 py-2 rounded-full text-sm font-medium {{ $status['class'] }}">
                        {{ $status['text'] }}
                    </span>
                    <span class="text-sm text-gray-500">
                        {{ \App\Services\JalaliDateService::toJalali($order->created_at, 'Y/m/d H:i') }}
                    </span>
                </div>

                <!-- Change Status Form -->
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')
                    <div class="flex gap-3">
                        <select name="status" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار پردازش</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>در حال پردازش</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>در حال ارسال</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تحویل داده شده</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                        </select>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save ml-1"></i>
                            تغییر وضعیت
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">اقلام سفارش</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                            @if($item->listing->images->isNotEmpty())
                                <img src="{{ url('storage/' . $item->listing->images->first()->file_path) }}" 
                                     alt="{{ $item->listing->title }}"
                                     class="w-20 h-20 object-cover rounded-lg ml-4">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg ml-4 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 mb-1">{{ $item->listing->title }}</h4>
                                <p class="text-sm text-gray-600 mb-2">تعداد: {{ \App\Services\PersianNumberService::convertToPersian($item->quantity) }}</p>
                                <p class="text-sm text-gray-500">
                                    قیمت واحد: {{ \App\Services\PersianNumberService::convertToPersian(number_format($item->price_snapshot)) }} تومان
                                </p>
                            </div>
                            
                            <div class="text-left">
                                <p class="font-semibold text-gray-800">
                                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($item->subtotal)) }} تومان
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">اطلاعات ارسال</h3>
                
                <form action="{{ route('admin.orders.updateShipping', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">آدرس ارسال</label>
                            <textarea name="shipping_address" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $order->shipping_address }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">شهر</label>
                                <input type="text" name="shipping_city" value="{{ $order->shipping_city }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">کد پستی</label>
                                <input type="text" name="shipping_postal_code" value="{{ $order->shipping_postal_code }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">شماره تماس</label>
                            <input type="text" name="shipping_phone" value="{{ $order->shipping_phone }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">کد رهگیری</label>
                            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save ml-1"></i>
                            ذخیره اطلاعات ارسال
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">خلاصه سفارش</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-700">
                        <span>جمع کل اقلام</span>
                        <span>{{ \App\Services\PersianNumberService::convertToPersian(number_format($order->subtotal)) }} تومان</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>هزینه ارسال</span>
                        <span>{{ \App\Services\PersianNumberService::convertToPersian(number_format($order->shipping_cost)) }} تومان</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between font-semibold text-lg text-gray-800">
                        <span>مبلغ کل</span>
                        <span>{{ \App\Services\PersianNumberService::convertToPersian(number_format($order->total)) }} تومان</span>
                    </div>
                </div>
            </div>

            <!-- Buyer Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">اطلاعات خریدار</h3>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->buyer->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->buyer->email }}</p>
                    </div>
                </div>
                @if($order->buyer->phone)
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-phone ml-2 text-gray-400"></i>
                        <span class="text-sm">{{ \App\Services\PersianNumberService::convertToPersian($order->buyer->phone) }}</span>
                    </div>
                @endif
                <a href="{{ route('admin.users.show', $order->buyer) }}" 
                   class="block mt-4 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    مشاهده پروفایل
                </a>
            </div>

            <!-- Seller Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">اطلاعات فروشنده</h3>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-store text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->seller->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->seller->email }}</p>
                    </div>
                </div>
                @if($order->seller->phone)
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-phone ml-2 text-gray-400"></i>
                        <span class="text-sm">{{ \App\Services\PersianNumberService::convertToPersian($order->seller->phone) }}</span>
                    </div>
                @endif
                <a href="{{ route('admin.users.show', $order->seller) }}" 
                   class="block mt-4 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    مشاهده پروفایل
                </a>
            </div>

            <!-- Cancellation Info -->
            @if($order->status === 'cancelled' && $order->cancelled_by)
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-red-900 mb-4">اطلاعات لغو</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-red-700">لغو شده توسط:</span>
                            <span class="font-medium">{{ $order->cancelled_by === 'buyer' ? 'خریدار' : 'فروشنده' }}</span>
                        </div>
                        @if($order->cancellation_penalty)
                            <div class="flex justify-between">
                                <span class="text-red-700">جریمه:</span>
                                <span class="font-medium">{{ \App\Services\PersianNumberService::convertToPersian(number_format($order->cancellation_penalty)) }} تومان</span>
                            </div>
                        @endif
                        @if($order->cancelled_at)
                            <div class="flex justify-between">
                                <span class="text-red-700">تاریخ لغو:</span>
                                <span class="font-medium">{{ \App\Services\JalaliDateService::toJalali($order->cancelled_at, 'Y/m/d H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
