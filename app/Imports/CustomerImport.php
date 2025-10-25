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
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Shop;
use \Carbon\Carbon;
use Session;

class CustomerImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
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
        $now = Carbon::now();
        $category = null;
        if (!is_null($row['category'])) {
            $category = CustomerCategory::where('shop_id', $shop->id)->where('cat_name', $row['category'])->first();
            if (is_null($category)) {
                $category = new CustomerCategory;
                $category->shop_id = $shop->id;
                $category->cat_name = $row['category'];
                $category->save();
            }
        }
        
        $customer = new Customer();
        $customer->shop_id = $shop->id;
        $customer->name = $row['name'];
        $customer->phone = $row['phone'];
        $customer->email = $row['email'];
        $customer->postal_address = $row['postal_address'];
        $customer->physical_address = $row['physical_address'];
        $customer->street = $row['street'];
        $customer->tin = $row['tin'];
        $customer->vrn = $row['vrn'];
        $customer->time_created = $now;
        if (!is_null($category)) {
            $customer->customer_category_id = $category->id;
        }
        $customer->save();

        return $customer;
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new CustomerImport(),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
