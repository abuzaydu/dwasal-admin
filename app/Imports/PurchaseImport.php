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
use App\Models\PurchaseTemp;
use App\Models\PurchaseItemTemp;
use \Carbon\Carbon;

class PurchaseImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
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
        $purchasetemp = PurchaseTemp::find($request['purchase_temp_id']);
        if (!is_null($purchasetemp)) {
            $shop = Shop::find($purchasetemp->shop_id);
            if (array_key_exists('product_no', $row) && array_key_exists('barcode', $row) && array_key_exists('name', $row) && array_key_exists('qty', $row) && array_key_exists('unit_cost', $row) && array_key_exists('retail_price', $row) && array_key_exists('wholesale_price', $row)) {
                $product = $shop->products()->where('product_no', $row['product_no'])->where('name', $row['name'])->where('barcode', $row['barcode'])->first();
                if (!is_null($product)) {
                    $unitcost = 0;
                    if (!is_null($row['unit_cost'])) {
                        $unitcost = $row['unit_cost'];
                    }
                    $stockItemTemp = new PurchaseItemTemp;
                    $stockItemTemp->purchase_temp_id = $purchasetemp->id;
                    $stockItemTemp->product_id = $product->id;
                    $stockItemTemp->quantity_in  = $row['qty'];
                    $stockItemTemp->unit_cost = $unitcost;
                    $stockItemTemp->total = $stockItemTemp->quantity_in*$stockItemTemp->unit_cost;
                    if (!is_null($row['retail_price']) && $row['retail_price'] > 0) {
                        $stockItemTemp->retail_price = $row['retail_price'];
                    }else{
                        if (!is_null($product->retail_price)) {
                            $stockItemTemp->retail_price = $product->retail_price;
                        }else{
                            $stockItemTemp->retail_price = 0;
                        }
                    }
                    if (!is_null($row['expire_date'])) {
                        try {
                            $expdate = Carbon::parse($request['expire_date']);
                            $now = Carbon::now();
                            $numd = $expdate->gt($now);
                            if ($numd) {
                                $stockItemTemp->expire_date = $expdate->format('Y-m-d');
                                $stockItemTemp->save();
                            }
                        } catch (\Exception $e) {}
                    }
                    $stockItemTemp->save();
                }else{

                }
            }else{
                return null;
            }
        }
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new PurchaseImport(),
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
