<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>داشبورد فروشنده</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Tahoma, Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card .value { font-size: 32px; font-weight: bold; color: #333; }
        .table-container { background: white; padding: 20px; border-radius: 8px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: right; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>داشبورد فروشنده</h1>
            <p>خوش آمدید، {{ auth()->user()->name }}</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>درآمد کل</h3>
                <div class="value">{{ number_format($stats['total_sales'] ?? 0) }} تومان</div>
            </div>
            <div class="stat-card">
                <h3>مزایده‌های فعال</h3>
                <div class="value">{{ $stats['active_auctions'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <h3>در انتظار تایید</h3>
                <div class="value">{{ $stats['pending_listings'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <h3>تکمیل شده</h3>
                <div class="value">{{ $stats['completed_auctions'] ?? 0 }}</div>
            </div>
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 20px;">مزایده‌های فعال</h2>
            @if(isset($activeListings) && $activeListings->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>قیمت فعلی</th>
                            <th>وضعیت</th>
                            <th>زمان پایان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeListings as $listing)
                            <tr>
                                <td>{{ $listing->title }}</td>
                                <td>{{ number_format($listing->current_price) }} تومان</td>
                                <td>{{ $listing->status }}</td>
                                <td>{{ $listing->ends_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>هیچ مزایده فعالی وجود ندارد</p>
            @endif
            
            <div style="margin-top: 20px;">
                <a href="{{ route('listings.create') }}" class="btn">افزودن مزایده جدید</a>
            </div>
        </div>
    </div>
</body>
</html>
