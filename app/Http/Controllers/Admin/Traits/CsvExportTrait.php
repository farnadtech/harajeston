<?php

namespace App\Http\Controllers\Admin\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait CsvExportTrait
{
    protected function csvResponse(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $httpHeaders = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Transfer-Encoding' => 'binary',
        ];

        return response()->stream(function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for Excel UTF-8
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 200, $httpHeaders);
    }

    protected function jalali($date): string
    {
        if (!$date) return '';
        try {
            return \Morilog\Jalali\Jalalian::fromCarbon(
                $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date)
            )->format('Y/m/d H:i');
        } catch (\Exception $e) {
            return '';
        }
    }
}
