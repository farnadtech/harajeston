<div class="category-megamenu relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
        <span class="material-symbols-outlined text-[20px]">apps</span>
        <span class="font-medium">دسته‌بندی‌ها</span>
        <span class="material-symbols-outlined text-[18px]">expand_more</span>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute top-full right-0 mt-2 w-[900px] bg-white rounded-xl shadow-2xl border border-gray-100 z-50">
        <div class="grid grid-cols-4 gap-4 p-6 max-h-[500px] overflow-y-auto">
            @foreach($categories as $parent)
            <div class="flex flex-col">
                <a href="{{ route('listings.index', ['category' => $parent->slug]) }}" 
                   class="flex items-center gap-2 p-2 font-bold text-gray-800 hover:bg-primary/5 hover:text-primary rounded-lg transition-colors mb-2">
                    <span class="material-symbols-outlined text-[20px]">{{ $parent->icon ?? 'category' }}</span>
                    <span class="text-sm">{{ $parent->name }}</span>
                </a>
                
                @if($parent->children && count($parent->children) > 0)
                <ul class="space-y-1">
                    @foreach($parent->children as $child)
                    <li>
                        <a href="{{ route('listings.index', ['category' => $child->slug]) }}"
                           class="block pr-8 py-1.5 text-sm text-gray-600 hover:text-primary hover:bg-gray-50 rounded transition-colors">
                            {{ $child->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
