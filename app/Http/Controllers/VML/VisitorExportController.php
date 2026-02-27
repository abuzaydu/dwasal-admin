<?php

namespace App\Http\Controllers\VML;

use App\Exports\VisitorsExport;
use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VisitorExportController extends Controller
{
    public function export(Request $request)
    {
        $type   = $request->get('type',   'total');
        $period = $request->get('period', 'today');
        $format = $request->get('format', 'xlsx');

        $filename = "visitors_{$type}_{$period}_" . now()->format('Ymd');

        if ($format === 'pdf') {
            return $this->exportPdf($type, $period, $filename);
        }

        // Default: Excel
        return Excel::download(new VisitorsExport($type, $period), "{$filename}.xlsx");
    }

    private function exportPdf(string $type, string $period, string $filename)
    {
        [$from, $to] = $this->getDateRange($period);

        $query = Visitor::query();
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $visitors = match($type) {
            'pending'   => $query->whereIn('status', ['Awaiting Host permission', 'Permission Granted'])->get(),
            'checkedin' => $query->where('status', 'Checked In')->get(),
            'checkedout' => $query->where('status', 'Checked Out')->get(), // ✅ new
            default     => $query->get(),
        };

        $pdf = Pdf::loadView('vml.visitors.exports.visitors-pdf', [
            'visitors' => $visitors,
            'type'     => $type,
            'period'   => $period,
            'generatedAt' => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("{$filename}.pdf");
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'weekly'  => [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()],
            'monthly' => [Carbon::now()->startOfMonth(),           Carbon::now()->endOfMonth()],
            'yearly'  => [Carbon::now()->startOfYear(),            Carbon::now()->endOfYear()],
            'total'   => [null, null],
            default   => [Carbon::today()->startOfDay(),           Carbon::today()->endOfDay()],
        };
    }
}
