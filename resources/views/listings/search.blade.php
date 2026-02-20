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

                @php
                    $currentCategory = request('category') ? \App\Models\Category::where('slug', request('category'))->first() : null;
                    $availableAttributes = [];
                    
                    if ($currentCategory) {
                        // اگر دسته اصلی است، ویژگی‌های تمام زیردسته‌ها را جمع‌آوری کن
                        if ($currentCategory->isParent()) {
                            foreach ($currentCategory->children as $child) {
                                foreach ($child->attributes()->filterable()->get() as $attr) {
                                    $availableAttributes[$attr->id] = $attr;
                                }
                            }
                        } else {
                            // اگر زیردسته است، فقط ویژگی‌های خودش
                            $availableAttributes = $currentCategory->attributes()->filterable()->get()->keyBy('id')->toArray();
                        }
                    }
                @endphp

                @if(!empty($availableAttributes))
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
        <!-- Auction Card -->
        <div class="group bg-white rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col h-full relative overflow-hidden {{ $listing->status === 'completed' ? 'opacity-75' : '' }}">
            @if($listing->status === 'completed')
                <div class="absolute top-3 left-3 z-10 bg-gray-600 text-white text-xs font-bold px-3 py-1.5 rounded-md shadow-sm">
                    تمام شده
                </div>
            @elseif($listing->ends_at && $listing->status === 'active')
                @php
                    $hoursLeft = $listing->ends_at->diffInHours(now());
                    $now = \Carbon\Carbon::now();
                    if ($now->greaterThanOrEqualTo($listing->ends_at)) {
                        $timeLeft = 'پایان یافته';
                    } else {
                        $diff = $now->diff($listing->ends_at);
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
            
            @if($listing->buy_now_price)
                <div class="absolute top-3 right-3 z-10 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-md shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">bolt</span>
                    خرید فوری
                </div>
            @endif
            
            <a href="{{ route('listings.show', $listing) }}" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
                @if($listing->images->isNotEmpty())
                    <img alt="{{ $listing->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" src="{{ url('storage/' . $listing->images->first()->file_path) }}"/>
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <span class="material-symbols-outlined text-6xl">image</span>
                    </div>
                @endif
            </a>
            
            <div class="p-4 flex flex-col flex-1">
                <div class="flex items-center gap-1 mb-2">
                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $listing->category ? $listing->category->name : 'مزایده' }}</span>
                </div>
                
                <a href="{{ route('listings.show', $listing) }}">
                    <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors line-clamp-1">{{ $listing->title }}</h3>
                </a>
                
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($listing->description, 60) }}</p>
                
                <!-- Tags -->
                @if($listing->tags && count($listing->tags) > 0)
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach(array_slice($listing->tags, 0, 3) as $tag)
                    <a href="{{ route('listings.index', ['tag' => $tag]) }}" class="text-xs px-2 py-0.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors">
                        #{{ $tag }}
                    </a>
                    @endforeach
                </div>
                @endif
                
                <div class="mt-auto space-y-3">
                    <div class="flex justify-between items-end border-t border-dashed border-gray-200 pt-3">
                        <span class="text-xs text-gray-500 mb-1">
                            @if($listing->bids->count() > 0)
                                پیشنهاد فعلی:
                            @else
                                قیمت پایه:
                            @endif
                        </span>
                        <div class="text-right">
                            <span class="text-lg font-black text-primary">
                                {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->current_price ?? $listing->starting_price)) }}
                            </span>
                            <span class="text-xs text-gray-400">تومان</span>
                        </div>
                    </div>
                    
                    @if($listing->buy_now_price)
                        <div class="flex justify-between items-end pb-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500">خرید فوری:</span>
                            <div class="text-right">
                                <span class="text-sm font-bold text-green-600">
                                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->buy_now_price)) }}
                                </span>
                                <span class="text-xs text-gray-400">تومان</span>
                            </div>
                        </div>
                    @endif
                    
                    <a href="{{ route('listings.show', $listing) }}" class="block w-full py-2.5 {{ $listing->status === 'completed' ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary hover:bg-blue-600' }} text-white text-sm font-bold rounded-lg transition-colors shadow-lg {{ $listing->status === 'completed' ? 'shadow-gray-400/20' : 'shadow-blue-500/20' }} text-center">
                        {{ $listing->status === 'completed' ? 'مزایده پایان یافته' : 'ثبت پیشنهاد' }}
                    </a>
                </div>
            </div>
        </div>
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


