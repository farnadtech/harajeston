@extends('layouts.seller')

@section('title', 'تست داشبورد')

@section('page-title', 'داشبورد فروشنده')
@section('page-subtitle', 'نام: ' . auth()->user()->name)

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">آمار فروشگاه</h2>
        <div class="space-y-3">
            <p><strong>نام:</strong> {{ auth()->user()->name }}</p>
            <p><strong>مزایده‌های فعال:</strong> {{ $stats['active_auctions'] ?? 0 }}</p>
            <p><strong>در انتظار تایید:</strong> {{ $stats['pending_listings'] ?? 0 }}</p>
            <p><strong>تکمیل شده:</strong> {{ $stats['completed_auctions'] ?? 0 }}</p>
            <p><strong>درآمد کل:</strong> {{ number_format($stats['total_sales'] ?? 0) }} تومان</p>
        </div>
    </div>
@endsection
