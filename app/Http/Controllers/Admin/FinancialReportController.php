<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function __construct(
        protected FinancialReportService $financialReportService
    ) {}

    /**
     * نمایش صفحه اصلی گزارشات مالی
     */
    public function index(Request $request)
    {
        // دریافت بازه زمانی از request یا استفاده از ماه جاری
        if ($request->input('start_date')) {
            // تبدیل تاریخ شمسی به میلادی
            try {
                $jalaliStart = $request->input('start_date');
                $startDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d H:i', $jalaliStart)->toCarbon()->startOfDay();
            } catch (\Exception $e) {
                $startDate = Carbon::now()->startOfMonth();
            }
        } else {
            $startDate = Carbon::now()->startOfMonth();
        }
            
        if ($request->input('end_date')) {
            // تبدیل تاریخ شمسی به میلادی
            try {
                $jalaliEnd = $request->input('end_date');
                $endDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d H:i', $jalaliEnd)->toCarbon()->endOfDay();
            } catch (\Exception $e) {
                $endDate = Carbon::now()->endOfMonth();
            }
        } else {
            $endDate = Carbon::now()->endOfMonth();
        }

        // دریافت داده‌ها
        $summary = $this->financialReportService->getSiteRevenueSummary($startDate, $endDate);
        $dailyRevenue = $this->financialReportService->getDailyRevenue($startDate, $endDate);
        $monthlyRevenue = $this->financialReportService->getMonthlyRevenue(Carbon::now()->year);
        $siteWallet = $this->financialReportService->getSiteWalletBalance();
        $platformStats = $this->financialReportService->getPlatformStats();
        $topSellers = $this->financialReportService->getTopSellers($startDate, $endDate, 5);
        $topBuyers = $this->financialReportService->getTopBuyers($startDate, $endDate, 5);
        $categoryStats = $this->financialReportService->getCategoryStats($startDate, $endDate);

        return view('admin.financial-reports.index', compact(
            'summary',
            'dailyRevenue',
            'monthlyRevenue',
            'siteWallet',
            'platformStats',
            'topSellers',
            'topBuyers',
            'categoryStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * نمایش جزئیات کمیسیون‌ها
     */
    public function commissions(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $commissions = $this->financialReportService->getCommissionDetails($startDate, $endDate);

        return view('admin.financial-reports.commissions', compact(
            'commissions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export گزارش به CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $csv = $this->financialReportService->exportToCSV($startDate, $endDate);

        $filename = sprintf(
            'financial-report-%s-to-%s.csv',
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Content-Transfer-Encoding', 'binary');
    }

    /**
     * دریافت داده‌های نمودار (AJAX)
     */
    public function chartData(Request $request)
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        if ($type === 'daily') {
            $data = $this->financialReportService->getDailyRevenue($startDate, $endDate);
        } else {
            $data = $this->financialReportService->getMonthlyRevenue($startDate->year);
        }

        return response()->json($data);
    }
}
