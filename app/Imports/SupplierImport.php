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
use App\Models\Supplier;
use App\Models\Shop;
use \Carbon\Carbon;
use Session;

class SupplierImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
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
        $shop = Shop::find(Session::get('shop_id'));
        $supp_id = Supplier::where('shop_id' , $shop->id)->get()->max('supp_id');
        return new Supplier([
            'shop_id' => $shop->id,
            'name' => $row['name'],
            'supp_id' =>  !is_null($supp_id) & ($supp_id > 1) ? $supp_id+1 : 1 , 
            'contact_no' => $row['contact_no'],
            'email' => $row['email'],
            'address' => $row['address'],
            'supplier_for' => 'Stock',
            'time_created' => Carbon::now()
        ]);
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new SupplierImport(),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

}
