<?php
/**
 * Harajino Installer
 * Upload this file to your server root and open it in browser
 */

define('INSTALLER_VERSION', '1.0.0');
define('MIN_PHP', '8.1.0');
define('REQUIRED_EXTENSIONS', ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'zip', 'curl']);

session_start();

// اگه قبلاً نصب شده، دسترسی ممنوع
if (file_exists(__DIR__ . '/installed.lock') && !isset($_GET['force'])) {
    die('<div style="font-family:Tahoma;text-align:center;padding:60px;color:#dc2626"><h2>⚠️ سیستم قبلاً نصب شده است</h2><p>برای امنیت، فایل install.php را از سرور حذف کنید.</p></div>');
}

$step = (int)($_GET['step'] ?? 1);

// ─── helpers ───────────────────────────────────────────────────────────────

function checkRequirements(): array {
    $errors = [];
    if (version_compare(PHP_VERSION, MIN_PHP, '<')) {
        $errors[] = 'PHP ' . MIN_PHP . ' یا بالاتر نیاز است. نسخه فعلی: ' . PHP_VERSION;
    }
    foreach (REQUIRED_EXTENSIONS as $ext) {
        if (!extension_loaded($ext)) {
            $errors[] = "افزونه PHP مورد نیاز نصب نیست: {$ext}";
        }
    }
    if (!is_writable(__DIR__)) {
        $errors[] = 'پوشه root قابل نوشتن نیست. chmod 755 را اعمال کنید.';
    }
    $storagePath = __DIR__ . '/storage';
    if (is_dir($storagePath) && !is_writable($storagePath)) {
        $errors[] = 'پوشه storage قابل نوشتن نیست. chmod -R 775 storage را اجرا کنید.';
    }
    return $errors;
}

function testDbConnection(string $host, string $port, string $db, string $user, string $pass): bool|string {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function writeEnvFile(array $data): void {
    $envExample = __DIR__ . '/.env.example';
    $envFile    = __DIR__ . '/.env';

    $content = file_exists($envExample) ? file_get_contents($envExample) : '';

    $replacements = [
        'APP_NAME'      => '"' . addslashes($data['site_name']) . '"',
        'APP_URL'       => $data['app_url'],
        'APP_ENV'       => 'production',
        'APP_DEBUG'     => 'false',
        'APP_KEY'       => 'base64:' . base64_encode(random_bytes(32)),
        'DB_HOST'       => $data['db_host'],
        'DB_PORT'       => $data['db_port'],
        'DB_DATABASE'   => $data['db_name'],
        'DB_USERNAME'   => $data['db_user'],
        'DB_PASSWORD'   => $data['db_pass'],
    ];

    foreach ($replacements as $key => $value) {
        if (preg_match("/^{$key}=/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }
    }

    file_put_contents($envFile, $content);
}

function runArtisan(string $command): string {
    $php    = PHP_BINARY ?: 'php';
    $artisan = __DIR__ . '/artisan';
    $output = shell_exec("{$php} {$artisan} {$command} 2>&1");
    return $output ?? '';
}

function createAdminUser(array $data): void {
    $pdo = new PDO(
        "mysql:host={$_SESSION['db_host']};port={$_SESSION['db_port']};dbname={$_SESSION['db_name']};charset=utf8mb4",
        $_SESSION['db_user'],
        $_SESSION['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $hash = password_hash($data['admin_password'], PASSWORD_BCRYPT);
    $now  = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
        VALUES (?, ?, ?, 'admin', ?, ?, ?)
        ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'admin'
    ");
    $stmt->execute([$data['admin_name'], $data['admin_email'], $hash, $now, $now, $now]);
}

// ─── HTML layout ───────────────────────────────────────────────────────────

function pageHeader(string $title, int $currentStep): void { ?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?> — نصب حراجینو</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Tahoma,Arial,sans-serif;background:#f1f3f7;color:#1a1a2e;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);width:100%;max-width:560px;overflow:hidden}
.header{background:linear-gradient(135deg,#135bec,#0e4bc7);padding:28px 32px;color:#fff}
.header h1{font-size:22px;font-weight:700;margin-bottom:4px}
.header p{font-size:13px;opacity:.8}
.steps{display:flex;gap:0;border-bottom:1px solid #e5e7eb}
.step{flex:1;padding:12px 8px;text-align:center;font-size:12px;color:#9ca3af;border-bottom:2px solid transparent}
.step.active{color:#135bec;border-bottom-color:#135bec;font-weight:700}
.step.done{color:#10b981;border-bottom-color:#10b981}
.body{padding:28px 32px}
.form-group{margin-bottom:18px}
label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
input[type=text],input[type=email],input[type=password],input[type=number]{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .2s}
input:focus{outline:none;border-color:#135bec;box-shadow:0 0 0 3px rgba(19,91,236,.1)}
.btn{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#135bec;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .2s}
.btn:hover{background:#0e4bc7}
.btn-outline{background:#fff;color:#374151;border:1px solid #d1d5db}
.btn-outline:hover{background:#f9fafb}
.btn-success{background:#10b981}.btn-success:hover{background:#059669}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:flex-start;gap:10px}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
.alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8}
.check-item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13px}
.check-item:last-child{border:none}
.badge{padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700}
.badge-ok{background:#dcfce7;color:#16a34a}
.badge-fail{background:#fee2e2;color:#dc2626}
.footer{display:flex;justify-content:space-between;align-items:center;margin-top:24px}
.hint{font-size:12px;color:#9ca3af}
.log{background:#1e1e2e;color:#a6e3a1;padding:16px;border-radius:8px;font-size:12px;font-family:monospace;max-height:200px;overflow-y:auto;white-space:pre-wrap;margin-bottom:16px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
h3{font-size:15px;font-weight:700;color:#111827;margin-bottom:16px}
</style>
</head>
<body>
<div class="card">
<div class="header">
    <h1>🔨 نصب حراجینو</h1>
    <p>نسخه <?= INSTALLER_VERSION ?> — راه‌اندازی آسان روی هاست لینوکس</p>
</div>
<div class="steps">
<?php
$steps = ['بررسی سیستم','دیتابیس','تنظیمات','نصب','پایان'];
foreach ($steps as $i => $s) {
    $n = $i + 1;
    $cls = $n < $currentStep ? 'done' : ($n === $currentStep ? 'active' : '');
    echo "<div class='step {$cls}'>{$n}. {$s}</div>";
}
?>
</div>
<div class="body">
<?php } // end pageHeader

function pageFooter(): void { ?>
</div></div></body></html>
<?php } // end pageFooter

// ═══════════════════════════════════════════════════════════════════════════
// STEP 1 — Requirements
// ═══════════════════════════════════════════════════════════════════════════
if ($step === 1) {
    $errors = checkRequirements();
    pageHeader('بررسی سیستم', 1);
    ?>
    <h3>بررسی پیش‌نیازها</h3>

    <div class="check-item">
        <span>نسخه PHP</span>
        <span class="badge <?= version_compare(PHP_VERSION, MIN_PHP, '>=') ? 'badge-ok' : 'badge-fail' ?>">
            <?= PHP_VERSION ?>
        </span>
    </div>
    <?php foreach (REQUIRED_EXTENSIONS as $ext): ?>
    <div class="check-item">
        <span>افزونه <?= $ext ?></span>
        <span class="badge <?= extension_loaded($ext) ? 'badge-ok' : 'badge-fail' ?>">
            <?= extension_loaded($ext) ? '✓ موجود' : '✗ نصب نشده' ?>
        </span>
    </div>
    <?php endforeach; ?>
    <div class="check-item">
        <span>قابلیت نوشتن پوشه</span>
        <span class="badge <?= is_writable(__DIR__) ? 'badge-ok' : 'badge-fail' ?>">
            <?= is_writable(__DIR__) ? '✓ OK' : '✗ chmod 755' ?>
        </span>
    </div>

    <?php if ($errors): ?>
    <div class="alert alert-error" style="margin-top:16px">
        <div>
            <strong>مشکلاتی یافت شد:</strong><br>
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-success" style="margin-top:16px">
        ✓ همه پیش‌نیازها برقرار است. می‌توانید ادامه دهید.
    </div>
    <?php endif; ?>

    <div class="footer">
        <span class="hint">مرحله ۱ از ۵</span>
        <?php if (!$errors): ?>
        <a href="?step=2"><button class="btn">ادامه ←</button></a>
        <?php endif; ?>
    </div>
    <?php
    pageFooter();
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// STEP 2 — Database
// ═══════════════════════════════════════════════════════════════════════════
if ($step === 2) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $host = trim($_POST['db_host'] ?? 'localhost');
        $port = trim($_POST['db_port'] ?? '3306');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? '');
        $pass = $_POST['db_pass'] ?? '';

        if (!$name || !$user) {
            $error = 'نام دیتابیس و نام کاربری الزامی است.';
        } else {
            $result = testDbConnection($host, $port, $name, $user, $pass);
            if ($result === true) {
                $_SESSION['db_host'] = $host;
                $_SESSION['db_port'] = $port;
                $_SESSION['db_name'] = $name;
                $_SESSION['db_user'] = $user;
                $_SESSION['db_pass'] = $pass;
                header('Location: ?step=3');
                exit;
            } else {
                $error = 'خطا در اتصال به دیتابیس: ' . $result;
            }
        }
    }

    pageHeader('تنظیمات دیتابیس', 2);
    ?>
    <h3>اطلاعات دیتابیس MySQL</h3>
    <div class="alert alert-info">
        ابتدا یک دیتابیس MySQL خالی در هاست خود بسازید، سپس اطلاعات آن را وارد کنید.
    </div>
    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <div class="form-group">
                <label>هاست دیتابیس</label>
                <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
            </div>
            <div class="form-group">
                <label>پورت</label>
                <input type="number" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>نام دیتابیس</label>
            <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" placeholder="harajino_db" required>
        </div>
        <div class="form-group">
            <label>نام کاربری دیتابیس</label>
            <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" placeholder="harajino_user" required>
        </div>
        <div class="form-group">
            <label>رمز عبور دیتابیس</label>
            <input type="password" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
        </div>
        <div class="footer">
            <a href="?step=1"><button type="button" class="btn btn-outline">← برگشت</button></a>
            <button type="submit" class="btn">تست و ادامه ←</button>
        </div>
    </form>
    <?php
    pageFooter();
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// STEP 3 — Admin Account Only
// ═══════════════════════════════════════════════════════════════════════════
if ($step === 3) {
    if (!isset($_SESSION['db_host'])) { header('Location: ?step=2'); exit; }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $adminName   = trim($_POST['admin_name'] ?? '');
        $adminEmail  = trim($_POST['admin_email'] ?? '');
        $adminPass   = $_POST['admin_password'] ?? '';
        $adminPass2  = $_POST['admin_password2'] ?? '';
        $appUrl      = rtrim(trim($_POST['app_url'] ?? ''), '/');

        if (!$adminName || !$adminEmail || !$adminPass || !$appUrl) {
            $error = 'همه فیلدها الزامی است.';
        } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'ایمیل معتبر نیست.';
        } elseif ($adminPass !== $adminPass2) {
            $error = 'رمز عبور و تکرار آن یکسان نیستند.';
        } elseif (strlen($adminPass) < 8) {
            $error = 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
        } else {
            $_SESSION['site_name']       = 'حراجینو';
            $_SESSION['app_url']         = $appUrl;
            $_SESSION['admin_name']      = $adminName;
            $_SESSION['admin_email']     = $adminEmail;
            $_SESSION['admin_password']  = $adminPass;
            header('Location: ?step=4');
            exit;
        }
    }

    // تشخیص خودکار URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir      = dirname($_SERVER['SCRIPT_NAME']);
    $guessUrl = $protocol . '://' . $host . ($dir === '/' ? '' : $dir);

    pageHeader('حساب ادمین', 3);
    ?>
    <h3>ساخت حساب مدیر سیستم</h3>
    <div class="alert alert-info">
        این تنها حساب ادمین سایت خواهد بود. بعد از نصب می‌توانید نام سایت و سایر تنظیمات را از پنل ادمین تغییر دهید.
    </div>
    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>آدرس سایت (URL)</label>
            <input type="text" name="app_url" value="<?= htmlspecialchars($_POST['app_url'] ?? $guessUrl) ?>" required>
            <small style="color:#6b7280;font-size:12px">مثال: https://example.com — بدون / در انتها</small>
        </div>
        <div class="form-group">
            <label>نام ادمین</label>
            <input type="text" name="admin_name" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" placeholder="مدیر سیستم" required>
        </div>
        <div class="form-group">
            <label>ایمیل ادمین</label>
            <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" placeholder="admin@example.com" required>
        </div>
        <div class="row">
            <div class="form-group">
                <label>رمز عبور (حداقل ۸ کاراکتر)</label>
                <input type="password" name="admin_password" required minlength="8">
            </div>
            <div class="form-group">
                <label>تکرار رمز عبور</label>
                <input type="password" name="admin_password2" required>
            </div>
        </div>
        <div class="footer">
            <a href="?step=2"><button type="button" class="btn btn-outline">← برگشت</button></a>
            <button type="submit" class="btn">شروع نصب ←</button>
        </div>
    </form>
    <?php
    pageFooter();
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// STEP 4 — Install
// ═══════════════════════════════════════════════════════════════════════════
if ($step === 4) {
    if (!isset($_SESSION['db_host'], $_SESSION['admin_email'])) { header('Location: ?step=2'); exit; }

    pageHeader('در حال نصب...', 4);
    ?>
    <h3>نصب حراجینو</h3>
    <div class="alert alert-info">لطفاً صبر کنید. این فرآیند ممکن است چند دقیقه طول بکشد...</div>
    <div class="log" id="log">شروع نصب...\n</div>

    <script>
    const log = document.getElementById('log');
    function addLog(msg) { log.textContent += msg + '\n'; log.scrollTop = log.scrollHeight; }

    async function install() {
        const steps = [
            { url: '?step=4&action=env',       label: 'ساخت فایل .env' },
            { url: '?step=4&action=key',        label: 'تولید APP_KEY' },
            { url: '?step=4&action=migrate',    label: 'اجرای migration دیتابیس' },
            { url: '?step=4&action=seed',       label: 'بارگذاری داده‌های اولیه' },
            { url: '?step=4&action=admin',      label: 'ساخت حساب ادمین' },
            { url: '?step=4&action=storage',    label: 'لینک storage' },
            { url: '?step=4&action=cache',      label: 'پاک کردن cache' },
            { url: '?step=4&action=finish',     label: 'پایان نصب' },
        ];

        for (const s of steps) {
            addLog('⏳ ' + s.label + '...');
            try {
                const res = await fetch(s.url);
                const data = await res.json();
                if (data.success) {
                    addLog('✓ ' + s.label + (data.msg ? ': ' + data.msg : ''));
                } else {
                    addLog('✗ خطا در ' + s.label + ': ' + (data.error || 'نامشخص'));
                    addLog('\n❌ نصب متوقف شد.');
                    return;
                }
            } catch(e) {
                addLog('✗ خطای شبکه: ' + e.message);
                return;
            }
        }

        addLog('\n✅ نصب با موفقیت انجام شد!');
        setTimeout(() => { window.location.href = '?step=5'; }, 1500);
    }

    window.onload = install;
    </script>
    <?php
    pageFooter();
    exit;
}

// ─── AJAX actions for step 4 ───────────────────────────────────────────────
if ($step === 4 && isset($_GET['action'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['db_host'])) {
        echo json_encode(['success' => false, 'error' => 'Session expired']);
        exit;
    }

    $action = $_GET['action'];

    try {
        switch ($action) {

            case 'env':
                // اول zip اسکریپت رو extract کن
                $zipFiles = glob(__DIR__ . '/harajino-v*.zip');
                if (!empty($zipFiles)) {
                    $scriptZip = $zipFiles[0];
                    $zip = new ZipArchive();
                    if ($zip->open($scriptZip) === true) {
                        $zip->extractTo(__DIR__);
                        $zip->close();
                        @unlink($scriptZip); // zip رو بعد از extract پاک کن
                    }
                }

                writeEnvFile([
                    'site_name' => $_SESSION['site_name'],
                    'app_url'   => $_SESSION['app_url'],
                    'db_host'   => $_SESSION['db_host'],
                    'db_port'   => $_SESSION['db_port'],
                    'db_name'   => $_SESSION['db_name'],
                    'db_user'   => $_SESSION['db_user'],
                    'db_pass'   => $_SESSION['db_pass'],
                ]);
                echo json_encode(['success' => true, 'msg' => 'فایل‌های اسکریپت extract شد']);
                break;

            case 'key':
                $out = runArtisan('key:generate --force');
                echo json_encode(['success' => true, 'msg' => trim($out)]);
                break;

            case 'migrate':
                $out = runArtisan('migrate --force --no-interaction');
                $success = !str_contains(strtolower($out), 'error') && !str_contains(strtolower($out), 'exception');
                echo json_encode(['success' => $success, 'msg' => substr(trim($out), 0, 200), 'error' => $success ? null : $out]);
                break;

            case 'seed':
                $out = runArtisan('db:seed --class=InstallSeeder --force --no-interaction');
                echo json_encode(['success' => true, 'msg' => 'تنظیمات و دسته‌بندی‌های پیش‌فرض بارگذاری شد']);
                break;

            case 'admin':
                createAdminUser([
                    'admin_name'     => $_SESSION['admin_name'],
                    'admin_email'    => $_SESSION['admin_email'],
                    'admin_password' => $_SESSION['admin_password'],
                ]);
                echo json_encode(['success' => true, 'msg' => $_SESSION['admin_email']]);
                break;

            case 'storage':
                $out = runArtisan('storage:link');
                echo json_encode(['success' => true, 'msg' => trim($out)]);
                break;

            case 'cache':
                runArtisan('config:clear');
                runArtisan('view:clear');
                runArtisan('cache:clear');
                echo json_encode(['success' => true]);
                break;

            case 'finish':
                // ذخیره نام سایت در site_settings
                try {
                    $pdo = new PDO(
                        "mysql:host={$_SESSION['db_host']};port={$_SESSION['db_port']};dbname={$_SESSION['db_name']};charset=utf8mb4",
                        $_SESSION['db_user'], $_SESSION['db_pass'],
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );
                    $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`, `type`) VALUES ('site_name', ?, 'string') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                    $stmt->execute([$_SESSION['site_name']]);
                    $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`, `type`) VALUES ('site_url', ?, 'string') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                    $stmt->execute([$_SESSION['app_url']]);
                } catch (Exception $e) { /* ignore */ }

                // ساخت فایل installed.lock
                file_put_contents(__DIR__ . '/installed.lock', date('Y-m-d H:i:s'));
                echo json_encode(['success' => true]);
                break;

            default:
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// STEP 5 — Done
// ═══════════════════════════════════════════════════════════════════════════
if ($step === 5) {
    $adminUrl = ($_SESSION['app_url'] ?? '') . '/admin/dashboard';
    $siteUrl  = $_SESSION['app_url'] ?? '';

    pageHeader('نصب کامل شد', 5);
    ?>
    <div style="text-align:center;padding:16px 0">
        <div style="font-size:56px;margin-bottom:12px">🎉</div>
        <h3 style="font-size:20px;margin-bottom:8px">حراجینو با موفقیت نصب شد!</h3>
        <p style="color:#6b7280;font-size:14px;margin-bottom:24px">سایت شما آماده استفاده است.</p>
    </div>

    <div class="alert alert-success">
        <div>
            <strong>اطلاعات ورود ادمین:</strong><br>
            ایمیل: <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?><br>
            آدرس پنل: <a href="<?= htmlspecialchars($adminUrl) ?>" target="_blank"><?= htmlspecialchars($adminUrl) ?></a>
        </div>
    </div>

    <div class="alert alert-error">
        <div>
            <strong>⚠️ مهم — امنیت:</strong><br>
            فایل <code>install.php</code> را از سرور حذف کنید تا امنیت سایت حفظ شود.
        </div>
    </div>

    <div class="footer" style="justify-content:center;gap:12px">
        <a href="<?= htmlspecialchars($siteUrl) ?>" target="_blank">
            <button class="btn btn-outline">مشاهده سایت</button>
        </a>
        <a href="<?= htmlspecialchars($adminUrl) ?>" target="_blank">
            <button class="btn btn-success">ورود به پنل ادمین ←</button>
        </a>
    </div>
    <?php
    // پاک کردن session
    session_destroy();
    pageFooter();
    exit;
}

// redirect to step 1 if invalid step
header('Location: ?step=1');
exit;
