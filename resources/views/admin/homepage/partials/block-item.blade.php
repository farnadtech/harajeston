@php
    $typeLabels = [
        'hero'          => 'بنر اصلی',
        'categories'    => 'دسته‌بندی‌ها',
        'auction_list'  => 'لیست حراجی‌ها',
        'trust_badges'  => 'نشان‌های اعتماد',
        'stats'         => 'آمار سایت',
        'newsletter'    => 'خبرنامه',
        'banner'        => 'بنر تبلیغاتی',
        'text_block'    => 'متن آزاد',
        'divider'       => 'خط جداکننده',
    ];
    $typeIcons = [
        'hero'          => 'image',
        'categories'    => 'category',
        'auction_list'  => 'gavel',
        'trust_badges'  => 'verified',
        'stats'         => 'bar_chart',
        'newsletter'    => 'mail',
        'banner'        => 'campaign',
        'text_block'    => 'text_fields',
        'divider'       => 'horizontal_rule',
    ];
    $label    = $typeLabels[$block['type']] ?? $block['type'];
    $icon     = $typeIcons[$block['type']] ?? 'widgets';
    $subtitle = $block['config']['title'] ?? ($block['config']['content'] ?? '');
    $enabled  = $block['enabled'] ?? true;
@endphp
<div class="block-item bg-white rounded-xl border {{ $enabled ? 'border-gray-200' : 'border-gray-200 opacity-50' }} cursor-grab active:cursor-grabbing hover:border-primary/40 hover:shadow-sm transition-all select-none"
     style="display:block; width:100%;"
     data-id="{{ $block['id'] }}"
     data-type="{{ $block['type'] }}"
     data-enabled="{{ $enabled ? 'true' : 'false' }}">
    <div style="display:flex; align-items:center; gap:12px; padding:12px 16px;">
        <span class="material-symbols-outlined" style="color:#d1d5db; font-size:20px; flex-shrink:0;">{{ $icon }}</span>
        <div style="flex:1; min-width:0;">
            <p style="font-weight:600; color:#1f2937; font-size:14px; margin:0;">{{ $label }}</p>
            <p class="block-subtitle" style="font-size:12px; color:#9ca3af; margin:2px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                {{ $subtitle ?: 'بدون عنوان' }}
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:2px; flex-shrink:0;" onclick="event.stopPropagation()">
            <button onclick="editBlock('{{ $block['id'] }}')"
                    style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                    onmouseover="this.style.color='#3b82f6';this.style.background='rgba(59,130,246,.1)'"
                    onmouseout="this.style.color='#9ca3af';this.style.background='none'"
                    title="ویرایش">
                <span class="material-symbols-outlined" style="font-size:18px;">edit</span>
            </button>
            <button onclick="toggleBlock('{{ $block['id'] }}', this)"
                    style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                    onmouseover="this.style.color='#374151';this.style.background='#f3f4f6'"
                    onmouseout="this.style.color='#9ca3af';this.style.background='none'"
                    title="نمایش/پنهان">
                <span class="material-symbols-outlined" style="font-size:18px;">{{ $enabled ? 'visibility' : 'visibility_off' }}</span>
            </button>
            <button onclick="deleteBlock('{{ $block['id'] }}')"
                    style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                    onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2'"
                    onmouseout="this.style.color='#9ca3af';this.style.background='none'"
                    title="حذف">
                <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
            </button>
        </div>
    </div>
</div>
