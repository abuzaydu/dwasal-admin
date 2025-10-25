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
use Log;

class ProductsImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
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
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company = Company::find(Session::get('company_id'));
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        if (array_key_exists('name', $row) && array_key_exists('basic_unit', $row) && array_key_exists('in_stock', $row) && array_key_exists('retail_retail_price', $row) && array_key_exists('unit_cost', $row)) {
                        
            if (is_null($row['name']) || is_null($row['basic_unit'])) {
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

                $product = Product::where('slug', $slug)->where('basic_unit', $row['basic_unit'])->where('company_id', $company->id)->first();

                $unit_cost = (float)$row['unit_cost'];
                $retail_price = (float)$row['retail_retail_price'];
                $wholesaleprice = (float)$row['wholesale_retail_price'];
                $expire_date = $row['expire_date'];
                    
                $quantity_in = 0;
                if (!is_null($row['in_stock'])) {
                    $quantity_in = $row['in_stock'];
                }

                if (is_null($product)) {
                    $product = new Product();
                    $product->company_id = $company->id;
                    $product->name = $row['name'];
                    $product->basic_unit = $row['basic_unit'];
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
                        $stock = Stock::create([
                            'shop_id' => $shop->id,
                            'product_id' => $product->id,
                            'quantity_in' => $quantity_in,
                            'unit_cost' => $unit_cost,
                            'source' => 'Circle Counting',
                            'expire_dates' => $expire_date,
                            'time_created' => $now
                        ]);

                        $shop_product = $shop->products()->where('product_id', $product->id)->first();
                        if (!is_null($shop_product)) {

                            $stock_in = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_in');
                            $sold = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                            
                            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
                                         
                            $shop_product->pivot->in_stock = $instock;
                            $shop_product->pivot->save();
                        }
                        
                    }

                    $shopprod = $shop->products()->where('product_id', $product->id)->first();
                    if (is_null($shopprod)) {

                        $shop->products()->attach($product, ['in_stock' => $quantity_in, 'location' => $row['location'], 'product_no' => $row['product_no'], 'barcode' => $row['barcode'], 'unit_cost' => $unit_cost, 'retail_price' => $retail_price,  'wholesale_price' => $wholesaleprice, 'time_created' => $now, 'brand' => $row['brand'], 'model' => $row['model'], 'type' => $row['type'],'size' => $row['size'], 'color' => $row['color'], 'length' => $row['length'], 'width' => $row['width'], 'thick' => $row['thick'], 'height' => $row['height'], 'volume' => $row['volume'], 'weight' => $row['weight']]);
                        
                    }

                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    }elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                                $shop_product->pivot->status = 'Low Stock';
                    }
                    $shop_product->pivot->save();

                    $prod_unit = new ProductUnit();
                    $prod_unit->shop_id = $shop->id;
                    $prod_unit->product_id = $product->id;
                    $prod_unit->unit_name = $product->basic_unit;
                    $prod_unit->is_basic = true;
                    $prod_unit->qty_equal_to_basic = 1;
                    $prod_unit->unit_price = $retail_price;
                    $prod_unit->save();
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
                        $stock = Stock::create([
                            'shop_id' => $shop->id,
                            'product_id' => $product->id,
                            'quantity_in' => $quantity_in,
                            'unit_cost' => $unit_cost,
                            'source' => 'Circle Counting',
                            'expire_dates' => $expire_date,
                            'time_created' => $now
                        ]);
                    }

                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if (!is_null($shop_product)) {

                        $stock_in = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_in');
                        $sold = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                        $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                        $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                        $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                            
                        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
                        $shop_product->pivot->unit_cost = $unit_cost;
                        $shop_product->pivot->in_stock = $instock;
                        $shop_product->pivot->save();
                    }

                    $shopprod = $shop->products()->where('product_id', $product->id)->first();
                    if (is_null($shopprod)) {

                            $shop->products()->attach($product, ['in_stock' => $quantity_in, 'location' => $row['location'], 'product_no' => $row['product_no'], 'barcode' => $row['barcode'], 'unit_cost' => $unit_cost, 'retail_price' => $retail_price,'wholesale_price' => $wholesaleprice, 'time_created' => $now, 'brand' => $row['brand'], 'model' => $row['model'], 'type' => $row['type'],'size' => $row['size'], 'color' => $row['color'], 'length' => $row['length'], 'width' => $row['width'], 'thick' => $row['thick'], 'height' => $row['height'], 'volume' => $row['volume'], 'weight' => $row['weight']]);
                        
                    }
                    
                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    }elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                                $shop_product->pivot->status = 'Low Stock';
                    }
                    $shop_product->pivot->save();
                    $prod_unit = ProductUnit::where('product_id', $product->id)->where('is_basic', true)->where('shop_id', $shop->id)->first();
                    if (is_null($prod_unit)) {       
                        $prod_unit = new ProductUnit();
                        $prod_unit->shop_id = $shop->id;
                        $prod_unit->product_id = $product->id;
                        $prod_unit->unit_name = $product->basic_unit;
                        $prod_unit->is_basic = true;
                        $prod_unit->qty_equal_to_basic = 1;
                        $prod_unit->unit_price = $retail_price;
                        $prod_unit->save();
                    }else{
                        $prod_unit->unit_price = $retail_price;
                        $prod_unit->save();

                        $shop_product->pivot->retail_price = $retail_price;
                        $shop_product->pivot->save();
                    }
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
            'basic_unit' => 'required|string',
        ];
    }
}
