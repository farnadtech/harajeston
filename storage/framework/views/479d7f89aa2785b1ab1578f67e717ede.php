<?php $__env->startSection('title', 'صفحه اصلی'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $blocks    = json_decode(\App\Models\HomepageSetting::get('blocks', '[]'), true) ?? [];
    $cardStyle = \App\Models\HomepageSetting::get('card_style', 'classic');

    $colsMap = [
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    ];

    // آگهی‌های فعال برای استفاده در بلوک‌ها
    $allListings = $listings;
?>

<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-12">

<?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(!($block['enabled'] ?? true)): ?> <?php continue; ?> <?php endif; ?>
<?php $cfg = $block['config'] ?? []; ?>


<?php if($block['type'] === 'hero'): ?>
<?php
    $mode = $cfg['mode'] ?? 'image';
?>

<?php if($mode === 'listings'): ?>

<?php
    $lFilter  = $cfg['listings_filter'] ?? 'ending_soon';
    $lCount   = (int)($cfg['listings_count'] ?? 6);
    $lBg      = $cfg['listings_bg'] ?? '#1e40af';
    $lTitle   = $cfg['listings_title'] ?? 'محصولات ویژه';
    $showSideBanners = $cfg['show_side_banners'] ?? true;

    $heroListings = $allListings->where('status','active');
    $heroListings = match($lFilter) {
        'most_bids'     => $heroListings->sortByDesc('bids_count'),
        'highest_price' => $heroListings->sortByDesc(fn($l) => $l->bids()->max('amount') ?? $l->starting_price),
        'newest'        => $heroListings->sortByDesc('created_at'),
        'ending_soon'   => $heroListings->sortBy('ends_at'),
        default         => $heroListings,
    };
    $heroListings = $heroListings->take($lCount)->values();
    $sliderId = 'hero-slider-' . $block['id'];
    $lTextPos = $cfg['listings_text_pos'] ?? 'right';
    $slideContentStyle = match($lTextPos) {
        'center' => 'position:absolute; bottom:0; left:0; right:0; padding:2rem; color:white; text-align:center; display:flex; flex-direction:column; align-items:center;',
        'left'   => 'position:absolute; bottom:0; left:0; padding:2rem; color:white; text-align:left; max-width:36rem;',
        default  => 'position:absolute; bottom:0; right:0; left:0; padding:2rem; color:white;',
    };
?>
<?php $showSideBanners = $cfg['show_side_banners'] ?? true; ?>
<section class="grid grid-cols-1 <?php echo e($showSideBanners ? 'lg:grid-cols-12' : ''); ?> gap-6 h-auto lg:h-[480px]">
    
    <div class="<?php echo e($showSideBanners ? 'lg:col-span-8' : 'col-span-full'); ?> relative rounded-2xl overflow-hidden"
         style="background-color: <?php echo e($lBg); ?>">

        <?php if($heroListings->isNotEmpty()): ?>
        
        <div id="<?php echo e($sliderId); ?>" style="position:relative; width:100%; height:100%; min-height:300px;">
            <?php $__currentLoopData = $heroListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $img = $listing->images->isNotEmpty() ? url('storage/' . $listing->images->first()->file_path) : null;
                $price = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price;
            ?>
            <div class="hero-slide" data-slider="<?php echo e($sliderId); ?>"
                 style="position:absolute; inset:0; opacity:<?php echo e($i === 0 ? '1' : '0'); ?>; transition:opacity .6s ease; pointer-events:<?php echo e($i === 0 ? 'auto' : 'none'); ?>;">
                
                <?php if($img): ?>
                    <img src="<?php echo e($img); ?>" alt="<?php echo e($listing->title); ?>"
                         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                <?php endif; ?>
                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.3) 50%, transparent 100%);"></div>

                
                <div style="<?php echo e($slideContentStyle); ?>">
                    <?php if($listing->category): ?>
                        <span style="display:inline-block; background:rgba(255,255,255,.2); backdrop-filter:blur(4px); padding:.25rem .75rem; border-radius:999px; font-size:.75rem; font-weight:600; margin-bottom:.75rem;">
                            <?php echo e($listing->category->name); ?>

                        </span>
                    <?php endif; ?>
                    <h2 style="font-size:clamp(1.25rem,3vw,2rem); font-weight:900; margin:0 0 .5rem; line-height:1.3;">
                        <?php echo e($listing->title); ?>

                    </h2>
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap; <?php echo e($lTextPos === 'center' ? 'justify-content:center;' : ''); ?>">
                        <div style="display:flex; align-items:baseline; gap:.25rem;">
                            <span style="font-size:1.5rem; font-weight:900; color:#fbbf24;">
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($price))); ?>

                            </span>
                            <span style="font-size:.875rem; opacity:.8;">تومان</span>
                        </div>
                        <span style="display:flex; align-items:center; gap:.25rem; font-size:.8rem; opacity:.7;">
                            <span class="material-symbols-outlined" style="font-size:16px;">gavel</span>
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0)); ?> پیشنهاد
                        </span>
                    </div>
                    <a href="<?php echo e(route('listings.show', $listing)); ?>"
                       style="display:inline-flex; align-items:center; gap:.5rem; background:white; color:#1e40af; padding:.6rem 1.5rem; border-radius:.75rem; font-weight:700; font-size:.95rem; text-decoration:none; transition:transform .2s;"
                       onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                        شرکت در مزایده
                        <span class="material-symbols-outlined" style="font-size:18px; transform:scaleX(-1);">arrow_right_alt</span>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($heroListings->count() > 1): ?>
            <button onclick="slideHero('<?php echo e($sliderId); ?>', -1)"
                    style="position:absolute; top:50%; right:1rem; transform:translateY(-50%); z-index:30; background:rgba(255,255,255,.2); backdrop-filter:blur(4px); border:none; color:white; width:44px; height:44px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s;"
                    onmouseover="this.style.background='rgba(255,255,255,.4)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                <span class="material-symbols-outlined" style="font-size:22px;">chevron_right</span>
            </button>
            <button onclick="slideHero('<?php echo e($sliderId); ?>', 1)"
                    style="position:absolute; top:50%; left:1rem; transform:translateY(-50%); z-index:30; background:rgba(255,255,255,.2); backdrop-filter:blur(4px); border:none; color:white; width:44px; height:44px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s;"
                    onmouseover="this.style.background='rgba(255,255,255,.4)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                <span class="material-symbols-outlined" style="font-size:22px;">chevron_left</span>
            </button>

            
            <div style="position:absolute; bottom:1rem; left:50%; transform:translateX(-50%); display:flex; gap:.4rem; z-index:30;">
                <?php $__currentLoopData = $heroListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button onclick="goToSlide('<?php echo e($sliderId); ?>', <?php echo e($i); ?>)"
                        class="hero-dot" data-slider="<?php echo e($sliderId); ?>" data-index="<?php echo e($i); ?>"
                        style="width:<?php echo e($i === 0 ? '24px' : '8px'); ?>; height:8px; border-radius:999px; border:none; cursor:pointer; transition:all .3s; background:<?php echo e($i === 0 ? 'white' : 'rgba(255,255,255,.4)'); ?>;"></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <div style="position:absolute; top:1rem; right:1rem; z-index:30;">
                <span style="background:rgba(0,0,0,.4); backdrop-filter:blur(4px); color:white; padding:.3rem .8rem; border-radius:.5rem; font-size:.8rem; font-weight:600;">
                    <?php echo e($lTitle); ?>

                </span>
            </div>
        </div>
        <?php else: ?>
        <div style="display:flex; align-items:center; justify-content:center; height:100%; min-height:300px; color:rgba(255,255,255,.5);">
            <p>آگهی فعالی وجود ندارد</p>
        </div>
        <?php endif; ?>
    </div>

    
    <?php if($showSideBanners): ?>
    <?php
        $b1Tag   = $cfg['side_banner1_tag']   ?? 'محصولات دیجیتال';
        $b1Title = $cfg['side_banner1_title']  ?? 'گوشی و تبلت';
        $b1Desc  = $cfg['side_banner1_desc']   ?? 'جدیدترین محصولات در مزایده';
        $b1Url   = $cfg['side_banner1_url']    ?? route('listings.index');
        $b1Bg    = $cfg['side_banner1_bg']     ?? '#e0e7ff';
        $b1Color = $cfg['side_banner1_color']  ?? '#3b82f6';
        $b1Img   = $cfg['side_banner1_image']  ?? null;
        $b2Tag   = $cfg['side_banner2_tag']    ?? 'ساعت و جواهرات';
        $b2Title = $cfg['side_banner2_title']  ?? 'ساعت‌های کلاسیک';
        $b2Desc  = $cfg['side_banner2_desc']   ?? 'مزایده برندهای معتبر';
        $b2Url   = $cfg['side_banner2_url']    ?? route('listings.index');
        $b2Bg    = $cfg['side_banner2_bg']     ?? '#fff7ed';
        $b2Color = $cfg['side_banner2_color']  ?? '#f97316';
        $b2Img   = $cfg['side_banner2_image']  ?? null;
    ?>
    <div class="lg:col-span-4 flex flex-col gap-6">
        <a href="<?php echo e($b1Url ?: route('listings.index')); ?>" class="flex-1 relative rounded-2xl overflow-hidden flex items-center group" style="background-color: <?php echo e($b1Bg); ?>; min-height:0;">
            <div class="flex-1 p-5 z-10 min-w-0">
                <span class="font-bold text-sm block mb-1" style="color: <?php echo e($b1Color); ?>"><?php echo e($b1Tag); ?></span>
                <h3 class="text-lg font-black text-gray-900 mb-1 leading-tight"><?php echo e($b1Title); ?></h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo e($b1Desc); ?></p>
                <span class="font-bold text-sm flex items-center gap-1" style="color: <?php echo e($b1Color); ?>">مشاهده <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span></span>
            </div>
            <?php if($b1Img): ?><div style="width:100px; flex-shrink:0; align-self:stretch; overflow:hidden;"><img src="<?php echo e(url('storage/' . $b1Img)); ?>" alt="<?php echo e($b1Title); ?>" style="width:100%; height:100%; object-fit:cover; display:block;"></div><?php endif; ?>
        </a>
        <a href="<?php echo e($b2Url ?: route('listings.index')); ?>" class="flex-1 relative rounded-2xl overflow-hidden flex items-center group" style="background-color: <?php echo e($b2Bg); ?>; min-height:0;">
            <div class="flex-1 p-5 z-10 min-w-0">
                <span class="font-bold text-sm block mb-1" style="color: <?php echo e($b2Color); ?>"><?php echo e($b2Tag); ?></span>
                <h3 class="text-lg font-black text-gray-900 mb-1 leading-tight"><?php echo e($b2Title); ?></h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo e($b2Desc); ?></p>
                <span class="font-bold text-sm flex items-center gap-1" style="color: <?php echo e($b2Color); ?>">مشاهده <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span></span>
            </div>
            <?php if($b2Img): ?><div style="width:100px; flex-shrink:0; align-self:stretch; overflow:hidden;"><img src="<?php echo e(url('storage/' . $b2Img)); ?>" alt="<?php echo e($b2Title); ?>" style="width:100%; height:100%; object-fit:cover; display:block;"></div><?php endif; ?>
        </a>
    </div>
    <?php endif; ?>
</section>

<?php else: ?>

<?php
    $bgColor         = $cfg['bg_color'] ?? '#1e40af';
    $customImage     = $cfg['custom_image'] ?? null;
    $showSideBanners = $cfg['show_side_banners'] ?? true;
    $textPos         = $cfg['text_position'] ?? 'right';
    $textColor       = $cfg['text_color'] ?? '#ffffff';
    $btnBg           = $cfg['btn_bg'] ?? '#3b82f6';
    $btnTextColor    = $cfg['btn_text_color'] ?? '#ffffff';

    // موقعیت container متن
    $textContainerStyle = match($textPos) {
        'center' => 'position:absolute; bottom:0; left:0; right:0; padding:2rem; z-index:20; display:flex; flex-direction:column; align-items:center; text-align:center;',
        'left'   => 'position:absolute; bottom:0; left:0; padding:2rem; z-index:20; max-width:36rem; display:flex; flex-direction:column; align-items:flex-start; text-align:left;',
        default  => 'position:absolute; bottom:0; right:0; padding:2rem; z-index:20; max-width:36rem; display:flex; flex-direction:column; align-items:flex-start; text-align:right;',
    };
?>
<section class="grid grid-cols-1 <?php echo e($showSideBanners ? 'lg:grid-cols-12' : ''); ?> gap-6 h-auto lg:h-[480px]">
    <div class="<?php echo e($showSideBanners ? 'lg:col-span-8' : 'col-span-full'); ?> relative rounded-2xl overflow-hidden group"
         style="background-color: <?php echo e($bgColor); ?>">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
        <?php if($customImage): ?>
            <img alt="hero" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 min-h-[300px]"
                 src="<?php echo e(url('storage/' . $customImage)); ?>"/>
        <?php else: ?>
            <div class="w-full min-h-[300px]" style="background-color: <?php echo e($bgColor); ?>"></div>
        <?php endif; ?>
        <div style="<?php echo e($textContainerStyle); ?>">
            <h2 style="font-size:clamp(1.5rem,4vw,3rem); font-weight:900; margin:0 0 1rem; line-height:1.2; color:<?php echo e($textColor); ?>;">
                <?php echo e($cfg['title'] ?? 'بهترین مزایده‌های آنلاین'); ?>

            </h2>
            <?php if(!empty($cfg['subtitle'])): ?>
                <p style="font-size:1.1rem; margin:0 0 1.5rem; opacity:.85; color:<?php echo e($textColor); ?>;"><?php echo e($cfg['subtitle']); ?></p>
            <?php endif; ?>
            <?php if(!empty($cfg['button_text'])): ?>
            <a href="<?php echo e(!empty($cfg['button_url']) ? $cfg['button_url'] : route('listings.index')); ?>"
               style="display:inline-flex; align-items:center; gap:8px; padding:.75rem 2rem; border-radius:.75rem; font-weight:700; font-size:1.1rem; text-decoration:none; transition:opacity .2s; background-color:<?php echo e($btnBg); ?>; color:<?php echo e($btnTextColor); ?>;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <span><?php echo e($cfg['button_text']); ?></span>
                <span class="material-symbols-outlined" style="font-size:20px; transform:scaleX(-1);">arrow_right_alt</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php if($showSideBanners): ?>
    <div class="lg:col-span-4 flex flex-col gap-6">
        <?php
            $b1Tag   = $cfg['side_banner1_tag']   ?? 'محصولات دیجیتال';
            $b1Title = $cfg['side_banner1_title']  ?? 'گوشی و تبلت';
            $b1Desc  = $cfg['side_banner1_desc']   ?? 'جدیدترین محصولات در مزایده';
            $b1Url   = $cfg['side_banner1_url']    ?? route('listings.index');
            $b1Bg    = $cfg['side_banner1_bg']     ?? '#e0e7ff';
            $b1Color = $cfg['side_banner1_color']  ?? '#3b82f6';
            $b1Img   = $cfg['side_banner1_image']  ?? null;

            $b2Tag   = $cfg['side_banner2_tag']    ?? 'ساعت و جواهرات';
            $b2Title = $cfg['side_banner2_title']  ?? 'ساعت‌های کلاسیک';
            $b2Desc  = $cfg['side_banner2_desc']   ?? 'مزایده برندهای معتبر';
            $b2Url   = $cfg['side_banner2_url']    ?? route('listings.index');
            $b2Bg    = $cfg['side_banner2_bg']     ?? '#fff7ed';
            $b2Color = $cfg['side_banner2_color']  ?? '#f97316';
            $b2Img   = $cfg['side_banner2_image']  ?? null;
        ?>

        
        <a href="<?php echo e($b1Url ?: route('listings.index')); ?>"
           class="flex-1 relative rounded-2xl overflow-hidden flex items-center group"
           style="background-color: <?php echo e($b1Bg); ?>; min-height:0;">
            <div class="flex-1 p-5 z-10 min-w-0">
                <span class="font-bold text-sm block mb-1" style="color: <?php echo e($b1Color); ?>"><?php echo e($b1Tag); ?></span>
                <h3 class="text-lg font-black text-gray-900 mb-1 leading-tight"><?php echo e($b1Title); ?></h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo e($b1Desc); ?></p>
                <span class="font-bold text-sm flex items-center gap-1" style="color: <?php echo e($b1Color); ?>">
                    مشاهده <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span>
                </span>
            </div>
            <?php if($b1Img): ?>
                <div style="width:100px; flex-shrink:0; align-self:stretch; overflow:hidden;">
                    <img src="<?php echo e(url('storage/' . $b1Img)); ?>" alt="<?php echo e($b1Title); ?>"
                         style="width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s;"
                         class="group-hover:scale-105">
                </div>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e($b2Url ?: route('listings.index')); ?>"
           class="flex-1 relative rounded-2xl overflow-hidden flex items-center group"
           style="background-color: <?php echo e($b2Bg); ?>; min-height:0;">
            <div class="flex-1 p-5 z-10 min-w-0">
                <span class="font-bold text-sm block mb-1" style="color: <?php echo e($b2Color); ?>"><?php echo e($b2Tag); ?></span>
                <h3 class="text-lg font-black text-gray-900 mb-1 leading-tight"><?php echo e($b2Title); ?></h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo e($b2Desc); ?></p>
                <span class="font-bold text-sm flex items-center gap-1" style="color: <?php echo e($b2Color); ?>">
                    مشاهده <span class="material-symbols-outlined text-sm rtl:rotate-180">chevron_right</span>
                </span>
            </div>
            <?php if($b2Img): ?>
                <div style="width:100px; flex-shrink:0; align-self:stretch; overflow:hidden;">
                    <img src="<?php echo e(url('storage/' . $b2Img)); ?>" alt="<?php echo e($b2Title); ?>"
                         style="width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s;"
                         class="group-hover:scale-105">
                </div>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>
<?php endif; ?>


<?php if($block['type'] === 'categories'): ?>
<?php
    $dbCats = \App\Models\Category::whereNull('parent_id')->take((int)($cfg['count'] ?? 8))->get();
    $catStyle = $cfg['style'] ?? 'circle';
?>
<section>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900"><?php echo e($cfg['title'] ?? 'دسته‌بندی‌ها'); ?></h2>
        <a class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all" href="<?php echo e(route('categories.index')); ?>">
            مشاهده همه <span class="material-symbols-outlined text-lg rtl:rotate-180">arrow_right_alt</span>
        </a>
    </div>
    <div class="flex gap-6 overflow-x-auto no-scrollbar pb-4 snap-x">
        <?php $__empty_1 = true; $__currentLoopData = $dbCats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('listings.index', ['category' => $cat->slug])); ?>"
           class="group snap-center cursor-pointer flex-shrink-0"
           <?php if($catStyle === 'card'): ?> style="width:120px;" <?php else: ?> style="min-width:100px;" <?php endif; ?>>
            <?php if($catStyle === 'circle'): ?>
                <div class="flex flex-col items-center gap-3">
                    <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-3xl"><?php echo e($cat->icon ?? 'category'); ?></span>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors text-center leading-tight"><?php echo e($cat->name); ?></span>
                </div>
            <?php elseif($catStyle === 'card'): ?>
                <div class="w-full rounded-xl bg-blue-50 group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-center pt-4 pb-2 text-primary group-hover:text-white">
                        <span class="material-symbols-outlined text-2xl"><?php echo e($cat->icon ?? 'category'); ?></span>
                    </div>
                    <div class="px-2 pb-3 text-center">
                        <span class="text-xs font-semibold text-gray-700 group-hover:text-white leading-tight block"><?php echo e($cat->name); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="px-4 py-2 rounded-full bg-blue-50 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 text-sm font-medium whitespace-nowrap">
                    <?php echo e($cat->name); ?>

                </div>
            <?php endif; ?>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php $__currentLoopData = [['devices','دیجیتال'],['checkroom','مد'],['diamond','جواهرات'],['chair','دکوراسیون'],['brush','هنر'],['directions_car','خودرو']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ico,$nm]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="group flex flex-col items-center gap-3 min-w-[100px] snap-center" href="<?php echo e(route('listings.index')); ?>">
            <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm">
                <span class="material-symbols-outlined text-3xl"><?php echo e($ico); ?></span>
            </div>
            <span class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors"><?php echo e($nm); ?></span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>


<?php if($block['type'] === 'auction_list'): ?>
<?php
    $fFilter   = $cfg['filter'] ?? 'ending_soon';
    $fCount    = (int)($cfg['count'] ?? 6);
    $fCols     = (int)($cfg['columns'] ?? 3);
    $fBg       = $cfg['bg_color'] ?? '#1e40af';
    $fTextDark = ($cfg['text_color'] ?? 'white') === 'dark';
    $fTitle    = $cfg['title'] ?? 'آگهی‌های ویژه';
    $fCatIds   = array_filter((array)($cfg['category_ids'] ?? []));
    // همه به integer تبدیل کن
    $fCatIds = array_values(array_map('intval', $fCatIds));

    // اگر دسته‌بندی انتخاب شده، مستقیم از DB بخون تا دقیق‌تر باشه
    if (!empty($fCatIds)) {
        // جمع‌آوری همه ID های زیردسته‌ها
        $allCatIds = $fCatIds;
        foreach ($fCatIds as $catId) {
            $childIds = \App\Models\Category::where('parent_id', $catId)->pluck('id')->toArray();
            $allCatIds = array_merge($allCatIds, array_map('intval', $childIds));
            foreach ($childIds as $childId) {
                $grandChildIds = \App\Models\Category::where('parent_id', $childId)->pluck('id')->toArray();
                $allCatIds = array_merge($allCatIds, array_map('intval', $grandChildIds));
            }
        }
        $allCatIds = array_unique($allCatIds);

        $fQuery = \App\Models\Listing::whereIn('status', ['active', 'ended', 'completed'])
            ->whereIn('category_id', $allCatIds)
            ->with('images', 'category', 'seller')
            ->withCount('bids');

        // active اول، بعد ended/completed
        $activeFirst = "CASE WHEN status = 'active' AND (ends_at IS NULL OR ends_at > NOW()) THEN 0 ELSE 1 END";

        $fListings = match($fFilter) {
            'most_bids'     => $fQuery->orderByRaw($activeFirst)->orderByDesc('bids_count'),
            'highest_price' => $fQuery->orderByRaw($activeFirst)->orderByRaw('COALESCE((SELECT MAX(amount) FROM bids WHERE bids.listing_id = listings.id), starting_price) DESC'),
            'lowest_price'  => $fQuery->orderByRaw($activeFirst)->orderByRaw('COALESCE((SELECT MAX(amount) FROM bids WHERE bids.listing_id = listings.id), starting_price) ASC'),
            'newest'        => $fQuery->orderByRaw($activeFirst)->orderByDesc('created_at'),
            'ending_soon'   => $fQuery->orderByRaw($activeFirst)->orderBy('ends_at'),
            default         => $fQuery->orderByRaw($activeFirst)->orderBy('ends_at'),
        };
        $fListings = $fListings->take($fCount)->get();
    } else {
        // بدون فیلتر دسته‌بندی از collection موجود استفاده کن
        // active اول، بعد ended/completed
        $fListings = $allListings->whereIn('status', ['active', 'ended', 'completed']);
        $fListings = $fListings->sortBy(function($l) use ($fFilter) {
            $isActive = $l->status === 'active' && ($l->ends_at === null || \Carbon\Carbon::parse($l->ends_at)->isFuture());
            $priority = $isActive ? 0 : 1;
            $secondary = match($fFilter) {
                'most_bids'     => -((int)($l->bids_count ?? 0)),
                'highest_price' => -(float)($l->starting_price ?? 0),
                'lowest_price'  => (float)($l->starting_price ?? 0),
                'newest'        => -strtotime($l->created_at ?? ''),
                default         => strtotime($l->ends_at ?? '9999-12-31'),
            };
            return [$priority, $secondary];
        });
        $fListings = $fListings->values()->take($fCount);
    }
    $fColsMap = [2=>'grid-cols-1 sm:grid-cols-2', 3=>'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3', 4=>'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'];

    // ساخت URL داینامیک برای دکمه مشاهده بیشتر
    $viewMoreParams = ['sort' => $fFilter];
    if (!empty($fCatIds)) {
        if (count($fCatIds) === 1) {
            // یک دسته - از slug استفاده کن
            $singleCat = \App\Models\Category::find($fCatIds[0]);
            if ($singleCat) $viewMoreParams['category'] = $singleCat->slug;
        } else {
            // چند دسته - از category_ids[] استفاده کن
            $viewMoreParams['category_ids'] = $fCatIds;
        }
    }
    $viewMoreUrl = route('listings.index', $viewMoreParams);
?>
<?php if($fListings->isNotEmpty()): ?>
<section class="rounded-2xl overflow-hidden p-6" style="background-color: <?php echo e($fBg); ?>">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black <?php echo e($fTextDark ? 'text-gray-900' : 'text-white'); ?>"><?php echo e($fTitle); ?></h2>
            <?php if(!empty($cfg['subtitle'])): ?>
                <p class="<?php echo e($fTextDark ? 'text-gray-600' : 'text-white/70'); ?> text-sm mt-1"><?php echo e($cfg['subtitle']); ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo e($viewMoreUrl); ?>" class="mr-auto text-primary text-sm font-bold flex items-center gap-1" style="text-decoration:none;">
            مشاهده همه <span class="material-symbols-outlined text-sm rtl:rotate-180">arrow_right_alt</span>
        </a>
    </div>
    <div class="grid <?php echo e($fColsMap[$fCols] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'); ?> gap-4">
        <?php $__currentLoopData = $fListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-card','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce)): ?>
<?php $attributes = $__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce; ?>
<?php unset($__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce)): ?>
<?php $component = $__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce; ?>
<?php unset($__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>


<?php if($block['type'] === 'trust_badges'): ?>
<?php
    $badges = collect($cfg['badges'] ?? []);
    $badgeCount = $badges->count();
    // max 4 per row on desktop, responsive on smaller screens
    $badgeCols = min($badgeCount, 4);
?>
<?php if($badges->isNotEmpty()): ?>
<section style="display:grid; grid-template-columns:repeat(<?php echo e($badgeCols); ?>, 1fr); gap:1.5rem; padding:2rem 0; border-top:1px solid #e5e7eb;"
         class="trust-badges-grid">
    <?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="flex items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="w-12 h-12 bg-blue-50 text-primary rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-3xl"><?php echo e($badge['icon'] ?? 'verified'); ?></span>
        </div>
        <div>
            <h3 class="font-bold text-gray-900"><?php echo e($badge['title'] ?? ''); ?></h3>
            <p class="text-xs text-gray-500 mt-1"><?php echo e($badge['desc'] ?? ''); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>
<?php endif; ?>


<?php if($block['type'] === 'stats'): ?>
<?php
    $statItems = collect($cfg['items'] ?? []);
    $statCount = $statItems->count();
    $statCols = min($statCount, 6);
?>
<?php if($statItems->isNotEmpty()): ?>
<section style="display:grid; grid-template-columns:repeat(<?php echo e($statCols); ?>, 1fr); gap:1.5rem;"
         class="stats-grid">
    <?php $__currentLoopData = $statItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="material-symbols-outlined text-2xl"><?php echo e($stat['icon'] ?? 'star'); ?></span>
        </div>
        <div class="text-3xl font-black text-gray-900 mb-1"><?php echo e($stat['value'] ?? ''); ?></div>
        <div class="text-sm text-gray-500"><?php echo e($stat['label'] ?? ''); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>
<?php endif; ?>


<?php if($block['type'] === 'newsletter'): ?>
<?php
    $nlTitle = $cfg['title'] ?? 'عضویت در خبرنامه';
    $nlSubtitle = $cfg['subtitle'] ?? 'از جدیدترین مزایده‌ها باخبر شوید';
    $nlBg = $cfg['bg_color'] ?? '#1e40af';
?>
<section class="newsletter-section rounded-2xl overflow-hidden" style="background:linear-gradient(135deg, <?php echo e($nlBg); ?> 0%, <?php echo e($nlBg); ?>dd 100%);">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center p-8 md:p-12">
        <div class="text-white">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-4">
                <span class="material-symbols-outlined text-lg">mail</span>
                <span class="text-sm font-semibold">خبرنامه</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-black mb-3 leading-tight"><?php echo e($nlTitle); ?></h2>
            <p class="text-white/90 text-base md:text-lg"><?php echo e($nlSubtitle); ?></p>
        </div>
        <div>
            <form id="newsletter-form-<?php echo e($block['id']); ?>" class="flex flex-col sm:flex-row gap-3" onsubmit="subscribeNewsletter(event, '<?php echo e($block['id']); ?>')">
                <?php echo csrf_field(); ?>
                <input type="email" name="email" required placeholder="ایمیل شما"
                       class="flex-1 px-5 py-4 rounded-xl text-gray-900 border-2 border-transparent focus:outline-none focus:border-white/50 shadow-lg">
                <button type="submit" class="bg-white text-gray-900 font-bold px-8 py-4 rounded-xl hover:shadow-2xl transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>عضویت</span>
                    <span class="material-symbols-outlined text-lg">arrow_left</span>
                </button>
            </form>
            <div id="newsletter-message-<?php echo e($block['id']); ?>" class="mt-3 text-sm text-white/90 hidden"></div>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($block['type'] === 'banner'): ?>
<?php
    $bannerItems = $cfg['banners'] ?? [];
    // backward compat: اگه config قدیمی بود، یه بنر از اون بساز
    if (empty($bannerItems) && (!empty($cfg['title']) || !empty($cfg['custom_image']))) {
        $bannerItems = [[
            'title'       => $cfg['title'] ?? '',
            'subtitle'    => $cfg['subtitle'] ?? '',
            'button_text' => $cfg['button_text'] ?? '',
            'button_url'  => $cfg['button_url'] ?? '',
            'bg_color'    => $cfg['bg_color'] ?? '#f59e0b',
            'custom_image'=> $cfg['custom_image'] ?? '',
        ]];
    }
    $bannerCount = count($bannerItems);
?>
<?php if($bannerCount > 0): ?>
<?php
    // max 4 per row on desktop
    $bannerCols = min($bannerCount, 4);
?>
<section style="display:grid; grid-template-columns:repeat(<?php echo e($bannerCols); ?>, 1fr); gap:1rem;" class="banner-grid">
    <?php $__currentLoopData = $bannerItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $bnBg    = $bn['bg_color'] ?? '#f59e0b';
        $bnImg   = $bn['custom_image'] ?? '';
        $bnTitle = $bn['title'] ?? '';
        $bnSub   = $bn['subtitle'] ?? '';
        $bnBtnTxt= $bn['button_text'] ?? '';
        $bnBtnUrl= $bn['button_url'] ?? route('listings.index');
    ?>
    
    <a href="<?php echo e($bnBtnUrl ?: route('listings.index')); ?>"
       class="rounded-2xl overflow-hidden relative flex items-center justify-between p-6 min-h-[140px] group cursor-pointer"
       style="background-color: <?php echo e($bnBg); ?>; text-decoration:none;">
        <?php if($bnImg): ?>
            <img src="<?php echo e(url('storage/' . $bnImg)); ?>" class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:opacity-50 transition-opacity">
        <?php endif; ?>
        <div class="text-white relative z-10 flex-1 min-w-0">
            <h2 class="text-2xl font-black mb-1 leading-tight"><?php echo e($bnTitle); ?></h2>
            <?php if($bnSub): ?><p class="text-white/80"><?php echo e($bnSub); ?></p><?php endif; ?>
        </div>
        <?php if($bnBtnTxt): ?>
        <span class="bg-white text-gray-900 font-bold px-6 py-2.5 rounded-xl hover:shadow-lg transition-all flex-shrink-0 relative z-10 mr-4 text-sm whitespace-nowrap">
            <?php echo e($bnBtnTxt); ?>

        </span>
        <?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>
<?php endif; ?>


<?php if($block['type'] === 'text_block'): ?>
<section class="py-4" style="text-align: <?php echo e($cfg['align'] ?? 'center'); ?>">
    <div class="prose max-w-none text-gray-700"><?php echo nl2br(e($cfg['content'] ?? '')); ?></div>
</section>
<?php endif; ?>


<?php if($block['type'] === 'divider'): ?>
<hr style="border-color: <?php echo e($cfg['color'] ?? '#e5e7eb'); ?>">
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
// اسلایدر hero
const _sliderState = {};

function slideHero(id, dir) {
    const slides = document.querySelectorAll(`.hero-slide[data-slider="${id}"]`);
    const dots   = document.querySelectorAll(`.hero-dot[data-slider="${id}"]`);
    if (!slides.length) return;

    if (!_sliderState[id]) _sliderState[id] = 0;
    let cur = _sliderState[id];

    // پنهان کردن اسلاید فعلی
    slides[cur].style.opacity = '0';
    slides[cur].style.pointerEvents = 'none';
    if (dots[cur]) { dots[cur].style.width = '8px'; dots[cur].style.background = 'rgba(255,255,255,.4)'; }

    // محاسبه اسلاید بعدی
    cur = (cur + dir + slides.length) % slides.length;
    _sliderState[id] = cur;

    // نمایش اسلاید جدید
    slides[cur].style.opacity = '1';
    slides[cur].style.pointerEvents = 'auto';
    if (dots[cur]) { dots[cur].style.width = '24px'; dots[cur].style.background = 'white'; }
}

function goToSlide(id, index) {
    const slides = document.querySelectorAll(`.hero-slide[data-slider="${id}"]`);
    const dots   = document.querySelectorAll(`.hero-dot[data-slider="${id}"]`);
    if (!slides.length) return;

    const cur = _sliderState[id] || 0;
    slides[cur].style.opacity = '0';
    slides[cur].style.pointerEvents = 'none';
    if (dots[cur]) { dots[cur].style.width = '8px'; dots[cur].style.background = 'rgba(255,255,255,.4)'; }

    _sliderState[id] = index;
    slides[index].style.opacity = '1';
    slides[index].style.pointerEvents = 'auto';
    if (dots[index]) { dots[index].style.width = '24px'; dots[index].style.background = 'white'; }
}

// auto-play هر ۵ ثانیه
document.querySelectorAll('[id^="hero-slider-"]').forEach(el => {
    const id = el.id;
    const slides = el.querySelectorAll('.hero-slide');
    if (slides.length > 1) {
        setInterval(() => slideHero(id, 1), 5000);
    }
});
</script>

<script>
function subscribeNewsletter(e, blockId) {
    e.preventDefault();
    const form = document.getElementById('newsletter-form-' + blockId);
    const msg = document.getElementById('newsletter-message-' + blockId);
    const btn = form.querySelector('button[type=submit]');
    const email = form.querySelector('input[name=email]').value;

    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">refresh</span>';

    fetch('<?php echo e(route("newsletter.subscribe")); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json'},
        body: JSON.stringify({email})
    })
    .then(r => r.json())
    .then(data => {
        msg.textContent = data.message;
        msg.classList.remove('hidden');
        if (data.success) {
            form.reset();
            msg.style.color = '#86efac';
        } else {
            msg.style.color = '#fca5a5';
        }
    })
    .catch(() => {
        msg.textContent = 'خطا در ارسال. لطفاً دوباره تلاش کنید.';
        msg.classList.remove('hidden');
        msg.style.color = '#fca5a5';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span>عضویت</span><span class="material-symbols-outlined text-lg">arrow_left</span>';
    });
}
</script>

<?php $__env->startPush('styles'); ?>
<style>
/* Trust badges responsive */
@media (max-width: 768px) {
    .trust-badges-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 480px) {
    .trust-badges-grid { grid-template-columns: 1fr !important; }
}
/* Stats responsive */
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
/* Banner responsive */
@media (max-width: 768px) {
    .banner-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 480px) {
    .banner-grid { grid-template-columns: 1fr !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\listings\index.blade.php ENDPATH**/ ?>