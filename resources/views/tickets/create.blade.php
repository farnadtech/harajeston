<x-dashboard-layout pageTitle="تیکت جدید">

<div class="p-6 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tickets.index') }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تیکت جدید</h1>
            <p class="text-sm text-gray-500">ارسال درخواست یا سوال</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <form action="{{ route('tickets.store') }}" method="POST" x-data="{ listingId: '{{ old('listing_id', $preselectedListing?->id ?? '') }}', ticketType: '{{ old('type', '') }}' }">
            @csrf

            {{-- انتخاب حراجی --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    حراجی مرتبط <span class="text-red-500">*</span>
                </label>
                @if($eligibleListings->isEmpty())
                    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">
                        <span class="material-symbols-outlined text-lg align-middle ml-1">warning</span>
                        برای ارسال تیکت باید در یک حراجی برنده شده باشید.
                    </div>
                @else
                    <div class="relative">
                        <select name="listing_id" x-model="listingId"
                            class="w-full border border-gray-300 rounded-xl pr-4 pl-10 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary appearance-none bg-white"
                            style="background-image:none !important; padding-left:2.5rem !important;"
                            required>
                            <option value="">-- انتخاب حراجی --</option>
                            @foreach($eligibleListings as $listing)
                                <option value="{{ $listing->id }}"
                                    {{ (old('listing_id', $preselectedListing?->id) == $listing->id) ? 'selected' : '' }}>
                                    {{ $listing->title }}
                                </option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 20 20"><path stroke="#6b7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/></svg>
                        </span>
                    </div>
                @endif
            </div>

            {{-- نوع تیکت --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ارسال به <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @if(auth()->user()->isSeller())
                        <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-colors"
                            :class="ticketType === 'seller_to_admin' ? 'border-primary bg-primary/5' : ''">
                            <input type="radio" name="type" value="seller_to_admin" x-model="ticketType" class="text-primary">
                            <div>
                                <p class="text-sm font-medium">ادمین سایت</p>
                                <p class="text-xs text-gray-500">مشکل فنی یا مالی</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-colors"
                            :class="ticketType === 'seller_to_buyer' ? 'border-primary bg-primary/5' : ''">
                            <input type="radio" name="type" value="seller_to_buyer" x-model="ticketType" class="text-primary">
                            <div>
                                <p class="text-sm font-medium">خریدار</p>
                                <p class="text-xs text-gray-500">مکاتبه با برنده حراجی</p>
                            </div>
                        </label>
                    @else
                        <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-colors"
                            :class="ticketType === 'buyer_to_admin' ? 'border-primary bg-primary/5' : ''">
                            <input type="radio" name="type" value="buyer_to_admin" x-model="ticketType" class="text-primary">
                            <div>
                                <p class="text-sm font-medium">ادمین سایت</p>
                                <p class="text-xs text-gray-500">مشکل فنی یا مالی</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary transition-colors"
                            :class="ticketType === 'buyer_to_seller' ? 'border-primary bg-primary/5' : ''">
                            <input type="radio" name="type" value="buyer_to_seller" x-model="ticketType" class="text-primary">
                            <div>
                                <p class="text-sm font-medium">فروشنده</p>
                                <p class="text-xs text-gray-500">سوال درباره محصول</p>
                            </div>
                        </label>
                    @endif
                </div>
            </div>

            {{-- موضوع --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">موضوع <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="موضوع تیکت را بنویسید...">
            </div>

            {{-- اولویت --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                <div class="relative">
                    <select name="priority" class="w-full border border-gray-300 rounded-xl pr-4 pl-10 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary appearance-none bg-white"
                        style="background-image:none !important; padding-left:2.5rem !important;">
                        <option value="low">کم</option>
                        <option value="normal" selected>معمولی</option>
                        <option value="high">زیاد</option>
                    </select>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 20 20"><path stroke="#6b7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/></svg>
                    </span>
                </div>
            </div>

            {{-- متن پیام --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">متن پیام <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required maxlength="5000"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary resize-none"
                    placeholder="توضیحات کامل مشکل یا سوال خود را بنویسید...">{{ old('message') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="!listingId || !ticketType"
                    class="flex-1 bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    ارسال تیکت
                </button>
                <a href="{{ route('tickets.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>

</x-dashboard-layout>
