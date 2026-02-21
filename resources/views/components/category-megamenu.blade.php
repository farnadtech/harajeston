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
         class="absolute top-full right-0 mt-2 w-[1000px] bg-white rounded-xl shadow-2xl border border-gray-100 z-50"
         x-data="{ activeParent: null }">
        <div class="grid grid-cols-12 max-h-[600px]">
            {{-- لیست دسته‌های اصلی --}}
            <div class="col-span-3 border-l border-gray-100 overflow-y-auto">
                @foreach($categories as $parent)
                <button @mouseenter="activeParent = {{ $parent->id }}"
                        @click="window.location.href='{{ route('listings.index', ['category' => $parent->slug]) }}'"
                        :class="activeParent === {{ $parent->id }} ? 'bg-primary/5 text-primary border-r-2 border-primary' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 p-4 transition-colors text-right">
                    <span class="material-symbols-outlined text-[22px]">{{ $parent->icon ?? 'category' }}</span>
                    <span class="text-sm font-medium">{{ $parent->name }}</span>
                    @if($parent->children && count($parent->children) > 0)
                        <span class="material-symbols-outlined text-sm mr-auto">chevron_left</span>
                    @endif
                </button>
                @endforeach
            </div>
            
            {{-- محتوای زیردسته‌ها --}}
            <div class="col-span-9 p-6 overflow-y-auto">
                @foreach($categories as $parent)
                <div x-show="activeParent === {{ $parent->id }}" x-transition>
                    @if($parent->children && count($parent->children) > 0)
                        <div class="grid grid-cols-3 gap-6">
                            @foreach($parent->children as $child)
                            <div>
                                <a href="{{ route('listings.index', ['category' => $child->slug]) }}" 
                                   class="flex items-center gap-2 font-bold text-gray-800 hover:text-primary transition-colors mb-3 group">
                                    @if($child->icon)
                                        <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">{{ $child->icon }}</span>
                                    @endif
                                    <span class="text-sm">{{ $child->name }}</span>
                                </a>
                                
                                @if($child->children && count($child->children) > 0)
                                <ul class="space-y-2">
                                    @foreach($child->children as $grandchild)
                                    <li>
                                        <a href="{{ route('listings.index', ['category' => $grandchild->slug]) }}"
                                           class="block text-xs text-gray-600 hover:text-primary hover:translate-x-1 transition-all py-1">
                                            {{ $grandchild->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-symbols-outlined text-4xl mb-2">category</span>
                            <p class="text-sm">این دسته زیرمجموعه ندارد</p>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
