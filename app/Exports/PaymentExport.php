<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, Responsable, ShouldAutoSize, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function map($profile): array
    {
        return [
            $profile->phone_number,
            $profile->transaction_id,
            $profile->code,
            $profile->amount_paid,
            $profile->created_at,
            $profile->activated_at,
            $profile->status,
            $profile->is_expired,
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Mobile',
            'TransactionID',
            'Code',
            'Amount',
            'Created At',
            'Activated At',
            'Status',
            'Expired?',
        ];
    }
}
