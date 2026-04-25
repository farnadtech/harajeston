<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { background: #f4f6f9; font-family: Tahoma, Arial, sans-serif; direction: rtl; }
  .wrapper { max-width: 600px; margin: 32px auto; }
  .header { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 16px 16px 0 0; padding: 32px 40px; text-align: center; }
  .header img { height: 40px; margin-bottom: 12px; }
  .header h1 { color: #fff; font-size: 20px; font-weight: bold; letter-spacing: 0.5px; }
  .header p { color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 4px; }
  .body { background: #fff; padding: 36px 40px; }
  .body p { color: #374151; font-size: 15px; line-height: 2; }
  .message-box { background: #f8fafc; border-right: 4px solid #2563eb; border-radius: 8px; padding: 20px 24px; margin: 24px 0; color: #1e293b; font-size: 15px; line-height: 2; }
  .footer { background: #f8fafc; border-radius: 0 0 16px 16px; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb; }
  .footer p { color: #9ca3af; font-size: 12px; line-height: 1.8; }
  .footer a { color: #2563eb; text-decoration: none; }
  .site-name { color: #2563eb; font-weight: bold; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>{{ $siteName }}</h1>
    <p>{{ $subject }}</p>
  </div>
  <div class="body">
    <p>کاربر گرامی،</p>
    <div class="message-box">{!! nl2br(e($body)) !!}</div>
    <p>با تشکر،<br><span class="site-name">تیم {{ $siteName }}</span></p>
  </div>
  <div class="footer">
    <p>
      این ایمیل به صورت خودکار ارسال شده است. لطفاً به این ایمیل پاسخ ندهید.<br>
      <a href="{{ $siteUrl }}">{{ $siteUrl }}</a>
    </p>
  </div>
</div>
</body>
</html>
