<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageController extends Controller
{
    // تعریف انواع block های موجود
    public static function blockTypes(): array
    {
        return [
            'hero'          => ['label' => 'بنر اصلی',          'icon' => 'image',              'color' => 'blue'],
            'categories'    => ['label' => 'دسته‌بندی‌ها',       'icon' => 'category',           'color' => 'purple'],
            'auction_list'  => ['label' => 'لیست حراجی‌ها',      'icon' => 'gavel',              'color' => 'red'],
            'trust_badges'  => ['label' => 'نشان‌های اعتماد',    'icon' => 'verified',           'color' => 'green'],
            'stats'         => ['label' => 'آمار سایت',          'icon' => 'bar_chart',          'color' => 'indigo'],
            'newsletter'    => ['label' => 'خبرنامه',            'icon' => 'mail',               'color' => 'pink'],
            'banner'        => ['label' => 'بنر تبلیغاتی',       'icon' => 'campaign',           'color' => 'yellow'],
            'text_block'    => ['label' => 'متن آزاد',           'icon' => 'text_fields',        'color' => 'gray'],
            'divider'       => ['label' => 'خط جداکننده',        'icon' => 'horizontal_rule',    'color' => 'gray'],
        ];
    }

    public function index()
    {
        $blocks    = json_decode(HomepageSetting::get('blocks', '[]'), true) ?? [];
        $cardStyle = HomepageSetting::get('card_style', 'classic');
        $blockTypes = self::blockTypes();

        return view('admin.homepage.index', compact('blocks', 'cardStyle', 'blockTypes'));
    }

    // ذخیره کل لیست blocks (ترتیب + فعال/غیرفعال)
    public function saveBlocks(Request $request)
    {
        $blocks = $request->input('blocks', []);
        HomepageSetting::set('blocks', json_encode($blocks, JSON_UNESCAPED_UNICODE));
        HomepageSetting::clearCache();
        return response()->json(['success' => true]);
    }

    // اضافه کردن یک block جدید
    public function addBlock(Request $request)
    {
        $type   = $request->input('type');
        $types  = self::blockTypes();
        if (!isset($types[$type])) {
            return response()->json(['error' => 'نوع نامعتبر'], 422);
        }

        $blocks = json_decode(HomepageSetting::get('blocks', '[]'), true) ?? [];
        $newBlock = [
            'id'      => 'block_' . Str::random(8),
            'type'    => $type,
            'enabled' => true,
            'config'  => self::defaultConfig($type),
        ];
        $blocks[] = $newBlock;
        HomepageSetting::set('blocks', json_encode($blocks, JSON_UNESCAPED_UNICODE));
        HomepageSetting::clearCache();

        return response()->json(['success' => true, 'block' => $newBlock]);
    }

    // حذف یک block
    public function deleteBlock(Request $request, string $blockId)
    {
        $blocks = json_decode(HomepageSetting::get('blocks', '[]'), true) ?? [];
        $blocks = array_values(array_filter($blocks, fn($b) => $b['id'] !== $blockId));
        HomepageSetting::set('blocks', json_encode($blocks, JSON_UNESCAPED_UNICODE));
        HomepageSetting::clearCache();
        return response()->json(['success' => true]);
    }

    // ذخیره config یک block
    public function updateBlock(Request $request, string $blockId)
    {
        $blocks = json_decode(HomepageSetting::get('blocks', '[]'), true) ?? [];
        $found = false;

        foreach ($blocks as &$block) {
            if ($block['id'] === $blockId) {
                $found = true;
                // JSON body یا form-data
                $config = $request->input('config', []);

                // اگر config خالیه ولی raw JSON داره
                if (empty($config) && $request->isJson()) {
                    $data = $request->json()->all();
                    $config = $data['config'] ?? [];
                }

                // آپلود عکس (فقط در form-data)
                if ($request->hasFile('hero_image')) {
                    $path = $request->file('hero_image')->store('homepage', 'public');
                    $config['custom_image'] = $path;
                } elseif ($request->input('config.remove_image')) {
                    $config['custom_image'] = null;
                } elseif (!isset($config['custom_image'])) {
                    $config['custom_image'] = $block['config']['custom_image'] ?? null;
                }

                // آپلود تصاویر بنرها (banner_image_0, banner_image_1, ...)
                if (isset($config['banners']) && is_array($config['banners'])) {
                    foreach ($config['banners'] as $idx => &$banner) {
                        $fileKey = 'banner_image_' . $idx;
                        if ($request->hasFile($fileKey)) {
                            $path = $request->file($fileKey)->store('homepage', 'public');
                            $banner['custom_image'] = $path;
                        } elseif (!isset($banner['custom_image'])) {
                            // حفظ تصویر قبلی
                            $banner['custom_image'] = $block['config']['banners'][$idx]['custom_image'] ?? '';
                        }
                    }
                    unset($banner);
                }

                $block['config'] = $config;
                break;
            }
        }

        if (!$found) {
            return response()->json(['success' => false, 'message' => 'block not found'], 404);
        }

        HomepageSetting::set('blocks', json_encode($blocks, JSON_UNESCAPED_UNICODE));
        HomepageSetting::clearCache();

        return response()->json(['success' => true]);
    }

    // آپلود عکس برای یک block
    public function uploadBlockImage(Request $request, string $blockId)
    {
        $blocks = json_decode(HomepageSetting::get('blocks', '[]'), true) ?? [];
        foreach ($blocks as &$block) {
            if ($block['id'] === $blockId) {
                // hero image اصلی
                if ($request->hasFile('hero_image')) {
                    $request->validate(['hero_image' => 'image|max:10240']);
                    $block['config']['custom_image'] = $request->file('hero_image')->store('homepage', 'public');
                }
                // بنر کناری ۱
                if ($request->hasFile('side_banner1_image')) {
                    $request->validate(['side_banner1_image' => 'image|max:5120']);
                    $block['config']['side_banner1_image'] = $request->file('side_banner1_image')->store('homepage', 'public');
                }
                // بنر کناری ۲
                if ($request->hasFile('side_banner2_image')) {
                    $request->validate(['side_banner2_image' => 'image|max:5120']);
                    $block['config']['side_banner2_image'] = $request->file('side_banner2_image')->store('homepage', 'public');
                }
                // banner block images (banner_image_0, banner_image_1, ...)
                if (isset($block['config']['banners']) && is_array($block['config']['banners'])) {
                    foreach ($block['config']['banners'] as $idx => &$banner) {
                        $fileKey = 'banner_image_' . $idx;
                        if ($request->hasFile($fileKey)) {
                            $banner['custom_image'] = $request->file($fileKey)->store('homepage', 'public');
                        }
                    }
                    unset($banner);
                }
                break;
            }
        }
        HomepageSetting::set('blocks', json_encode($blocks, JSON_UNESCAPED_UNICODE));
        HomepageSetting::clearCache();
        return back()->with('success', 'تصویر آپلود شد.');
    }

    // ذخیره سبک کارت
    public function updateCardStyle(Request $request)
    {
        $request->validate(['card_style' => 'required|in:classic,modern,minimal,horizontal']);
        HomepageSetting::set('card_style', $request->card_style);
        HomepageSetting::clearCache();
        return back()->with('success', 'سبک کارت محصول ذخیره شد.');
    }

    // config پیش‌فرض هر نوع block
    public static function defaultConfig(string $type): array
    {
        return match($type) {
            'hero' => [
                'mode'              => 'image',   // 'image' یا 'listings'
                'title'             => 'بهترین مزایده‌های آنلاین',
                'subtitle'          => 'هزاران کالای منحصربه‌فرد در انتظار شماست',
                'button_text'       => 'مشاهده مزایده‌ها',
                'bg_color'          => '#1e40af',
                'show_side_banners' => true,
                'custom_image'      => null,
                // حالت لیست محصولات
                'listings_title'    => 'محصولات ویژه',
                'listings_filter'   => 'ending_soon',
                'listings_count'    => 6,
                'listings_columns'  => 3,
                'listings_bg'       => '#1e40af',
            ],
            'categories' => [
                'title' => 'دسته‌بندی‌های محبوب',
                'count' => 8,
                'style' => 'circle',
            ],
            'listings_grid' => [
                'title'      => 'مزایده‌های داغ',
                'subtitle'   => '',
                'icon'       => 'local_fire_department',
                'icon_bg'    => 'bg-red-100',
                'icon_color' => 'text-red-600',
                'count'      => 8,
                'columns'    => 4,
                'filter'     => 'active',
                'sort'       => 'ending_soon',
            ],
            'auction_list' => [
                'title'       => 'لیست حراجی‌ها',
                'subtitle'    => '',
                'filter'      => 'ending_soon',
                'count'       => 6,
                'columns'     => 3,
                'category_ids'=> [],
                'bg_color'    => '#1e40af',
                'text_color'  => 'white',
                'show_header' => true,
            ],
            'trust_badges' => [
                'badges' => [
                    ['icon' => 'verified_user',  'title' => 'ضمانت اصالت کالا',      'desc' => 'تایید کارشناسی تمامی کالاها'],
                    ['icon' => 'local_shipping', 'title' => 'ارسال سریع و بیمه شده', 'desc' => 'ارسال به سراسر کشور'],
                    ['icon' => 'support_agent',  'title' => 'پشتیبانی ۲۴ ساعته',    'desc' => 'پاسخگویی در تمام مراحل'],
                ],
            ],
            'stats' => [
                'items' => [
                    ['icon' => 'gavel',  'value' => '۱۰,۰۰۰+', 'label' => 'مزایده موفق'],
                    ['icon' => 'people', 'value' => '۵۰,۰۰۰+', 'label' => 'کاربر فعال'],
                    ['icon' => 'store',  'value' => '۲,۰۰۰+',  'label' => 'فروشنده معتبر'],
                    ['icon' => 'star',   'value' => '۴.۸',      'label' => 'امتیاز رضایت'],
                ],
            ],
            'newsletter' => [
                'title'    => 'عضویت در خبرنامه',
                'subtitle' => 'از جدیدترین مزایده‌ها باخبر شوید',
                'bg_color' => '#1e40af',
            ],
            'banner' => [
                'title'       => 'پیشنهاد ویژه',
                'subtitle'    => 'تخفیف‌های استثنایی',
                'button_text' => 'مشاهده',
                'button_url'  => '',
                'bg_color'    => '#f59e0b',
                'custom_image'=> null,
            ],
            'text_block' => [
                'content' => 'متن دلخواه خود را اینجا وارد کنید.',
                'align'   => 'center',
                'bg'      => 'transparent',
            ],
            'divider' => [
                'style' => 'line',
                'color' => '#e5e7eb',
            ],
            default => [],
        };
    }
}
