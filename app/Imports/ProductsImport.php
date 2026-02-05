<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use \Carbon\Carbon;
use File;
use Session;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;
use App\Jobs\StockUpdaterJob;
use Log;

class ProductsImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
{
     use Importable, RegistersEventListeners;

    private $rows = 0;
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
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        if (array_key_exists('name', $row) && array_key_exists('basic_uom', $row) && array_key_exists('in_stock', $row) && array_key_exists('retail_price', $row) && array_key_exists('unit_cost', $row)) {
            
            if (is_null($row['name']) || is_null($row['basic_uom'])) {
                return null;
            }else{
                $slug = $row['name'];
                if (!is_null($row['brand'])) {
                    $slug .= ' - '.$row['brand'];
                }
                if (!is_null($row['model'])) {
                    $slug .= ' - '.$row['model'];
                }
                if (!is_null($row['type'])) {
                    $slug .= ' - '.$row['type'];
                }
                if (!is_null($row['color'])) {
                    $slug .= ' - '.$row['color'];
                }
                if (!is_null($row['size'])) {
                   $slug .= ' - '.$row['size'];
                }
                if (!is_null($row['thick'])) {
                    $slug .= ' - '.$row['thick'];
                }
                if (!is_null($row['length'])) {
                    $slug .= ' - '.$row['length'];
                }
                if (!is_null($row['width'])) {
                    $slug .= ' - '.$row['width'];
                }
                if (!is_null($row['height'])) {
                    $slug .= ' - '.$row['height'];
                }
                if (!is_null($row['volume'])) {
                    $slug .= ' - '.$row['volume'];
                }
                if (!is_null($row['weight'])) {
                    $slug .= ' - '.$row['weight'];
                }

                $product = Product::where('slug', $slug)->where('basic_uom', $row['basic_uom'])->where('shop_id', $shop->id)->first();

                $unit_cost = (float)$row['unit_cost'];
                $retail_price = (float)$row['retail_price'];
                $wholesaleprice = (float)$row['wholesale_price'];
                $expire_date = $row['expire_date'];
                    
                $quantity_in = 0;
                if (!is_null($row['in_stock'])) {
                    $quantity_in = $row['in_stock'];
                }

                if (is_null($product)) {
                    $product = new Product();
                    $product->shop_id = $shop->id;
                    $product->name = $row['name'];
                    $product->basic_uom = $row['basic_uom'];
                    $product->slug = $slug;
                    $product->save();

                    if(!is_null($row['brand'])){
                        $brand = Brand::where('name', $row['brand'])->where('shop_id', $shop->id)->first();
                        if (is_null($brand)) {
                            $brand = new Brand();
                            $brand->shop_id = $shop->id;
                            $brand->name = $row['brand'];
                            $brand->save();
                        }
                    }

                    if (!is_null($row['category'])) {
                        $category = Category::where('name', $row['category'])->where('shop_id', $shop->id)->first();
                        if (is_null($category)) {
                            $category = new Category();
                            $category->shop_id = $shop->id;
                            $category->name = $row['category'];
                            $category->save();
                        }

                        if (!is_null($row['sub_category'])) {
                            $subcategory = Category::where('name', $row['sub_category'])->where('shop_id', $shop->id)->where('parent_id', $category->id)->first();
                            if (is_null($subcategory)) {
                                $subcategory = new Category();
                                $subcategory->parent_id = $category->id;
                                $subcategory->shop_id = $shop->id;
                                $subcategory->name = $row['sub_category'];
                                $subcategory->save();


                                $subcategory->products()->attach($product);
                            }
                        }else{
                            $category->products()->attach($product);
                        }
                    }
                    
                    if ($quantity_in > 0) {
                        $stock = new Stock();
                        $stock->shop_id = $shop->id;
                        $stock->product_id = $product->id;
                        $stock->quantity_in = $quantity_in;
                        $stock->unit_cost = $unit_cost;
                        $stock->source = 'Circle Counting';
                        $stock->expire_date = $expire_date;
                        $stock->stock_date = $now;
                        $stock->save();

                        dispatch(new StockUpdaterJob($shop, $product->id));
                    }

                    $product->location = $row['location'];
                    $product->product_code = $row['product_code'];
                    $product->barcode = $row['barcode'];
                    $product->unit_cost = $unit_cost;
                    $product->retail_price = $retail_price;
                    $product->wholesale_price = $wholesaleprice;
                    $product->time_created = $now;
                    $product->brand = $row['brand'];
                    $product->model = $row['model'];
                    $product->type = $row['type'];
                    $product->size = $row['size'];
                    $product->color = $row['color'];
                    $product->length = $row['length'];
                    $product->width = $row['width'];
                    $product->thick = $row['thick'];
                    $product->height = $row['height'];
                    $product->volume = $row['volume'];
                    $product->weight = $row['weight'];
                    $product->save();

                    $prod_unit = new ProductUnit();
                    $prod_unit->product_id = $product->id;
                    $prod_unit->unit_name = $product->basic_uom;
                    $prod_unit->is_basic = true;
                    $prod_unit->qty_equal_to_basic = 1;
                    $prod_unit->unit_price = $retail_price;
                    $prod_unit->save();

                    ++$this->rows;
                    // Log::info($this->rows);
                    return $product;
                }else{
                    if(!is_null($row['brand'])){
                        $brand = Brand::where('name', $row['brand'])->where('shop_id', $shop->id)->first();
                        if (is_null($brand)) {
                            $brand = new Brand();
                            $brand->shop_id = $shop->id;
                            $brand->name = $row['brand'];
                            $brand->save();
                        }
                    }

                    if (!is_null($row['category'])) {
                        $category = Category::where('name', $row['category'])->where('shop_id', $shop->id)->first();
                        if (is_null($category)) {
                            $category = new Category();
                            $category->shop_id = $shop->id;
                            $category->name = $row['category'];
                            $category->save();
                        }

                        if (!is_null($row['sub_category'])) {
                            $subcategory = Category::where('name', $row['sub_category'])->where('shop_id', $shop->id)->where('parent_id', $category->id)->first();
                            if (is_null($subcategory)) {
                                $subcategory = new Category();
                                $subcategory->parent_id = $category->id;
                                $subcategory->shop_id = $shop->id;
                                $subcategory->name = $row['sub_category'];
                                $subcategory->save();


                                $subcategory->products()->attach($product);
                            }
                        }else{
                            $category->products()->attach($product);
                        }
                    }
                    
                    if ($quantity_in > 0) {                        
                        $stock = new Stock();
                        $stock->shop_id = $shop->id;
                        $stock->product_id = $product->id;
                        $stock->quantity_in = $quantity_in;
                        $stock->unit_cost = $unit_cost;
                        $stock->source = 'Circle Counting';
                        $stock->expire_date = $expire_date;
                        $stock->stock_date = $now;
                        $stock->save();

                        dispatch(new StockUpdaterJob($shop, $product->id));
                    }


                    $product->location = $row['location'];
                    $product->product_code = $row['product_code'];
                    $product->barcode = $row['barcode'];
                    $product->unit_cost = $unit_cost;
                    $product->retail_price = $retail_price;
                    $product->wholesale_price = $wholesaleprice;
                    $product->time_created = $now;
                    $product->brand = $row['brand'];
                    $product->model = $row['model'];
                    $product->type = $row['type'];
                    $product->size = $row['size'];
                    $product->color = $row['color'];
                    $product->length = $row['length'];
                    $product->width = $row['width'];
                    $product->thick = $row['thick'];
                    $product->height = $row['height'];
                    $product->volume = $row['volume'];
                    $product->weight = $row['weight'];
                    $product->save();

                    $prod_unit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->first();
                    if (is_null($prod_unit)) {       
                        $prod_unit = new ProductUnit();
                        $prod_unit->product_id = $product->id;
                        $prod_unit->unit_name = $product->basic_uom;
                        $prod_unit->is_basic = true;
                        $prod_unit->qty_equal_to_basic = 1;
                        $prod_unit->unit_price = $retail_price;
                        $prod_unit->save();
                    }else{
                        $prod_unit->unit_price = $retail_price;
                        $prod_unit->save();

                        $product->retail_price = $retail_price;
                        $product->save();
                    }

                    ++$this->rows;
                    return $product;
                }
            }
        } else{
             return null;
        } 
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new ProductsImport(),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'basic_uom' => 'required|string',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
