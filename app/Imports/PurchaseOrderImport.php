<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Shop;
use App\Models\POrderTemp;
use App\Models\PurchaseOrderTemp;
use Log;

class PurchaseOrderImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
{
    use Importable, RegistersEventListeners;


    public static function beforeImport(BeforeImport $event)
    {
        $worksheet = $event->reader->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10

        if ($highestRow < 2) {
            $error = \Illuminate\Validation\ValidationException::withMessages([]);
            $failure = new Failure(1, 'rows', [0 => 'Now enough rows!']);
            $failures = [0 => $failure];
            throw new ValidationException($error, $failures);
        }
    }

    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $request = request()->all();
        $ordertemp = POrderTemp::find($request['order_temp_id']);
        if (!is_null($ordertemp)) {
            $shop = Shop::find($ordertemp->shop_id);
            if (array_key_exists('product_no', $row) && array_key_exists('barcode', $row) && array_key_exists('name', $row) && array_key_exists('qty', $row) && array_key_exists('unit_cost', $row) && array_key_exists('retail_price', $row) && array_key_exists('wholesale_price', $row)) {
                $product = $shop->products()->where('product_no', $row['product_no'])->where('name', $row['name'])->where('barcode', $row['barcode'])->first();
                if (!is_null($product)) {
                    $unitcost = 0;
                    if (!is_null($row['unit_cost'])) {
                        $unitcost = $row['unit_cost'];
                    }
                    $orderItemTemp = new PurchaseOrderTemp;
                    $orderItemTemp->p_order_temp_id = $ordertemp->id;
                    $orderItemTemp->product_id = $product->id;
                    $orderItemTemp->qty  = $row['qty'];
                    $orderItemTemp->unit_cost = $unitcost;
                    $orderItemTemp->save();
                }else{
                    Log::info('Product Nowt found');
                }
            }else{
                return null;
            }
        }else{
            Log::info('PO Tem not found');
        }
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new PurchaseOrderImport(),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'qty' => 'required|string',
        ];
    }
}
