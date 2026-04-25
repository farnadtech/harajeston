<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateController extends Controller
{
    // آدرس سرور آپدیت شما — بعداً تغییر بدید
    const UPDATE_SERVER = 'https://iranbooklet.ir';

    /**
     * نسخه فعلی نصب شده
     */
    public function currentVersion(): string
    {
        $path = base_path('version.json');
        if (!file_exists($path)) return '1.0.0';
        return json_decode(file_get_contents($path), true)['version'] ?? '1.0.0';
    }

    /**
     * صفحه مدیریت آپدیت
     */
    public function index()
    {
        $current = $this->currentVersion();
        $latest  = null;
        $changelog = null;
        $hasUpdate = false;
        $error = null;

        try {
            $response = Http::timeout(8)->get(self::UPDATE_SERVER . '/version.json');
            if ($response->successful()) {
                $data      = $response->json();
                $latest    = $data['version'] ?? null;
                $changelog = $data['changelog'] ?? null;
                $hasUpdate = $latest && version_compare($latest, $current, '>');
            }
        } catch (\Exception $e) {
            $error = 'اتصال به سرور آپدیت برقرار نشد.';
        }

        return view('admin.update.index', compact('current', 'latest', 'hasUpdate', 'changelog', 'error'));
    }

    /**
     * دانلود و اجرای آپدیت
     */
    public function run(Request $request)
    {
        try {
            // ۱. گرفتن اطلاعات آپدیت از سرور
            $meta = Http::timeout(15)->get(self::UPDATE_SERVER . '/version.json')->json();
            $downloadUrl = $meta['download_url'] ?? null;
            $newVersion  = $meta['version'] ?? null;

            if (!$downloadUrl || !$newVersion) {
                return back()->with('error', 'اطلاعات آپدیت ناقص است.');
            }

            if (!version_compare($newVersion, $this->currentVersion(), '>')) {
                return back()->with('info', 'شما از آخرین نسخه استفاده می‌کنید.');
            }

            // ۲. دانلود zip
            $zipPath = storage_path("updates/update-v{$newVersion}.zip");
            if (!is_dir(storage_path('updates'))) {
                mkdir(storage_path('updates'), 0755, true);
            }

            $zipContent = Http::timeout(120)->get($downloadUrl)->body();
            file_put_contents($zipPath, $zipContent);

            // ۳. بکاپ فایل‌های قدیمی
            $this->backupFiles($zipPath, $newVersion);

            // ۴. extract و جایگزینی فایل‌ها
            $this->extractUpdate($zipPath);

            // ۵. اجرای migration های جدید
            Artisan::call('migrate', ['--force' => true]);

            // ۶. پاک کردن cache
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            // ۷. آپدیت version.json
            file_put_contents(base_path('version.json'), json_encode([
                'version'     => $newVersion,
                'released_at' => now()->toDateString(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // ۸. پاک کردن فایل zip
            @unlink($zipPath);

            Log::info("Update applied: v{$newVersion}");

            return back()->with('success', "آپدیت به نسخه {$newVersion} با موفقیت انجام شد.");

        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            return back()->with('error', 'خطا در اجرای آپدیت: ' . $e->getMessage());
        }
    }

    /**
     * بکاپ فایل‌هایی که قرار است جایگزین شوند
     */
    private function backupFiles(string $zipPath, string $version): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) return;

        // خواندن manifest
        $manifestIndex = $zip->locateName('manifest.json');
        if ($manifestIndex === false) { $zip->close(); return; }

        $manifest = json_decode($zip->getFromIndex($manifestIndex), true);
        $zip->close();

        $backupDir = storage_path("updates/backup-v{$version}");
        if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

        foreach ($manifest['files'] ?? [] as $file) {
            $src = base_path($file);
            if (!file_exists($src)) continue;
            $dest = $backupDir . '/' . $file;
            $destDir = dirname($dest);
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            copy($src, $dest);
        }
    }

    /**
     * extract کردن zip و جایگزینی فایل‌ها
     */
    private function extractUpdate(string $zipPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('باز کردن فایل zip ناموفق بود.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            // manifest رو skip کن
            if ($name === 'manifest.json') continue;

            // پوشه‌ها رو skip کن
            if (str_ends_with($name, '/')) continue;

            $targetPath = base_path($name);
            $targetDir  = dirname($targetPath);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            file_put_contents($targetPath, $zip->getFromIndex($i));
        }

        $zip->close();
    }
}
