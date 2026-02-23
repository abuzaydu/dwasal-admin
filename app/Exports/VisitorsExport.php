<?php

namespace App\Exports;

use App\Models\Visitor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected string $type,
        protected string $period
    ) {}

    public function query()
    {
        $query = Visitor::query();

        // Apply period filter
        [$from, $to] = $this->getDateRange();
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Apply type filter
        $query = match($this->type) {
            'pending'   => $query->whereIn('status', ['Awaiting Host permission', 'Permission Granted']),
            'checkedin' => $query->where('status', 'Checked In'),
            'checkedout' => $query->where('status', 'Checked Out'), 
            default     => $query, // total / period = all
        };

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            '#',
            'Visitor Name',
            'Phone',
            'Email',
            'Host',
            'Purpose',
            'Status',
            'Check-in Time',
            'Check-out Time',
            'Date',
        ];
    }

    public function map($visitor): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $visitor->name,
            $visitor->mobile       ?? 'N/A',
            $visitor->email       ?? 'N/A',
            $visitor->user->first_name . ' ' . $visitor->user->last_name ?? 'N/A',
            $visitor->purpose     ?? 'N/A',
            $visitor->status,
            $visitor->time_in  ? Carbon::parse($visitor->time_in)->format('H:i') : 'N/A',
            $visitor->time_out ? Carbon::parse($visitor->time_out)->format('H:i') : 'N/A',
            Carbon::parse($visitor->created_at)->format('d M Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [ // heading row
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1A73E8']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return ucfirst($this->type) . ' Visitors - ' . ucfirst($this->period);
    }

    private function getDateRange(): array
    {
        return match($this->period) {
            'weekly'  => [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()],
            'monthly' => [Carbon::now()->startOfMonth(),           Carbon::now()->endOfMonth()],
            'yearly'  => [Carbon::now()->startOfYear(),            Carbon::now()->endOfYear()],
            'total'   => [null, null],
            default   => [Carbon::today()->startOfDay(),           Carbon::today()->endOfDay()],
        };
    }
}