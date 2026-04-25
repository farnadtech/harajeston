@extends('layouts.admin')
@section('title', 'درخواست‌های برداشت')
@section('header-title', 'درخواست‌های برداشت')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-black text-gray-900">درخواست‌های برداشت</h2>
        <a href="{{ route('admin.withdrawals.export', request()->query()) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">download</span>
            خروجی Excel
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex gap-3 flex-wrap">
        @foreach(['pending' => 'در انتظار ('.$pendingCount.')', 'approved' => 'تایید شده', 'rejected' => 'رد شده', 'all' => 'همه'] as $val => $label)
            <a href="{{ route('admin.withdrawals.index', ['status' => $val]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ $status === $val ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @forelse($requests as $req)
            <div class="p-5 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                    {{-- User & Amount --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                            <span class="font-bold text-gray-900">{{ $req->user->name }}</span>
                            <span class="text-xs text-gray-500">{{ $req->user->phone }}</span>
                            <span class="text-xs text-gray-400">{{ \Morilog\Jalali\Jalalian::fromCarbon($req->created_at)->format('Y/m/d H:i') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($req->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $req->status_label }}
                            </span>
                        </div>

                        <div class="text-2xl font-black text-primary mb-3">@price($req->amount) تومان</div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-1 text-sm">
                            <div><span class="text-gray-500">نام:</span> <span class="font-medium">{{ $req->full_name }}</span></div>
                            <div><span class="text-gray-500">بانک:</span> <span class="font-medium">{{ $req->bank_name }}</span></div>
                            <div><span class="text-gray-500">کد ملی:</span> <span class="font-medium font-mono" dir="ltr">{{ $req->national_id }}</span></div>
                            <div><span class="text-gray-500">شماره کارت:</span> <span class="font-medium font-mono" dir="ltr">{{ $req->card_number }}</span></div>
                            <div class="md:col-span-2"><span class="text-gray-500">شبا:</span> <span class="font-medium font-mono" dir="ltr">{{ $req->sheba_number }}</span></div>
                        </div>

                        @if($req->status === 'rejected' && $req->reject_reason)
                            <div class="mt-2 text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">
                                دلیل رد: {{ $req->reject_reason }}
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    @if($req->status === 'pending')
                    <div class="flex flex-col gap-2 min-w-[160px]">
                        <form id="approve-form-{{ $req->id }}" method="POST" action="{{ route('admin.withdrawals.approve', $req) }}">
                            @csrf
                        </form>
                        <button type="button"
                                onclick="showApproveModal({{ $req->id }}, '{{ $req->user->name }}', {{ $req->amount }})"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            تایید و پرداخت
                        </button>

                        <button type="button"
                                onclick="showRejectModal({{ $req->id }}, '{{ route('admin.withdrawals.reject', $req) }}')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                            <span class="material-symbols-outlined text-base">cancel</span>
                            رد درخواست
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">account_balance_wallet</span>
                <p class="text-gray-500">درخواستی یافت نشد</p>
            </div>
        @endforelse
    </div>

    @if($requests->hasPages())
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            {{ $requests->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>

{{-- Approve Modal --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">تایید درخواست برداشت</h3>
            <p class="text-gray-500 text-sm" id="approve-desc"></p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
            <div class="flex items-start gap-2 text-green-800 text-sm">
                <span class="material-symbols-outlined text-base mt-0.5">info</span>
                <div>
                    <p class="font-medium mb-1">با تایید این درخواست:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>مبلغ از کیف پول کاربر کسر می‌شود</li>
                        <li>این عملیات قابل بازگشت نیست</li>
                        <li>پرداخت به صورت دستی انجام دهید</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="closeApproveModal()"
                    class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                انصراف
            </button>
            <button type="button" onclick="submitApprove()"
                    class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-bold flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">check_circle</span>
                تایید و پرداخت
            </button>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-red-600 text-3xl">cancel</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">رد درخواست برداشت</h3>
            <p class="text-gray-500 text-sm">لطفاً دلیل رد درخواست را وارد کنید تا به کاربر نمایش داده شود.</p>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">دلیل رد درخواست *</label>
                <textarea name="reject_reason" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none text-sm"
                          placeholder="مثال: مشخصات بانکی ناقص است. لطفاً شماره شبا را مجدداً بررسی کنید."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                    انصراف
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-bold flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">cancel</span>
                    رد درخواست
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentApproveId = null;

function showApproveModal(id, name, amount) {
    currentApproveId = id;
    const formatted = new Intl.NumberFormat('fa-IR').format(amount);
    document.getElementById('approve-desc').textContent =
        'تایید برداشت ' + formatted + ' تومان برای ' + name;
    document.getElementById('approveModal').classList.remove('hidden');
}
function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    currentApproveId = null;
}
function submitApprove() {
    if (currentApproveId) {
        document.getElementById('approve-form-' + currentApproveId).submit();
    }
}

function showRejectModal(id, url) {
    document.getElementById('rejectForm').action = url;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').querySelector('textarea').value = '';
}

// Close on backdrop click
['approveModal', 'rejectModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endsection
