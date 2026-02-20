@extends('layouts.admin')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">مدیریت دسته‌بندی‌ها</h2>
            <p class="text-sm text-gray-500 mt-1">برای تغییر ترتیب، دسته‌ها را بکشید - برای مشاهده زیردسته‌ها کلیک کنید</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 flex items-center gap-2 text-sm font-medium transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span>
            افزودن دسته‌بندی
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Categories List -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div id="sortable-categories" class="divide-y divide-gray-200">
            @forelse($categories as $category)
            <div class="category-group" data-id="{{ $category->id }}">
                <!-- Parent Category -->
                <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-gray-400 cursor-grab active:cursor-grabbing drag-handle">
                        drag_indicator
                    </span>
                    
                    @if($category->children->count() > 0)
                    <button onclick="toggleChildren({{ $category->id }})" class="p-1 hover:bg-gray-200 rounded transition-colors">
                        <span class="material-symbols-outlined text-gray-600 expand-icon" id="icon-{{ $category->id }}">
                            chevron_left
                        </span>
                    </button>
                    @else
                    <span class="w-8"></span>
                    @endif
                    
                    @if($category->icon)
                        <span class="material-symbols-outlined text-gray-600">{{ $category->icon }}</span>
                    @endif
                    
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500">
                            @persian($category->listings()->count()) حراجی
                            @if($category->children->count() > 0)
                                • @persian($category->children->count()) زیردسته
                            @endif
                        </p>
                    </div>
                    
                    @if($category->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            فعال
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            غیرفعال
                        </span>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Children Categories -->
                @if($category->children->count() > 0)
                <div id="children-{{ $category->id }}" class="hidden bg-gray-50 border-t border-gray-200">
                    @foreach($category->children as $child)
                    <div class="flex items-center gap-3 p-4 pr-16 hover:bg-gray-100 transition-colors border-b border-gray-200 last:border-b-0">
                        <span class="text-gray-400">└─</span>
                        
                        @if($child->icon)
                            <span class="material-symbols-outlined text-gray-500 text-[20px]">{{ $child->icon }}</span>
                        @endif
                        
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800">{{ $child->name }}</h4>
                            <p class="text-xs text-gray-500">@persian($child->listings()->count()) حراجی</p>
                        </div>
                        
                        @if($child->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                فعال
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                غیرفعال
                            </span>
                        @endif
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.category-attributes.index', $child) }}" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="مدیریت ویژگی‌ها">
                                <span class="material-symbols-outlined text-[18px]">tune</span>
                            </a>
                            <a href="{{ route('admin.categories.edit', $child) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این زیردسته مطمئن هستید؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-gray-400 text-5xl">category</span>
                    <p class="text-gray-500">دسته‌بندی‌ای یافت نشد</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Toggle children visibility
function toggleChildren(categoryId) {
    const childrenDiv = document.getElementById('children-' + categoryId);
    const icon = document.getElementById('icon-' + categoryId);
    
    if (childrenDiv.classList.contains('hidden')) {
        childrenDiv.classList.remove('hidden');
        icon.textContent = 'expand_more';
    } else {
        childrenDiv.classList.add('hidden');
        icon.textContent = 'chevron_left';
    }
}

// Sortable for parent categories
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sortable-categories');
    if (!container) return;

    new Sortable(container, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'bg-blue-50',
        onEnd: function(evt) {
            const groups = Array.from(container.querySelectorAll('.category-group'));
            const order = groups.map((group, index) => ({
                id: group.dataset.id,
                order: index + 1
            }));

            // ارسال ترتیب جدید به سرور
            fetch('{{ route("admin.categories.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('ترتیب با موفقیت ذخیره شد', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('خطا در ذخیره ترتیب', 'error');
            });
        }
    });
});

function showNotification(message, type) {
    const alert = document.createElement('div');
    alert.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded-lg z-50 ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}
</script>
@endsection
