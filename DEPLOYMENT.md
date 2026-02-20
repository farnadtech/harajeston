# راهنمای استقرار (Deployment Guide)

این راهنما مراحل استقرار پلتفرم مزایده ایرانی در محیط Production را شرح می‌دهد.

## پیش‌نیازها

### نیازمندی‌های سرور

- **سیستم عامل**: Ubuntu 20.04 LTS یا بالاتر (توصیه می‌شود)
- **وب سرور**: Nginx یا Apache
- **PHP**: نسخه 8.1 یا بالاتر
- **دیتابیس**: MySQL 8.0 یا MariaDB 10.5+
- **Redis**: برای کش و صف (توصیه می‌شود)
- **Supervisor**: برای مدیریت Queue Workers
- **SSL Certificate**: برای HTTPS (Let's Encrypt رایگان است)

### افزونه‌های PHP مورد نیاز

```bash
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml \
  php8.1-bcmath php8.1-curl php8.1-gd php8.1-zip php8.1-redis
```

## مراحل استقرار

### 1. آماده‌سازی سرور

#### نصب Nginx

```bash
sudo apt update
sudo apt install nginx
```

#### نصب MySQL

```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

#### ایجاد دیتابیس

```bash
sudo mysql -u root -p

CREATE DATABASE haraj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'haraj_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON haraj.* TO 'haraj_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### نصب Redis

```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### نصب Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### نصب Node.js و NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs
```

### 2. آپلود کد برنامه

```bash
# ایجاد دایرکتوری پروژه
sudo mkdir -p /var/www/haraj
sudo chown -R $USER:$USER /var/www/haraj

# کلون کردن یا آپلود کد
cd /var/www/haraj
git clone <repository-url> .

# یا با rsync
rsync -avz --exclude 'node_modules' --exclude 'vendor' \
  /local/path/ user@server:/var/www/haraj/
```

### 3. نصب وابستگی‌ها

```bash
cd /var/www/haraj

# نصب وابستگی‌های PHP
composer install --optimize-autoloader --no-dev

# نصب وابستگی‌های JavaScript
npm ci
npm run build
```

### 4. تنظیمات محیطی

```bash
# کپی فایل .env
cp .env.example .env

# ویرایش فایل .env
nano .env
```

تنظیمات مهم در `.env`:

```env
APP_NAME="پلتفرم مزایده ایرانی"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haraj
DB_USERNAME=haraj_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# تنظیمات ایمیل (مثال با Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. تولید کلید و لینک Storage

```bash
# تولید کلید برنامه
php artisan key:generate

# ایجاد لینک symbolic برای storage
php artisan storage:link
```

### 6. اجرای Migration

```bash
# اجرای migration ها
php artisan migrate --force

# اجرای seeder (اختیاری - فقط برای داده‌های نمایشی)
# php artisan db:seed --class=DemoDataSeeder
```

### 7. بهینه‌سازی

```bash
# کش کردن تنظیمات
php artisan config:cache

# کش کردن route ها
php artisan route:cache

# کش کردن view ها
php artisan view:cache

# بهینه‌سازی autoloader
composer dump-autoload --optimize
```

### 8. تنظیم دسترسی‌ها

```bash
# تنظیم مالکیت
sudo chown -R www-data:www-data /var/www/haraj

# تنظیم دسترسی‌ها
sudo chmod -R 755 /var/www/haraj
sudo chmod -R 775 /var/www/haraj/storage
sudo chmod -R 775 /var/www/haraj/bootstrap/cache
```

### 9. تنظیم Nginx

ایجاد فایل تنظیمات Nginx:

```bash
sudo nano /etc/nginx/sites-available/haraj
```

محتوای فایل:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/haraj/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    # Max upload size
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

فعال‌سازی سایت:

```bash
sudo ln -s /etc/nginx/sites-available/haraj /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 10. نصب SSL Certificate (Let's Encrypt)

```bash
# نصب Certbot
sudo apt install certbot python3-certbot-nginx

# دریافت certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# تست تمدید خودکار
sudo certbot renew --dry-run
```

### 11. تنظیم Cron Job

```bash
sudo crontab -e -u www-data
```

اضافه کردن این خط:

```cron
* * * * * cd /var/www/haraj && php artisan schedule:run >> /dev/null 2>&1
```

### 12. تنظیم Queue Worker با Supervisor

ایجاد فایل تنظیمات:

```bash
sudo nano /etc/supervisor/conf.d/haraj-worker.conf
```

محتوای فایل:

```ini
[program:haraj-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/haraj/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/haraj/storage/logs/worker.log
stopwaitsecs=3600
```

راه‌اندازی Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start haraj-worker:*
```

## بررسی وضعیت

### بررسی Queue Worker

```bash
sudo supervisorctl status haraj-worker:*
```

### بررسی لاگ‌ها

```bash
# لاگ Laravel
tail -f /var/www/haraj/storage/logs/laravel.log

# لاگ Nginx
tail -f /var/log/nginx/error.log

# لاگ Worker
tail -f /var/www/haraj/storage/logs/worker.log
```

### بررسی Cron Job

```bash
# مشاهده لاگ cron
grep CRON /var/log/syslog
```

## به‌روزرسانی برنامه

```bash
cd /var/www/haraj

# دریافت آخرین تغییرات
git pull origin main

# نصب وابستگی‌های جدید
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# اجرای migration های جدید
php artisan migrate --force

# پاک کردن کش‌ها
php artisan config:clear
php artisan route:clear
php artisan view:clear

# کش کردن مجدد
php artisan config:cache
php artisan route:cache
php artisan view:cache

# راه‌اندازی مجدد worker ها
sudo supervisorctl restart haraj-worker:*
```

## نکات امنیتی

### 1. فایروال

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 2. محدود کردن دسترسی SSH

```bash
sudo nano /etc/ssh/sshd_config
```

تغییرات توصیه شده:
```
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
```

### 3. نصب Fail2Ban

```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 4. بکاپ خودکار دیتابیس

ایجاد اسکریپت بکاپ:

```bash
sudo nano /usr/local/bin/backup-haraj-db.sh
```

محتوای اسکریپت:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/haraj"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

mysqldump -u haraj_user -p'your_password' haraj | gzip > $BACKUP_DIR/haraj_$DATE.sql.gz

# حذف بکاپ‌های قدیمی‌تر از 30 روز
find $BACKUP_DIR -name "haraj_*.sql.gz" -mtime +30 -delete
```

اضافه کردن به crontab:

```bash
sudo chmod +x /usr/local/bin/backup-haraj-db.sh
sudo crontab -e
```

```cron
0 2 * * * /usr/local/bin/backup-haraj-db.sh
```

## مانیتورینگ

### نصب Laravel Telescope (محیط staging)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### نصب Laravel Horizon (برای مانیتور Queue)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

## عیب‌یابی

### خطای 500

```bash
# بررسی لاگ
tail -f storage/logs/laravel.log

# بررسی دسترسی‌ها
ls -la storage/
ls -la bootstrap/cache/
```

### مشکل Queue

```bash
# راه‌اندازی مجدد worker
sudo supervisorctl restart haraj-worker:*

# بررسی وضعیت Redis
redis-cli ping
```

### مشکل Performance

```bash
# پاک کردن کش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# بهینه‌سازی مجدد
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## پشتیبانی

برای مشکلات و سوالات، لطفاً یک Issue در مخزن GitHub ایجاد کنید.

---

**نکته**: این راهنما برای استقرار در سرور Ubuntu نوشته شده است. برای سیستم‌عامل‌های دیگر، ممکن است نیاز به تغییراتی باشد.
