<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;

class CreateSiteUserCommand extends Command
{
    protected $signature = 'site:create-user';
    protected $description = 'ایجاد کاربر سایت برای دریافت کمیسیون‌ها';

    public function handle()
    {
        $this->info('در حال ایجاد کاربر سایت...');

        // بررسی وجود کاربر
        $existingUser = User::find(1);
        if ($existingUser) {
            $this->warn('کاربر سایت از قبل وجود دارد!');
            $this->info("نام: {$existingUser->name}");
            $this->info("ایمیل: {$existingUser->email}");
            
            if ($this->confirm('آیا می‌خواهید کاربر موجود را حذف و دوباره ایجاد کنید؟', false)) {
                $existingUser->delete();
            } else {
                return 0;
            }
        }

        // ایجاد کاربر جدید
        $user = User::create([
            'id' => 1,
            'name' => 'سایت حراج',
            'email' => 'site@persianauction.com',
            'password' => bcrypt('SitePassword123!@#'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // ایجاد کیف پول
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'frozen' => 0,
        ]);

        $this->info('✅ کاربر سایت با موفقیت ایجاد شد!');
        $this->info("ID: {$user->id}");
        $this->info("نام: {$user->name}");
        $this->info("ایمیل: {$user->email}");
        $this->info("رمز عبور: SitePassword123!@#");
        $this->warn('⚠️  لطفاً رمز عبور را تغییر دهید!');

        return 0;
    }
}
