@extends('layouts.admin')

@section('title', 'مدیریت پرسش‌های محصولات')
@section('header-title', 'مدیریت پرسش‌های محصولات')
@section('header-subtitle', 'تایید، رد یا حذف پرسش‌های کاربران درباره محصولات')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                <option value="">همه وضعیت‌ها</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار تایید (@persian($pendingCount))</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>تایید شده</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>رد شده</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                اعمال فیلتر
            </button>
            
            @if(request()->hasAny(['status', 'type']))
                <a href="{{ route('admin.comments.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    حذف فیلترها
                </a>
            @endif
        </form>
    </div>

    <!-- Comments List -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @forelse($comments as $comment)
            <div class="p-6 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-gray-400">
                                {{ $comment->type === 'question' ? 'help' : 'chat_bubble' }}
                            </span>
                            <span class="font-bold text-gray-900">{{ $comment->user->name }}</span>
                            @if($comment->rating)
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-{{ $i <= $comment->rating ? 'yellow-400' : 'gray-300' }} text-sm">★</span>
                                    @endfor
                                    <span class="text-xs text-gray-600 mr-1">({{ $comment->rating }})</span>
                                </div>
                            @endif
                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            
                            @if($comment->status === 'pending')
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-medium">در انتظار تایید</span>
                            @elseif($comment->status === 'approved')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">تایید شده</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">رد شده</span>
                            @endif
                            
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                                {{ $comment->type === 'question' ? 'پرسش' : 'نظر' }}
                            </span>
                        </div>
                        
                        <a href="{{ route('listings.show', $comment->listing_id) }}" class="text-sm text-primary hover:underline mb-2 block">
                            {{ $comment->listing->title }}
                        </a>
                        
                        <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>
                        
                        @if($comment->replies->count() > 0)
                            <div class="mt-4 mr-8 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-blue-50 rounded-lg p-4 border-r-4 border-primary">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="material-symbols-outlined text-primary text-sm">reply</span>
                                            <span class="font-medium text-gray-900 text-sm">{{ $reply->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                            @if($reply->user_id === $comment->listing->seller_id)
                                                <span class="text-xs bg-primary text-white px-2 py-0.5 rounded-full">فروشنده</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex gap-2">
                        @if($comment->status === 'pending')
                            <form method="POST" action="{{ route('admin.comments.approve', $comment->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="تایید">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.comments.reject', $comment->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="رد">
                                    <span class="material-symbols-outlined">cancel</span>
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" class="inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="حذف">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">chat_bubble_outline</span>
                <p class="text-gray-500">نظر یا پرسشی یافت نشد</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($comments->hasPages())
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            {{ $comments->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection
