@extends('layouts.app')

@section('title', 'صفحه اصلی')

@section('content')
@php
    // همه محصولات حراج هستند
    $featuredListing = $listings->where('status', 'active')->first();
    $hotAuctions = $listings->where('status', 'active')->take(8);
@endphp

<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-12">
    <!-- Hero Section -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-auto lg:h-[480px]">
        <!-- Main Slider -->
        <div class="lg:col-span-8 relative rounded-2xl overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-l from-black/70 to-transparent z-10"></div>
            @if($featuredListing && $featuredListing->images->isNotEmpty())
                <img alt="{{ $featuredListing->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" src="{{ url('storage/' . $featuredListing->images->first()->file_path) }}"/>
            @else
                <img alt="Hero Banner" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAGbpvjygdH1kviPkLE3nFqQpfKLQHSiB47VVhTAFFWhZMV2CG0UslDc1P5gj_RtA6I3Flvk1vmorLJ7fvmVW0hTMqjqbadzBRdhON74ePElldkk2tsg-M0IrJ8MROpObSfqocakvM3y__X320rrSl-M8FxSiIfZW0UmlwRI2Wp3VSgZYIswkyt-peOeNVZR2lkeOg3hk0d2ZOoRgrx-ibXIetxRostQZtk7LrZiO5pYwY_idrXgaEuP6n_JGah7weF5UIAL8qarb0"/>
            @endif
            <div class="absolute bottom-0 right-0 p-8 z-20 max-w-xl text-white">
                <span class="inline-block px-3 py-1 bg-secondary text-white text-xs font-bold rounded-full mb-4">ویژه</span>
                @if($featuredListing)
                    <h2 class="text-3xl md:text-5xl font-black mb-4 leading-tight">{{ $featuredListing->title }}</h2>
                    <p class="text-gray-200 text-lg mb-6 leading-relaxed">{{ Str::limit($featuredListing->description, 100) }}</p>
                    <a href="{{ route('listings.show', $featuredListing) }}" class="bg-primary hover:bg-blue-600 text-white px-8 py-3 rounded-xl font-bold text-lg transition-all shadow-lg shadow-primary/30 inline-flex items-center gap-2">
                        <span>شرکت در مزایده</span>
                        <span class="material-symbols-outlined rtl:rotate-180">arrow_right_alt</span>
                    </a>
                @else
                    <h2 class="text-3xl md:text-5xl font-black mb-4 leading-tight">کلکسیون نفیس فرش دستباف ایرانی</h2>
                    <p class="text-gray-200 text-lg mb-6 leading-relaxed">مزایده بی‌نظیر آثار استادکاران تبریز و کاشان. شروع قیمت از ۵۰,۰۰۰,۰۰۰ تومان.</p>
                    <a href="{{ route('listings.index') }}" class="bg-primary hover:bg-blue-600 text-white px-8 py-3 rounded-xl font-bold text-lg transition-all shadow-lg shadow-primary/30 inline-flex items-center gap-2">
                        <span>مشاهده مزایده‌ها</span>
                        <span class="material-symbols-outlined rtl:rotate-180">arrow_right_alt</span>
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Side Banners -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="flex-1 relative rounded-2xl overflow-hidden bg-[#e0e7ff] flex flex-col justify-center p-6 items-start">
                <div class="z-10">
                    <span class="text-primary font-bold text-sm">محصولات دیجیتال</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-1 mb-2">گوشی و تبلت</h3>
                    <p class="text-gray-600 text-sm mb-4">جدیدترین محصولات در مزایده</p>
                    <a class="text-primary font-bold hover:underline text-sm flex items-center gap-1" href="{{ route('listings.index') }}">
                        مشاهده جزئیات <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span>
                    </a>
                </div>
                <img alt="iPhone" class="absolute -left-12 bottom-0 w-48 h-auto object-contain z-0 opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCags2xX6-Sxa_RQwonxzWuQdz0-ywNB7SJ45FjDrmvzAj-3468rjlChy9ftWbPM9W2QBdKNw4KD67D6g8e7wOhZH2Fez63rGwQw9Q6oj-bnurFafZLcuH9vLehiFxacZTYL6uISeaTBlOJq0AuZJwRgSFYuEjpTIn9WCGiHnwTKO9SUAP9i73R6sfGdEHy3gXOPliWsYevd27_L7z-Y2NuxKRpvk6vRge-e16RjDDXeqXKmEVsTSwmuwEkVa-DjondeIvj0On3cyOB"/>
            </div>
            <div class="flex-1 relative rounded-2xl overflow-hidden bg-[#fff7ed] flex flex-col justify-center p-6 items-start">
                <div class="z-10">
                    <span class="text-secondary font-bold text-sm">ساعت و جواهرات</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-1 mb-2">ساعت‌های کلاسیک</h3>
                    <p class="text-gray-600 text-sm mb-4">مزایده برندهای معتبر</p>
                    <a class="text-secondary font-bold hover:underline text-sm flex items-center gap-1" href="{{ route('listings.index') }}">
                        مشاهده کاتالوگ <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span>
                    </a>
                </div>
                <img alt="Watch" class="absolute -left-4 bottom-[-20px] w-40 h-auto object-contain z-0 mix-blend-multiply opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDnhjDliz3Y_HeiA2FIdV_WhmSSqa_oOwOl4UdSTdpDRQ_LFrQSXJqwtyRKq1FjUztrx3uzd3itZt3fL3MrywwyB22hEBo6I4qqiP-DVF96Y4zoBSv5N0YbtuY7f2t6vIbAysuMSdyy_0Fe_-g3FzM0GjRF1tC7Z91_SHa6Nr8PUy9Krdynjf7MSkLRifd1CY1sMdl6Zw4w0gs4RIY-UsHIyZIfL9a0QpQzlaAuM5MbtgzSPJRk2ULW4NAOV9iAoJZGiPDakwJ83HE8"/>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">دسته‌بندی‌های محبوب</h2>
            <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all" href="{{ route('listings.index') }}">
                مشاهده همه <span class="material-symbols-outlined text-lg rtl:rotate-180">arrow_right_alt</span>
            </a>
        </div>
        <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 snap-x">
            <!-- Category Item -->
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">devices</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">دیجیتال</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">checkroom</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">مد و پوشاک</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">diamond</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">جواهرات</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">chair</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">دکوراسیون</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">brush</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">هنر</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">directions_car</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">خودرو</span>
            </a>
            <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center cursor-pointer" href="{{ route('listings.index') }}">
                <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-md">
                    <span class="material-symbols-outlined text-3xl">sports_esports</span>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">سرگرمی</span>
            </a>
        </div>
    </section>

    <!-- Hot Auctions Grid -->
    @if($hotAuctions->isNotEmpty())
    <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                <span class="material-symbols-outlined">local_fire_department</span>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900">مزایده‌های داغ</h2>
                <p class="text-gray-500 text-sm">فرصت‌های استثنایی با زمان محدود</p>
            </div>
            <div class="mr-auto hidden sm:flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600">
                <span class="material-symbols-outlined text-lg">timer</span>
                <span>پایان تا: ۲۴ ساعت آینده</span>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($hotAuctions as $auction)
            <!-- Auction Card -->
            <div class="group bg-white rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col h-full relative overflow-hidden">
                @if($auction->ends_at && $auction->status === 'active')
                    @php
                        $hoursLeft = $auction->ends_at->diffInHours(now());
                        $now = \Carbon\Carbon::now();
                        if ($now->greaterThanOrEqualTo($auction->ends_at)) {
                            $timeLeft = 'پایان یافته';
                        } else {
                            $diff = $now->diff($auction->ends_at);
                            $days = $diff->d;
                            $hours = $diff->h;
                            $minutes = $diff->i;
                            
                            if ($days > 0) {
                                $timeLeft = \App\Services\PersianNumberService::convertToPersian($days) . ' روز';
                            } elseif ($hours > 0) {
                                $timeLeft = \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت';
                            } elseif ($minutes > 0) {
                                $timeLeft = \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه';
                            } else {
                                $timeLeft = 'کمتر از یک دقیقه';
                            }
                        }
                    @endphp
                    <div class="absolute top-3 left-3 z-10 {{ $hoursLeft < 3 ? 'bg-red-500 animate-pulse' : 'bg-orange-500' }} text-white text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                        {{ $timeLeft }} مانده
                    </div>
                @endif
                
                @if($auction->hasBuyNowPrice())
                    <div class="absolute top-3 right-3 z-10 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md shadow-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">bolt</span>
                        خرید فوری
                    </div>
                @endif
                
                <a href="{{ route('listings.show', $auction) }}" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
                    @if($auction->images->isNotEmpty())
                        <img alt="{{ $auction->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" src="{{ url('storage/' . $auction->images->first()->file_path) }}"/>
                    @else
                        <img alt="Product" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmIr5ej3rLupZupENZHj3j-T-3FYinv-aE7s7fj2Hpy1FCMpfoQIUcZJ5sFK8G96pN0juy3KKU-p7O3VJy2m1LiMKyRttj3ZNuoqQhFp5ndZVr2SJJaDRbEEnTCsY_djwglUVEJDI0SVUkoNx-mfqjn7ypv9efnl5_D1lJzl8ah4c_mYlEl_oYXVj58KSEPuWY-7awRFvPXp9SN4tu9BPyFnrP8V7cEeNsTmVK9egRlmadNvfJU0zTZXEt0kp4qaWkLDRDoPqvqUi0"/>
                    @endif
                </a>
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-center gap-1 mb-2">
                        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">مزایده</span>
                    </div>
                    <a href="{{ route('listings.show', $auction) }}">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors line-clamp-1">{{ $auction->title }}</h3>
                    </a>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($auction->description, 60) }}</p>
                    <div class="mt-auto space-y-3">
                        <div class="flex justify-between items-end border-t border-dashed border-gray-200 pt-3">
                            <span class="text-xs text-gray-500 mb-1">پیشنهاد فعلی:</span>
                            <div class="text-right">
                                <span class="text-lg font-black text-primary">@price($auction->current_price ?? $auction->starting_price)</span>
                                <span class="text-xs text-gray-400">تومان</span>
                            </div>
                        </div>
                        
                        @if($auction->hasBuyNowPrice())
                            <div class="flex justify-between items-end pb-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500">خرید فوری:</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-green-600">@price($auction->buy_now_price)</span>
                                    <span class="text-xs text-gray-400">تومان</span>
                                </div>
                            </div>
                        @endif
                        
                        <a href="{{ route('listings.show', $auction) }}" class="block w-full py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20 text-center">
                            ثبت پیشنهاد
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Trust Badges -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 py-8 border-t border-gray-200">
        <div class="flex items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-blue-50 text-primary rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">verified_user</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">ضمانت اصالت کالا</h3>
                <p class="text-xs text-gray-500 mt-1">تایید کارشناسی تمامی کالاها قبل از مزایده</p>
            </div>
        </div>
        <div class="flex items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-blue-50 text-primary rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">local_shipping</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">ارسال سریع و بیمه شده</h3>
                <p class="text-xs text-gray-500 mt-1">ارسال به سراسر کشور با بسته بندی ایمن</p>
            </div>
        </div>
        <div class="flex items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-12 h-12 bg-blue-50 text-primary rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">support_agent</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">پشتیبانی ۲۴ ساعته</h3>
                <p class="text-xs text-gray-500 mt-1">پاسخگویی به سوالات شما در تمام مراحل</p>
            </div>
        </div>
    </section>
</div>
@endsection


