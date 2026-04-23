<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function index()
    {
        $settings = $this->getThemeSettings();
        return view('admin.theme.index', compact('settings'));
    }

    public function save(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Handle logo upload
        if ($request->hasFile('header_logo_file')) {
            $path = $request->file('header_logo_file')->store('theme', 'public');
            $data['header_logo'] = $path;
        }

        // Handle footer logo upload
        if ($request->hasFile('footer_logo_file')) {
            $path = $request->file('footer_logo_file')->store('theme', 'public');
            $data['footer_logo'] = $path;
        }

        // Handle footer trust image upload
        if ($request->hasFile('footer_trust_image_file')) {
            $path = $request->file('footer_trust_image_file')->store('theme', 'public');
            $data['footer_trust_image'] = $path;
        }

        // Handle dashboard logo upload
        if ($request->hasFile('dashboard_logo_file')) {
            $path = $request->file('dashboard_logo_file')->store('theme', 'public');
            $data['dashboard_logo'] = $path;
        }

        // Save nav links as JSON
        if (isset($data['header_nav_links'])) {
            $links = [];
            foreach ($data['header_nav_links']['label'] ?? [] as $i => $label) {
                if (!empty($label)) {
                    $links[] = [
                        'label' => $label,
                        'url'   => $data['header_nav_links']['url'][$i] ?? '#',
                        'icon'  => $data['header_nav_links']['icon'][$i] ?? '',
                    ];
                }
            }
            SiteSetting::set('theme_header_nav_links', json_encode($links, JSON_UNESCAPED_UNICODE), 'json');
            unset($data['header_nav_links']);
        }

        // Save footer bottom links as JSON
        if (isset($data['footer_bottom_links'])) {
            $blinks = [];
            foreach ($data['footer_bottom_links']['label'] ?? [] as $i => $label) {
                if (!empty($label)) {
                    $blinks[] = [
                        'label' => $label,
                        'url'   => $data['footer_bottom_links']['url'][$i] ?? '#',
                    ];
                }
            }
            SiteSetting::set('theme_footer_bottom_links', json_encode($blinks, JSON_UNESCAPED_UNICODE), 'json');
            unset($data['footer_bottom_links']);
        }
        if (isset($data['footer_columns'])) {
            SiteSetting::set('theme_footer_columns', json_encode($data['footer_columns'], JSON_UNESCAPED_UNICODE), 'json');
            unset($data['footer_columns']);
        }

        // Save footer social links as JSON
        if (isset($data['footer_social'])) {
            $socials = [];
            foreach ($data['footer_social']['icon'] ?? [] as $i => $icon) {
                if (!empty($icon)) {
                    $socials[] = [
                        'icon' => $icon,
                        'url'  => $data['footer_social']['url'][$i] ?? '#',
                    ];
                }
            }
            SiteSetting::set('theme_footer_social', json_encode($socials, JSON_UNESCAPED_UNICODE), 'json');
            unset($data['footer_social']);
        }

        // Save dashboard sidebar links as JSON
        if (isset($data['dashboard_sidebar_links'])) {
            SiteSetting::set('theme_dashboard_sidebar_links', json_encode($data['dashboard_sidebar_links'], JSON_UNESCAPED_UNICODE), 'json');
            unset($data['dashboard_sidebar_links']);
        }

        // Save remaining scalar settings
        foreach ($data as $key => $value) {
            if (!str_starts_with($key, 'header_logo_file') && !str_starts_with($key, 'footer_logo_file') && !str_starts_with($key, 'dashboard_logo_file')) {
                SiteSetting::set('theme_' . $key, $value ?? '');
            }
        }

        return back()->with('success', 'تنظیمات ظاهر سایت ذخیره شد.');
    }

    private function getThemeSettings(): array
    {
        $defaults = [
            // Header
            'header_bg'           => '#ffffff',
            'header_text_color'   => '#0d121b',
            'header_logo'         => '',
            'header_logo_text'    => 'پرشینآکشن',
            'header_logo_icon'    => 'gavel',
            'header_logo_size'    => '40',
            'header_show_search'  => '1',
            'header_show_cats'    => '1',
            'header_nav_links'    => '[]',
            'header_sticky'       => '1',
            'header_height'       => '80',
            // Footer
            'footer_bg'           => '#ffffff',
            'footer_text_color'   => '#6b7280',
            'footer_logo'         => '',
            'footer_logo_text'    => 'پرشینآکشن',
            'footer_logo_icon'    => 'gavel',
            'footer_logo_size'    => '32',
            'footer_description'  => 'اولین و بزرگترین پلتفرم برگزاری مزایدات آنلاین در ایران.',
            'footer_copyright'    => 'تمامی حقوق این وبسایت محفوظ است © ۱۴۰۳',
            'footer_privacy_text' => 'حریم خصوصی',
            'footer_privacy_url'  => '#',
            'footer_terms_text'   => 'شرایط استفاده',
            'footer_terms_url'    => '#',
            'footer_bottom_links' => '[]',
            'footer_trust_html'   => '',
            'footer_trust_image'  => '',
            'footer_columns'      => '[]',
            'footer_social'       => '[]',
            'footer_show'         => '1',
            // Dashboard
            'dashboard_bg'        => '#ffffff',
            'dashboard_sidebar_bg'=> '#ffffff',
            'dashboard_logo'      => '',
            'dashboard_logo_text' => 'حراجآنلاین',
            'dashboard_logo_icon' => 'storefront',
            'dashboard_primary'   => '#3b82f6',
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $val = SiteSetting::get('theme_' . $key, $default);
            $settings[$key] = $val;
        }

        // Parse JSON fields
        foreach (['header_nav_links', 'footer_columns', 'footer_social', 'dashboard_sidebar_links'] as $jsonKey) {
            if (isset($settings[$jsonKey]) && is_string($settings[$jsonKey])) {
                $settings[$jsonKey] = json_decode($settings[$jsonKey], true) ?? [];
            }
        }

        return $settings;
    }
}
