@extends('layouts.app')

@section('title', 'نتایج جستجو')

@section('content')
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    فیلترها
                </h2>

                @if(!empty($availableAttributes) && $availableAttributes->count() > 0)
                <form method="GET" action="{{ route('listings.index') }}" class="space-y-4">
                    <!-- حفظ پارامترهای موجود -->
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('tag'))
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                    @endif

                    @foreach($availableAttributes as $attribute)
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $attribute->name }}</label>
                        
                        @if($attribute->type === 'select' && $attribute->options)
                            <select name="attr[{{ $attribute->id }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <option value="">همه</option>
                                @foreach($attribute->options as $option)
                                    <option value="{{ $option }}" {{ request("attr.{$attribute->id}") === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($attribute->type === 'number')
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="attr[{{ $attribute->id }}][min]" 
                                       value="{{ request("attr.{$attribute->id}.min") }}"
                                       placeholder="از" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <input type="number" name="attr[{{ $attribute->id }}][max]" 
                                       value="{{ request("attr.{$attribute->id}.max") }}"
                                       placeholder="تا" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                            </div>
                        @else
                            <input type="text" name="attr[{{ $attribute->id }}]" 
                                   value="{{ request("attr.{$attribute->id}") }}"
                                   placeholder="{{ $attribute->name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                        @endif
                    </div>
                    @endforeach

                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                        اعمال فیلترها
                    </button>
                </form>
                @endif

                @if(request()->hasAny(['category', 'tag', 'search', 'attr']))
                <a href="{{ route('listings.index') }}" class="block w-full mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center font-medium">
                    حذف همه فیلترها
                </a>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Header -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 mb-2">
                            @if(request('tag'))
                                نتایج برچسب: <span class="text-primary">#{{ request('tag') }}</span>
                            @elseif(request('search'))
                                نتایج جستجو: <span class="text-primary">{{ request('search') }}</span>
                            @elseif(request('category'))
                                @php
                                    $categoryObj = \App\Models\Category::where('slug', request('category'))->first();
                                @endphp
                                دسته‌بندی: <span class="text-primary">{{ $categoryObj ? $categoryObj->name : request('category') }}</span>
                            @else
                                همه مزایده‌ها
                            @endif
                        </h1>
                        <p class="text-sm text-gray-500">
                            {{ \App\Services\PersianNumberService::convertToPersian($listings->total()) }} مزایده یافت شد
                        </p>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600 font-medium">مرتب‌سازی:</span>
                        <select onchange="window.location.href=this.value" class="border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'starting_soon']) }}" {{ request('sort') == 'starting_soon' ? 'selected' : '' }}>
                                زودتر شروع می‌شود
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'ending_soon']) }}" {{ request('sort') == 'ending_soon' || !request('sort') ? 'selected' : '' }}>
                                زودتر به پایان می‌رسد
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                جدیدترین
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                ارزان‌ترین
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                گران‌ترین
                            </option>
                        </select>
                    </div>
                </div>
                
                <!-- Active Filters -->
        @if(request('tag') || request('search') || request('category') || request('buy_now'))
        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-gray-100">
            <span class="text-sm text-gray-600 font-medium">فیلترهای فعال:</span>
            
            @if(request('tag'))
            <a href="{{ request()->fullUrlWithQuery(['tag' => null]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                <span class="material-symbols-outlined text-sm">tag</span>
                {{ request('tag') }}
                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            @endif
            
            @if(request('search'))
            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                <span class="material-symbols-outlined text-sm">search</span>
                {{ request('search') }}
                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            @endif
            
            @if(request('category'))
            @php
                $activeCategoryObj = \App\Models\Category::where('slug', request('category'))->first();
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors">
                <span class="material-symbols-outlined text-sm">category</span>
                {{ $activeCategoryObj ? $activeCategoryObj->name : request('category') }}
                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            @endif
            
            @if(request('buy_now'))
            <a href="{{ request()->fullUrlWithQuery(['buy_now' => null]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg text-sm font-medium hover:bg-orange-100 transition-colors">
                <span class="material-symbols-outlined text-sm">bolt</span>
                خرید فوری
                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            @endif
            
            <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                <span class="material-symbols-outlined text-sm">clear_all</span>
                حذف همه فیلترها
            </a>
        </div>
        @endif
            </div>

            <!-- Results Grid -->
            @if($listings->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($listings as $listing)
                    <x-listing-card :listing="$listing" />
                @endforeach
            </div>
    
    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $listings->links('vendor.pagination.custom') }}
    </div>
    
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-6xl text-gray-400">search_off</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">نتیجه‌ای یافت نشد</h3>
        <p class="text-gray-500 mb-6">متأسفانه مزایده‌ای با این مشخصات پیدا نشد. لطفاً فیلترهای دیگری را امتحان کنید.</p>
        <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-bold">
            <span class="material-symbols-outlined">home</span>
            بازگشت به صفحه اصلی
        </a>
    </div>
            @endif
        </div>
    </div>
</div>
@endsection


