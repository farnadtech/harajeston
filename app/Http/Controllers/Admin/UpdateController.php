<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateController extends Controller
{
    const UPDATE_SERVER = 'https://iranbooklet.ir/harajino';

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    public function currentVersion(): string
    {
        $path = base_path('version.json');
        if (!file_exists($path)) return '1.0.0';
        return json_decode(file_get_contents($path), true)['version'] ?? '1.0.0';
    }

    private function backupsDir(): string
    {
        $dir = storage_path('backups');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return $dir;
    }

    private function updatesDir(): string
    {
        $dir = storage_path('updates');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return $dir;
    }

    // ─────────────────────────────────────────
    // صفحه اصلی آپدیت
    // ─────────────────────────────────────────

    public function index()
    {
        $current   = $this->currentVersion();
        $latest    = null;
        $changelog = null;
        $hasUpdate = false;
        $error     = null;

        try {
            $ctx = stream_context_create(['http' => ['timeout' => 8]]);
            $json = @file_get_contents(self::UPDATE_SERVER . '/version.json', false, $ctx);
            if ($json) {
                $data      = json_decode($json, true);
                $latest    = $data['version'] ?? null;
                $changelog = $data['changelog'] ?? null;
                $hasUpdate = $latest && version_compare($latest, $current, '>');
            } else {
                $error = 'اتصال به سرور آپدیت برقرار نشد.';
            }
        } catch (\Exception $e) {
            $error = 'خطا در بررسی آپدیت: ' . $e->getMessage();
        }

        // لیست بکاپ‌های موجود برای rollback
        $backups = $this->listBackups();

        return view('admin.update.index', compact(
            'current', 'latest', 'hasUpdate', 'changelog', 'error', 'backups'
        ));
    }

    // ─────────────────────────────────────────
    // آپدیت خودکار از سرور
    // ─────────────────────────────────────────

    public function run(Request $request)
    {
        try {
            $json = @file_get_contents(self::UPDATE_SERVER . '/version.json',
                false, stream_context_create(['http' => ['timeout' => 15]]));

            if (!$json) throw new \Exception('دریافت اطلاعات از سرور ناموفق بود.');

            $meta        = json_decode($json, true);
            $downloadUrl = $meta['download_url'] ?? null;
            $newVersion  = $meta['version'] ?? null;

            if (!$downloadUrl || !$newVersion) {
                return back()->with('error', 'اطلاعات آپدیت ناقص است.');
            }

            if (!version_compare($newVersion, $this->currentVersion(), '>')) {
                return back()->with('info', 'شما از آخرین نسخه استفاده می‌کنید.');
            }

            // دانلود zip
            $zipPath = $this->updatesDir() . "/update-v{$newVersion}.zip";
            $content = @file_get_contents($downloadUrl,
                false, stream_context_create(['http' => ['timeout' => 120]]));

            if (!$content) throw new \Exception('دانلود فایل آپدیت ناموفق بود.');
            file_put_contents($zipPath, $content);

            $this->applyUpdate($zipPath, $newVersion);

            return back()->with('success', "آپدیت به نسخه {$newVersion} با موفقیت انجام شد.");

        } catch (\Exception $e) {
            Log::error('Auto update failed: ' . $e->getMessage());
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // آپلود دستی zip
    // ─────────────────────────────────────────

    public function upload(Request $request)
    {
        $request->validate(['zip_file' => 'required|file|mimes:zip|max:102400']);

        try {
            $zipPath = $this->updatesDir() . '/manual-update.zip';
            $request->file('zip_file')->move($this->updatesDir(), 'manual-update.zip');

            // خواندن نسخه از manifest
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) throw new \Exception('فایل zip معتبر نیست.');
            $manifestJson = $zip->getFromName('manifest.json');
            $zip->close();

            if (!$manifestJson) throw new \Exception('manifest.json در zip یافت نشد.');
            $manifest   = json_decode($manifestJson, true);
            $newVersion = $manifest['version'] ?? null;
            if (!$newVersion) throw new \Exception('نسخه در manifest مشخص نشده.');

            $this->applyUpdate($zipPath, $newVersion);

            return back()->with('success', "آپدیت دستی به نسخه {$newVersion} با موفقیت انجام شد.");

        } catch (\Exception $e) {
            Log::error('Manual update failed: ' . $e->getMessage());
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // Rollback به نسخه قبلی
    // ─────────────────────────────────────────

    public function rollback(Request $request)
    {
        $backupName = $request->input('backup');
        $backupDir  = $this->backupsDir() . '/' . $backupName;

        if (!$backupName || !is_dir($backupDir)) {
            return back()->with('error', 'بکاپ انتخاب شده یافت نشد.');
        }

        try {
            // بازگردانی فایل‌ها
            $this->restoreFiles($backupDir);

            // بازگردانی دیتابیس
            $dbDump = $backupDir . '/database.sql';
            if (file_exists($dbDump)) {
                $this->restoreDatabase($dbDump);
            }

            // بازگردانی version.json
            $versionFile = $backupDir . '/version.json';
            if (file_exists($versionFile)) {
                copy($versionFile, base_path('version.json'));
            }

            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            $restoredVersion = $this->currentVersion();
            Log::info("Rollback to: {$backupName}");

            return back()->with('success', "بازگردانی به نسخه {$restoredVersion} با موفقیت انجام شد.");

        } catch (\Exception $e) {
            Log::error('Rollback failed: ' . $e->getMessage());
            return back()->with('error', 'خطا در بازگردانی: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // حذف بکاپ
    // ─────────────────────────────────────────

    public function deleteBackup(Request $request)
    {
        $backupName = $request->input('backup');
        $backupDir  = $this->backupsDir() . '/' . $backupName;

        if ($backupName && is_dir($backupDir)) {
            $this->deleteDir($backupDir);
        }

        return back()->with('success', 'بکاپ حذف شد.');
    }

    // ─────────────────────────────────────────
    // منطق اصلی آپدیت
    // ─────────────────────────────────────────

    private function applyUpdate(string $zipPath, string $newVersion): void
    {
        // ۱. اعتبارسنجی zip
        $zip = new ZipArchive();
        $result = $zip->open($zipPath);
        if ($result !== true) {
            throw new \Exception("فایل zip معتبر نیست. کد خطا: {$result}");
        }
        $zip->close();

        // ۲. بکاپ کامل قبل از آپدیت
        $currentVersion = $this->currentVersion();
        $backupName     = "backup-v{$currentVersion}-" . date('Ymd-His');
        $this->createFullBackup($backupName, $zipPath);

        // ۳. extract فایل‌ها
        $this->extractUpdate($zipPath);

        // ۴. اجرای migration
        Artisan::call('migrate', ['--force' => true]);

        // ۵. پاک کردن cache
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        // ۶. آپدیت version.json
        file_put_contents(base_path('version.json'), json_encode([
            'version'     => $newVersion,
            'released_at' => now()->toDateString(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // ۷. پاک کردن zip دانلود شده
        @unlink($zipPath);

        Log::info("Update applied: v{$currentVersion} -> v{$newVersion}, backup: {$backupName}");
    }

    // ─────────────────────────────────────────
    // بکاپ کامل (فایل‌ها + دیتابیس)
    // ─────────────────────────────────────────

    private function createFullBackup(string $backupName, string $zipPath): void
    {
        $backupDir = $this->backupsDir() . '/' . $backupName;
        mkdir($backupDir, 0755, true);

        // بکاپ فایل‌هایی که در zip هستن
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === true) {
            $manifestJson = $zip->getFromName('manifest.json');
            $zip->close();

            if ($manifestJson) {
                $manifest = json_decode($manifestJson, true);
                foreach ($manifest['files'] ?? [] as $file) {
                    $src = base_path($file);
                    if (!file_exists($src)) continue;
                    $dest    = $backupDir . '/' . $file;
                    $destDir = dirname($dest);
                    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                    copy($src, $dest);
                }
            }
        }

        // بکاپ version.json فعلی
        if (file_exists(base_path('version.json'))) {
            copy(base_path('version.json'), $backupDir . '/version.json');
        }

        // بکاپ دیتابیس
        $this->backupDatabase($backupDir . '/database.sql');

        // ذخیره metadata بکاپ
        file_put_contents($backupDir . '/backup-info.json', json_encode([
            'version'    => $this->currentVersion(),
            'created_at' => now()->toDateTimeString(),
            'name'       => $backupName,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // ─────────────────────────────────────────
    // بکاپ دیتابیس
    // ─────────────────────────────────────────

    private function backupDatabase(string $outputPath): void
    {
        try {
            $db     = config('database.default');
            $config = config("database.connections.{$db}");

            if ($config['driver'] !== 'mysql') return;

            $host     = $config['host'];
            $port     = $config['port'] ?? 3306;
            $database = $config['database'];
            $username = $config['username'];
            $password = $config['password'];

            // بکاپ با PHP بدون نیاز به mysqldump
            $tables = DB::select('SHOW TABLES');
            $sql    = "-- Harajino Database Backup\n";
            $sql   .= "-- Date: " . now()->toDateTimeString() . "\n\n";
            $sql   .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $table = array_values((array)$tableObj)[0];

                // ساختار جدول
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createSql   = array_values((array)$createTable[0])[1];
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createSql . ";\n\n";

                // داده‌ها
                $rows = DB::table($table)->get();
                if ($rows->isEmpty()) continue;

                $columns = array_keys((array)$rows->first());
                $colList = '`' . implode('`, `', $columns) . '`';

                $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowArr = (array)$row;
                    $vals   = array_map(function ($v) {
                        if ($v === null) return 'NULL';
                        return "'" . addslashes((string)$v) . "'";
                    }, $rowArr);
                    $values[] = '(' . implode(', ', $vals) . ')';
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            file_put_contents($outputPath, $sql);

        } catch (\Exception $e) {
            Log::warning('DB backup failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // بازگردانی دیتابیس
    // ─────────────────────────────────────────

    private function restoreDatabase(string $sqlPath): void
    {
        $sql = file_get_contents($sqlPath);
        DB::unprepared($sql);
    }

    // ─────────────────────────────────────────
    // Extract فایل‌های آپدیت
    // ─────────────────────────────────────────

    private function extractUpdate(string $zipPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('باز کردن zip ناموفق بود.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === 'manifest.json' || str_ends_with($name, '/')) continue;

            $target    = base_path($name);
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            file_put_contents($target, $zip->getFromIndex($i));
        }

        $zip->close();
    }

    // ─────────────────────────────────────────
    // بازگردانی فایل‌ها از بکاپ
    // ─────────────────────────────────────────

    private function restoreFiles(string $backupDir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($backupDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($backupDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);

            // فایل‌های metadata بکاپ رو skip کن
            if (in_array($relativePath, ['database.sql', 'backup-info.json', 'version.json'])) continue;

            $target    = base_path($relativePath);
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            copy($file->getPathname(), $target);
        }
    }

    // ─────────────────────────────────────────
    // لیست بکاپ‌ها
    // ─────────────────────────────────────────

    private function listBackups(): array
    {
        $backups = [];
        $dir     = $this->backupsDir();

        foreach (glob($dir . '/backup-*') as $path) {
            if (!is_dir($path)) continue;
            $infoFile = $path . '/backup-info.json';
            $info     = file_exists($infoFile)
                ? json_decode(file_get_contents($infoFile), true)
                : [];

            $backups[] = [
                'name'       => basename($path),
                'version'    => $info['version'] ?? '?',
                'created_at' => $info['created_at'] ?? '',
                'size'       => $this->dirSize($path),
            ];
        }

        // جدیدترین اول
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $backups;
    }

    private function dirSize(string $dir): string
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            $size += $file->getSize();
        }
        if ($size < 1024) return $size . ' B';
        if ($size < 1048576) return round($size / 1024, 1) . ' KB';
        return round($size / 1048576, 1) . ' MB';
    }

    private function deleteDir(string $dir): void
    {
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
