<?php

namespace App\Http\Controllers\Web;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Jobs\StockUpdaterJob;
use App\Models\AccountStatement;
use App\Models\ActionLog;
use App\Models\AnSale;
use App\Models\AnSaleItem;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\DeviceSale;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\ExpSupplierTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTemp;
use App\Models\InvoiceServiceItemTemp;
use App\Models\InvoiceServitem;
use App\Models\PaymentVoucher;
use App\Models\POrderTemp;
use App\Models\ProdDamage;
use App\Models\Product;
use App\Models\ProInvoice;
use App\Models\Purchase;
use App\Models\PurchaseCostItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderTemp;
use App\Models\PurchasePayment;
use App\Models\RmItem;
use App\Models\SaleItemTemp;
use App\Models\SalePayment;
use App\Models\SaleReturnItem;
use App\Models\SaleTemp;
use App\Models\ServiceItemTemp;
use App\Models\ServiceSaleItem;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierAccount;
use App\Models\SupplierTransaction;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\TransformationTransferItem;
use App\Models\TripLog;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;

class RecycleBinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $page = "Recyclebin";
        $title = 'Recyclebin';

        return view('account.recyclebin.index', compact('page', 'title'));
    }

    public function products(Request $request){
        $page = 'Recycled Products';
        $title = 'Recycled Products';
        $title_sw = 'Recycled Products';
        $shop = Shop::find(session::get('shop_id'));
        //dd($shop);
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }
        $products = Product::where('products.shop_id', $shop->id)->where('is_deleted', true)->where('is_active',true)->get();
       // dd($products);
        // if (!empty($request->customer)) {
        //     $cus_id = decrypt($request->customer);
        //     $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->where('customer_id', $cus_id)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        // } else {
        //     $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        // }
        //$customers = Customer::where('shop_id', $shop->id)->get();

        return view('account.recyclebin.products', compact('page', 'title', 'title_sw',  'is_post_query', 'start_date', 'end_date','products'));

    }

    public function recycleProduct($id)
    {
        $product = Product::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        if (!$product->is_deleted) {
            return redirect()->back()->with('info', 'Product is already active');
        }

        $product->is_deleted = false;
        $product->save();

        return redirect()->back()->with('success', 'Product restored successfully');
    }

   public function delRecycleProduct($id)
    {
        $product = Product::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $sales = AnSaleItem::where('product_id', $product->id)
            ->where('shop_id', $shop->id)
            ->count();

        $transfers = TransferOrderItem::where('product_id', $product->id)
            ->where('shop_id', $shop->id)
            ->count();

        $stocks = Stock::where('product_id', $product->id)
            ->where('shop_id', $shop->id)
            ->count();

        $invoiceItems = InvoiceItem::where('product_id', $product->id)->count();

        $categories = $shop->categories()
            ->whereHas('products', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->count();

        if ($sales || $transfers || $stocks || $invoiceItems || $categories) {
            return redirect()->back()->with(
                'info',
                'Cannot permanently delete this product because it has related records.'
            );
        }

        foreach ($shop->categories as $category) {
            $category->products()->detach($product->id);
        }

        Stock::where('product_id', $product->id)->delete();

        $product->delete();

        return redirect()->back()->with('success', 'Product permanently deleted');
    }

    public function delMultipleRecycleProducts(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        $ids = $request->ids;

        if (empty($ids)) {
            return redirect()->back()->with('info', 'No products selected');
        }

        $deletedCount = 0;
        $blockedProducts = [];

        foreach ($ids as $id) {

            $product = Product::find($id);

            if (!$product) {
                continue;
            }

            $sales = AnSaleItem::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $transfers = TransferOrderItem::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $stocks = Stock::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $invoiceItems = InvoiceItem::where('product_id', $product->id)->count();

            $categories = $shop->categories()
                ->whereHas('products', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->count();

            if ($sales || $transfers || $stocks || $invoiceItems || $categories) {
                $blockedProducts[] = $product->name;
                continue;
            }

            foreach ($shop->categories as $category) {
                $category->products()->detach($product->id);
            }

            Stock::where('product_id', $product->id)->delete();

            $product->delete();

            $deletedCount++;
        }

        if ($deletedCount > 0 && count($blockedProducts) > 0) {
            return redirect()->back()->with(
                'warning',
                "{$deletedCount} products deleted. Cannot delete: " . implode(', ', $blockedProducts)
            );
        }

        if ($deletedCount > 0) {
            return redirect()->back()->with(
                'success',
                "{$deletedCount} products permanently deleted."
            );
        }

        return redirect()->back()->with(
            'info',
            'No products were deleted because all have related records.'
        );
    }

    public function recycleMultipleProducts(Request $request)
    {
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return redirect()->back()->with('info', 'No products selected');
        }

        foreach ($ids as $id) {

            $product = Product::find($id);

            if (!$product) continue;

            // restore
            $product->is_deleted = false;
            $product->save();
        }

        return redirect()->back()->with('success', 'Selected products restored successfully');
    }

    public function emptyRecycleProducts(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));

        if (!Auth::user()->can('view-recyclebin')) {
            return view('errors.401');
        }

        $deletedProducts = Product::where('is_deleted', true)->get();

        if ($deletedProducts->isEmpty()) {
            return redirect('recyclebin')->with('info', 'No Recycle Products selected to Delete');
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($deletedProducts as $product) {

            // 1. Check all related records (IMPORTANT FIX)
            $sales = AnSaleItem::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $transfers = TransferOrderItem::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $stocks = Stock::where('product_id', $product->id)
                ->where('shop_id', $shop->id)
                ->count();

            $categories = $shop->categories()
                ->whereHas('products', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->count();

            // 🔥 IMPORTANT: missing FK check that caused your crash
            $invoiceItems = InvoiceItem::where('product_id', $product->id)->count();

            // 2. Block deletion if ANY relation exists
            if (
                $sales > 0 ||
                $transfers > 0 ||
                $stocks > 0 ||
                $categories > 0 ||
                $invoiceItems > 0
            ) {
                $skippedCount++;
                continue;
            }

            // 3. Clean related stocks
            Stock::where('product_id', $product->id)->delete();

            // 4. Detach categories
            foreach ($shop->categories as $category) {
                $category->products()->detach($product->id);
            }

            // 5. Finally delete product permanently
            $product->delete();

            // 6. Log action
            $actlog = new ActionLog();
            $actlog->shop_id = $shop->id;
            $actlog->user_id = Auth::user()->id;
            $actlog->action_type = 'Delete Product';
            $actlog->log_message = 'Product ' . $product->name . ' permanently deleted from recycle bin';
            $actlog->save();

            $deletedCount++;
        }

        // 7. Proper response messages
        if ($deletedCount > 0) {
            return redirect('recyclebin')->with(
                'success',
                "$deletedCount products deleted permanently. $skippedCount skipped due to related records."
            );
        }

        return redirect('recyclebin')->with(
            'info',
            "No products were deleted because all have related records."
        );
    }

    public function sales(Request $request)
    {
        $page = 'Recycled Sales';
        $title = 'Recycled Sales';
        $title_sw = 'Recycled Sales';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        if (!empty($request->customer)) {
            $cus_id = decrypt($request->customer);
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->where('customer_id', $cus_id)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        } else {
            $sales = AnSale::where('an_sales.shop_id', $shop->id)->where('is_deleted', true)->whereBetween('an_sales.time_created', [$start, $end])->join('users', 'users.id', '=', 'an_sales.user_id')->join('customers', 'customers.id', '=', 'an_sales.customer_id')->select('customers.name as name', 'customers.id as customer_id', 'an_sales.sale_amount_paid as sale_amount_paid', 'an_sales.sale_amount as sale_amount', 'an_sales.sale_discount as sale_discount', 'an_sales.time_created as time_created', 'an_sales.created_at as created_at', 'an_sales.updated_at as updated_at', 'an_sales.id as id', 'an_sales.tax_amount as tax_amount', 'an_sales.del_by as del_by', 'an_sales.time_paid as time_paid', 'an_sales.status as status', 'users.first_name as first_name')->orderBy('an_sales.time_created', 'desc')->paginate(20)->withQueryString();
        }
        $customers = Customer::where('shop_id', $shop->id)->get();

        return view('account.recyclebin.sales', compact('page', 'title', 'title_sw', 'sales', 'is_post_query', 'start_date', 'end_date', 'customers'));
    }

    public function recycleSale($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $sale = AnSale::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($sale)) {
                 $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $shop_product = $shop->products()->where('id', $item->product_id)->first();

                        $item->is_deleted = false;
                        $item->del_by = null;
                        $item->save();
                        // $item->delete();
                        dispatch(new StockUpdaterJob($shop, $item->product_id));
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->is_deleted = false;
                        $sitem->del_by = null;
                        $sitem->save();
                        // $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->is_deleted = false;
                        $payment->save();
                        // $payment->delete();
                    }
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->is_deleted = false;
                    $acctrans->save();
                }
                
                $sale->is_deleted = false;
                $sale->del_by = null;
                $sale->save();
                // $sale->delete();

                $success = 'Your sale was succesfuly Restored';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return view('errors.401');
        }
    }

    public function delRecycleSale($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $sale = AnSale::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (is_null($sale)) {
                return redirect('forbiden');
            } else {
                $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($items)) {
                    foreach ($items as $key => $item) {
                        $item->delete();
                        dispatch(new StockUpdaterJob($shop, $item->product_id));
                    }
                }

                $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                if (!is_null($servitems)) {
                    foreach ($servitems as $key => $sitem) {
                        $sitem->delete();
                    }
                }

                $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                if (!is_null($payments)) {
                    foreach ($payments as $key => $payment) {
                        $payment->delete();
                    }
                }

                $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                if (!is_null($acctrans)) {
                    $acctrans->delete();
                }

                $trips = TripLog::where('an_sale_id', $sale->id)->get();
                foreach ($trips as $key => $value) {
                    $value->an_sale_id = null;
                    $value->save();
                }

                $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
                if (!is_null($dsale)) {
                    $dsale->delete();
                }
                
                $sale->delete();

                $success = 'Your sale was succesfuly deleted';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return view('errors.401');
        }
    }


    public function delMultipleRecycleSales(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            if ($request->is_restore == 1) {
                if (!empty($request->input('ids'))) {
                    foreach ($request->input('ids') as $key => $id) {
                        $sale = AnSale::find($id);
                        if (!is_null($sale)) {
                            $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($items)) {
                                foreach ($items as $key => $item) {
                                    
                                    $shop_product = $shop->products()->where('id', $item->product_id)->first();
                                    $stock = Stock::find($item->stock_id);
                                    if (!is_null($stock)) {
                                        $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                                        if ($stock->quantity_in == $stock->quantity_out) {
                                            $stock->is_utilized = true;
                                        }else{
                                            $stock->is_utilized = false;
                                        }
                                        $stock->save();
                                    }
                                    $item->is_deleted = false;
                                    $item->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                                    $item->save();
                                    // $item->delete();
                                    dispatch(new StockUpdaterJob($shop, $item->product_id));
                                }
                            }

                            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($servitems)) {
                                foreach ($servitems as $key => $sitem) {
                                    $sitem->is_deleted  = false;
                                    $sitem->del_by = Auth::user()->first_name.'('.Carbon::now().')';
                                    $sitem->save();
                                    // $sitem->delete();
                                }
                            }

                            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                            if (!is_null($payments)) {
                                foreach ($payments as $key => $payment) {
                                    $payment->is_deleted = false;
                                    $payment->save();
                                    $acctrans = CustomerTransaction::find($payment->trans_id);
                                    if (!is_null($acctrans)) {
                                        if ($acctrans->payment == $payment->amount) {
                                            $acctrans->is_deleted = false;
                                            $acctrans->save();

                                            $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                            if (!is_null($astmt)) {
                                                $astmt->is_deleted = false;
                                                $astmt->save();
                                            }
                                        }else{
                                            $acctrans->trans_invoice_amount = $acctrans->trans_invoice_amount+$payment->amount;
                                            $acctrans->is_utilized = false;
                                            $acctrans->save();
                                        }
                                    }
                                }
                            }

                            $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->is_deleted = false;
                                $acctrans->save();
                            }
                            
                            $sale->is_deleted = false;
                            $sale->del_by = Auth::user()->first_name.' ('.Carbon::now().')';
                            $sale->save();
                            // $sale->delete();

                            $actlog = new ActionLog();
                            $actlog->shop_id = $shop->id;
                            $actlog->user_id = Auth::user()->id;
                            $actlog->action_type = 'Restore Invoice';
                            $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been Restored';
                            $actlog->save();
                        }
                    }
                    
                    $success = 'Sales were restored successfully';
                    return redirect('recyclebin')->with('success', $success);
                }else{
                    return redirect('recyclebin')->with('info', 'No Sales selected to restore');
                }
            }else{
                if (!empty($request->input('ids'))) {
                    foreach ($request->input('ids') as $key => $id) {
                        $sale = AnSale::find($id);
                        if (!is_null($sale)) {
                            $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($items)) {
                                foreach ($items as $key => $item) {
                                    
                                    $shop_product = $shop->products()->where('id', $item->product_id)->first();
                                    $stock = Stock::find($item->stock_id);
                                    if (!is_null($stock)) {
                                        $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                                        if ($stock->quantity_in == $stock->quantity_out) {
                                            $stock->is_utilized = true;
                                        }else{
                                            $stock->is_utilized = false;
                                        }
                                        $stock->save();
                                    }
                                    $item->delete();
                                    dispatch(new StockUpdaterJob($shop, $item->product_id));
                                }
                            }

                            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($servitems)) {
                                foreach ($servitems as $key => $sitem) {
                                    $sitem->delete();
                                }
                            }

                            $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                            
                            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                            if (!is_null($payments)) {
                                foreach ($payments as $key => $payment) {
                                    $payment->delete();
                                    $acctrans = CustomerTransaction::find($payment->trans_id);
                                    if (!is_null($acctrans)) {
                                        if ($acctrans->payment == $payment->amount) {
                                            $acctrans->delete();

                                            $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                            if (!is_null($astmt)) {
                                                $astmt->delete();
                                            }
                                        }
                                    }
                                }
                            }


                            $trips = TripLog::where('an_sale_id', $sale->id)->get();
                            foreach ($trips as $key => $value) {
                                $value->an_sale_id = null;
                                $value->save();
                            }

                            $dsale = DeviceSale::where('an_sale_id', $sale->id)->first();
                            if (!is_null($dsale)) {
                                $dsale->delete();
                            }

                            $sale->delete();

                            $actlog = new ActionLog();
                            $actlog->shop_id = $shop->id;
                            $actlog->user_id = Auth::user()->id;
                            $actlog->action_type = 'Delete Invoice';
                            $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been permanently deleted';
                            $actlog->save();
                        }
                    }
                    
                    $success = 'Sales were deleted successfully';
                    return redirect('recyclebin')->with('success', $success);
                }else{
                    return redirect('recyclebin')->with('info', 'No Sales selected to Delete');
                }
            }
        } else {
            return view('errors.401');
        }
    }

    //Delete multiple purchases
    public function delMultipleRecyclePurchases(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            if (!empty($request->input('ids'))) {
                if ($request->is_restore == 1) {
                    foreach ($request->input('ids') as $key => $id) {
                        $purchase = Purchase::where('id', $id)->where('shop_id', $shop->id)->first();
                        $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

                        foreach ($pitems as $key => $value) {
                            $value->is_deleted = false;
                            $value->save();
                            dispatch(new StockUpdaterJob($shop, $value->product_id));
                        }

                        $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
                        foreach ($payments as $key => $payment) {
                            $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                            if (!is_null($pv)) {
                                $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                                if (!is_null($acctrans)) {
                                    $acctrans->is_deleted = false;
                                    $acctrans->save();
                                }
                            }

                            $payment->is_deleted = false;
                            $payment->save();
                        }

                        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                        if ($acctrans) {
                            $acctrans->is_deleted = false;
                            $acctrans->save();
                        }
                        
                        $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                        $total_cost = 0;
                        foreach ($costitems as $key => $item) {
                            $item->is_deleted = false;
                            $item->save();
                        }
                        
                        $purchase->is_deleted = false;
                        $purchase->save();
                    }

                    $success = 'Purchases were restored successfully';
                    return redirect('recycle-purchases')->with('success', $success);
                }else{
                    foreach ($request->input('ids') as $key => $id) {
                        $purchase = Purchase::where('id', $id)->where('shop_id', $shop->id)->first();
                        $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

                        foreach ($pitems as $key => $value) {
                            $value->delete();
                            dispatch(new StockUpdaterJob($shop, $value->product_id));
                        }

                        $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
                        foreach ($payments as $key => $payment) {
                            $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                            if (!is_null($pv)) {
                                $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                                if (!is_null($acctrans)) {
                                    $acctrans->delete();
                                }
                                $pv->delete();
                            }
                            $payment->delete();
                        }

                        $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                        if ($acctrans) {
                            $acctrans->delete();
                        }
                        
                        $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                        $total_cost = 0;
                        foreach ($costitems as $key => $item) {
                            $item->delete();
                        }
                        $purchase->delete();
                    }

                    $success = 'Purchases were deleted successfully';
                    return redirect('recycle-purchases')->with('success', $success);
                }
            }else {
                return redirect('recycle-purchases')->with('info', 'No Recycle Purchases selected to Delete');
            }
        } else {
            return view('errors.401');
        }
    }

    //delMultipleRecycleExpense

    public function delMultipleRecycleExpenses(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            if (!empty($request->input('ids'))) {
                if ($request->is_restore == 1) {
                    foreach ($request->ids as $key => $id) {
                        $expense = Expense::where('id', $id)->where('shop_id', $shop->id)->first();
                        if (!is_null($expense)) {
                            $expense->is_deleted = false;
                            $expense->save();
                        }
                    }
                }else{
                    foreach ($request->ids as $key => $id) {
                        $expense = Expense::where('id', $id)->where('shop_id', $shop->id)->first();
                        $pvexps = 0;
                        $invoexps = 0;
                        if (!is_null($expense)) {
                            $exppays = ExpensePayment::where('expense_id', $expense->id)->get();
                            foreach ($exppays as $key => $pay) {
                                $pv = PaymentVoucher::where('pv_no', $pay->pv_no)->where('shop_id', $shop->id)->first();
                                if (!is_null($pv)) {
                                    $pv->amount = $pv->amount - $pay->amount;
                                    $pv->save();

                                    $trans = ExpSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();

                                    if (!is_null($trans)) {
                                        $trans->payment = $trans->payment - $pay->amount;
                                        $trans->save();

                                        if ($trans->payment <= 0) {
                                            $trans->delete();
                                        }
                                    }
                                    if ($pv->amount <= 0) {
                                        $pv->delete();
                                    }
                                }
                                $pay->delete();
                            }

                            $invoexps = Expense::where('trans_id', $expense->trans_id)->where('shop_id', $shop->id)->count();
                            if ($invoexps > 1) {
                                $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                                if (!is_null($trans)) {
                                    $trans->amount = $trans->amount - $expense->amount;
                                    $trans->save();
                                }
                            } else {
                                $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                                if (!is_null($trans)) {
                                    $trans->delete();
                                }
                            }
                            $expense->delete();
                        }
                    }
                }

                $success = 'Expenses were deleted successfully';
                return redirect('recycle-expenses')->with('success', $success);
            } else {
                return redirect('recycle-expenses')->with('info', 'No Recycle Expenses selected to Delete');
            }
        } else {
            return view('errors.401');
        }
    }

    //Recycle Purchases
    public function recyclePurchase($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $purchase = Purchase::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($purchase)) {
                $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->is_deleted = false;
                    $value->del_by = Auth::user()->first_name . '(' . Carbon::now() . ')';
                    $value->save();
                    // $value->delete();
                    dispatch(new StockUpdaterJob($shop, $value->product_id));
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                    if (!is_null($pv)) {
                        $pv->delete();
                    }
                    $payment->is_deleted = false;
                    $payment->save();
                    // $payment->delete();
                }

                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                if ($acctrans) {
                    $acctrans->is_deleted = false;
                    $acctrans->save();
                    // $acctrans->delete();
                }

                $purchase->is_deleted = false;
                $purchase->del_by = Auth::user()->first_name . ' (' . Carbon::now() . ')';
                $purchase->save();
                // $purchase->delete();
                return redirect()->back()->with('success', 'Purchase was restored successfully');
            }
        } else {
            return view('errors.401');
        }
    }

    //Delete Purchases
    public function delRecyclePurchase($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {

            $purchase = Purchase::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($purchase)) {
                $pitems = Stock::where('time_created', $purchase->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->delete();
                    dispatch(new StockUpdaterJob($shop, $value->product_id));
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                    if (!is_null($pv)) {
                        if ($shop->subscription_type_id == 2) {

                            $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                        } else {
                            $acctrans = SupplierAccount::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                        }
                        $pv->delete();
                    }
                    $payment->delete();
                }

                $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                if ($acctrans) {
                    $acctrans->delete();
                }
                $purchase->delete();
                return redirect()->back()->with('success', 'Purchase was deleted successfully');
            }
        } else {
            return view('errors.401');
        }
    }

    //Delete Expenses
    public function delRecycleExpense($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {

            $expense = Expense::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($expense)) {
                $pitems = Stock::where('time_created', $expense->time_created)->where('shop_id', $shop->id)->get();

                foreach ($pitems as $key => $value) {
                    $value->delete();
                    dispatch(new StockUpdaterJob($shop, $value->product_id));
                    $message = 'Stock was successfully deleted';
                }

                $payments = PurchasePayment::where('purchase_id', $expense->id)->get();

                foreach ($payments as $key => $payment) {
                    $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();

                    $payment->delete();
                }

                $expense->delete();
                return redirect()->back()->with('success', 'Expense was deleted successfully');
            }
        } else {
            return view('errors.401');
        }
    }


    //Recycle Purchases
    public function recyclePurchases(Request $request)
    {
        $page = 'Recycle Bin';
        $title = 'Recycle Bin';
        $title_sw = 'Recycle Bin';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $purchases = Purchase::where('purchases.shop_id', $shop->id)->where('purchases.is_deleted', true)->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->join('users', 'users.id', '=', 'purchases.user_id')->select('purchases.id as id', 'grn_no', 'invoice_no', 'purchases.time_created as time_created', 'name', 'total_amount', 'amount_paid', 'total_cost', 'purchases.created_at as created_at', 'first_name as user')->orderBy('purchases.time_created', 'desc')->get();
        return view('account.recyclebin.purchases', compact('page', 'page', 'title', 'title_sw', 'purchases', 'is_post_query', 'start_date', 'end_date'));
    }


    //Recycle Expenses
    public function recycleExpenses(Request $request)
    {
        $page = 'Recycled Expenses';
        $title = 'Recycled Expenses';
        $title_sw = 'Recycled Expenses';
        $shop = Shop::find(Session::get('shop_id'));

        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }


        if (!empty($request->expense)) {
            $expenseType = decrypt($request->expense);
            $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', true)->where('id', $expenseType)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expenses.supplier_id as supplier_id', 'expense_type as expenses_type', 'amount as amount', 'amount_paid as amount_paid', 'exp_vat as exp_vat', 'wht_rate as wht_rate', 'wht_amount as wht_amount', 'time_created as created_at', 'exp_type as exp_type', 'status as status', 'del_by as del_by', 'description as description')
                ->orderBy('time_created', 'desc')->get();
        } else {
            $expenses =  Expense::where('shop_id', $shop->id)->where('is_deleted', true)->whereBetween('time_created', [$start, $end])->join('users', 'users.id', '=', 'expenses.user_id')->select('users.first_name as first_name', 'expenses.id as id', 'expenses.supplier_id as supplier_id', 'expense_type as expenses_type', 'amount as amount', 'amount_paid as amount_paid', 'exp_vat as exp_vat', 'wht_rate as wht_rate', 'wht_amount as wht_amount', 'time_created as created_at', 'exp_type as exp_type', 'status as status', 'del_by as del_by', 'description as description')
                ->orderBy('time_created', 'desc')->get();
        }

        return view('account.recyclebin.expenses', compact('page', 'title', 'title_sw', 'expenses', 'is_post_query', 'start_date', 'end_date', 'page'));
    }

    //Restore Expenses
    public function recycleExpensesRestore($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $expense = Expense::where('id',  decrypt($id))->where('shop_id', $shop->id)->first();
            if (!is_null($expense)) {
                $expense->is_deleted = false;
                $expense->save();
                // dd($expense);
                $success = 'Your Expense was succesfuly Restored';
                return redirect()->back()->with('success', $success);
            }
        } else {
            return view('errors.401');
        }
    }

    public function recycleItem($id)
    {
    }

    public function recycleStock($id)
    {
    }

    //Empty Sales
    public function emptyRecycleSales(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $delsales = AnSale::where('shop_id', $shop->id)->where('is_deleted', true)->get();
            if ($delsales->count() > 0) {
                foreach ($delsales as $key => $sale) {
                    $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                    if (!is_null($items)) {
                        foreach ($items as $key => $item) {
                            $shop_product = $shop->products()->where('id', $item->product_id)->first();
                            $stock = Stock::find($item->stock_id);
                            if (!is_null($stock)) {
                                $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                                if ($stock->quantity_in == $stock->quantity_out) {
                                    $stock->is_utilized = true;
                                }else{
                                    $stock->is_utilized = false;
                                }
                                $stock->save();
                            }
                            $item->delete();
                            dispatch(new StockUpdaterJob($shop, $item->product_id));
                        }
                    }
                    $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                    if (!is_null($servitems)) {
                        foreach ($servitems as $key => $sitem) {
                            $sitem->delete();
                        }
                    }
                    
                    $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                    if (!is_null($acctrans)) {
                        $acctrans->delete();
                    }
                                
                    $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                    if (!is_null($payments)) {
                        foreach ($payments as $key => $payment) {
                            $payment->delete();
                            $acctrans = CustomerTransaction::find($payment->trans_id);
                            if (!is_null($acctrans)) {
                                if ($acctrans->payment == $payment->amount) {
                                    $acctrans->delete();
                                    $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                    if (!is_null($astmt)) {
                                        $astmt->delete();
                                    }
                                }
                            }
                        }
                    }

                    $sale->delete();

                    $actlog = new ActionLog();
                    $actlog->shop_id = $shop->id;
                    $actlog->user_id = Auth::user()->id;
                    $actlog->action_type = 'Delete Invoice';
                    $actlog->log_message = 'Invoice No '.sprintf('%04d', $sale->invoice_no).' has been permanently deleted';
                    $actlog->save();
                }
                $success = 'Sales deleted permanently . . .';
                return redirect('recyclebin')->with('success', $success);
            }else{
                return redirect('recyclebin')->with('info', 'No Recycle Expenses selected to Delete');
            }
        } else {
            return view('errors.401');
        }
    }

    //Empty Expenses
    public function emptyRecycleExpenses(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $delexpenses = Expense::where('shop_id', $shop->id)->where('is_deleted', true)->get();
            if ($delexpenses->count() > 0) {
                foreach ($delexpenses as $key => $expense) {
                    $exppays = ExpensePayment::where('expense_id', $expense->id)->get();
                    foreach ($exppays as $key => $pay) {
                        $pv = PaymentVoucher::where('pv_no', $pay->pv_no)->where('shop_id', $shop->id)->first();
                        if (!is_null($pv)) {
                            $pv->amount = $pv->amount - $pay->amount;
                            $pv->save();
                            $trans = ExpSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();

                            if (!is_null($trans)) {
                                $trans->payment = $trans->payment - $pay->amount;
                                $trans->save();
                                if ($trans->payment <= 0) {
                                    $trans->delete();
                                }
                            }
                            if ($pv->amount <= 0) {
                                $pv->delete();
                            }
                        }
                        $pay->delete();
                    }

                    $invoexps = Expense::where('trans_id', $expense->trans_id)->where('shop_id', $shop->id)->count();
                    if ($invoexps > 1) {
                        $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                        if (!is_null($trans)) {
                            $trans->amount = $trans->amount - $expense->amount;
                            $trans->save();
                        }
                    } else {
                        $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                        if (!is_null($trans)) {
                            $trans->delete();
                        }
                    }
                    $expense->delete();
                }
                $success = 'Expenses deleted permanently . . .';
                return redirect('recycle-expenses')->with('success', $success);
            }else{
                return redirect('recycle-expenses')->with('info', 'No Recycle Expenses selected to Delete');
            }
        } else {
            return view('errors.401');
        }
    }

    //Empty Purchases
    public function emptyRecyclePurchases(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        if (Auth::user()->can('view-recyclebin')) {
            $delpurchases = Purchase::where('shop_id', $shop->id)->where('is_deleted', true)->get();
            if ($delpurchases->count() > 0) {
                foreach ($delpurchases as $key => $purchase) {
                    $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

                    foreach ($pitems as $key => $value) {
                        $value->delete();
                        dispatch(new StockUpdaterJob($shop, $value->product_id));
                    }

                    $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
                    foreach ($payments as $key => $payment) {
                        $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                        if (!is_null($pv)) {
                            $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                            $pv->delete();
                        }
                        $payment->delete();
                    }

                    $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                    if ($acctrans) {
                        $acctrans->delete();
                    }
                        
                    $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                    $total_cost = 0;
                    foreach ($costitems as $key => $item) {
                        $item->delete();
                    }
                    $purchase->delete();
                }

                $success = 'Purchases were deleted successfully';
                return redirect('recycle-purchases')->with('success', $success);
            }else {
                return redirect('recycle-purchases')->with('info', 'No Recycle Purchases selected to Delete');
            }
        } else {
            return view('errors.401');
        }
    }

    public function clearAllData()
    {
        $page = 'Delete All Shop Data';
        $title = 'Delete All Shop Data';
        $shop = Shop::find(Session::get('shop_id'));
        return view('account.recyclebin.clear-data', compact('page', 'title', 'shop'));
    }

    public function clearData(Request $request)
    {
        $shop = Shop::find($request['shop_id']);
        if (Auth::user()->can('view-recyclebin')) {
            if (!empty($request['confirm_name'])) {
                if ($request['confirm_name'] == $shop->name) {
                    $delsales = AnSale::where('shop_id', $shop->id)->get();
                    if ($delsales->count() > 0) {
                        foreach ($delsales as $key => $sale) {
                            $items = AnSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($items)) {
                                foreach ($items as $key => $item) {
                                    $shop_product = $shop->products()->where('id', $item->product_id)->first();
                                    $stock = Stock::find($item->stock_id);
                                    if (!is_null($stock)) {
                                        $stock->quantity_out = $stock->quantity_out-$item->quantity_sold;
                                        if ($stock->quantity_in == $stock->quantity_out) {
                                            $stock->is_utilized = true;
                                        }else{
                                            $stock->is_utilized = false;
                                        }
                                        $stock->save();
                                    }
                                    $item->delete();
                                    dispatch(new StockUpdaterJob($shop, $item->product_id));
                                }
                            }
                            $servitems = ServiceSaleItem::where('an_sale_id', $sale->id)->get();
                            if (!is_null($servitems)) {
                                foreach ($servitems as $key => $sitem) {
                                    $sitem->delete();
                                }
                            }
                            
                            $acctrans = CustomerTransaction::where('an_sale_id', $sale->id)->where('shop_id', $shop->id)->first();
                            if (!is_null($acctrans)) {
                                $acctrans->delete();
                            }
                                        
                            $payments = SalePayment::where('an_sale_id', $sale->id)->get();
                            if (!is_null($payments)) {
                                foreach ($payments as $key => $payment) {
                                    $payment->delete();
                                    $acctrans = CustomerTransaction::find($payment->trans_id);
                                    if (!is_null($acctrans)) {
                                        if ($acctrans->payment == $payment->amount) {
                                            $acctrans->delete();
                                            $astmt = AccountStatement::where('customer_transaction_id', $acctrans->id)->first();
                                            if (!is_null($astmt)) {
                                                $astmt->delete();
                                            }
                                        }
                                    }
                                }
                            }

                            $sale->delete();
                        }

                        Log::info(' Sales for shop '.$shop->name.' deleted permanently by '.Auth::user()->first_name.' '.Auth::user()->last_name);
                    }else{
                        Log::info($shop->name.' No Sales');
                    }


                    $delexpenses = Expense::where('shop_id', $shop->id)->get();
                    if ($delexpenses->count() > 0) {
                        foreach ($delexpenses as $key => $expense) {
                            $exppays = ExpensePayment::where('expense_id', $expense->id)->get();
                            foreach ($exppays as $key => $pay) {
                                $pv = PaymentVoucher::where('pv_no', $pay->pv_no)->where('shop_id', $shop->id)->first();
                                if (!is_null($pv)) {
                                    $pv->amount = $pv->amount - $pay->amount;
                                    $pv->save();
                                    $trans = ExpSupplierTransaction::where('pv_no', $pv->pv_no)->where('shop_id', $shop->id)->first();

                                    if (!is_null($trans)) {
                                        $trans->payment = $trans->payment - $pay->amount;
                                        $trans->save();
                                        if ($trans->payment <= 0) {
                                            $trans->delete();
                                        }
                                    }
                                    if ($pv->amount <= 0) {
                                        $pv->delete();
                                    }
                                }
                                $pay->delete();
                            }

                            $invoexps = Expense::where('trans_id', $expense->trans_id)->where('shop_id', $shop->id)->count();
                            if ($invoexps > 1) {
                                $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                                if (!is_null($trans)) {
                                    $trans->amount = $trans->amount - $expense->amount;
                                    $trans->save();
                                }
                            } else {
                                $trans = ExpSupplierTransaction::where('id', $expense->trans_id)->where('shop_id', $shop->id)->where('created_at', $expense->created_at)->first();
                                if (!is_null($trans)) {
                                    $trans->delete();
                                }
                            }
                            $expense->delete();
                        }

                        Log::info('Expenses for shop '.$shop->name.' deleted permanently by '.Auth::user()->first_name.' '.Auth::user()->last_name);
                    }else{
                        Log::info($shop->name.' No Expenses');
                    }

                    $delpurchases = Purchase::where('shop_id', $shop->id)->get();
                    if ($delpurchases->count() > 0) {
                        foreach ($delpurchases as $key => $purchase) {
                            $pitems = Stock::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->get();

                            foreach ($pitems as $key => $value) {
                                $value->delete();
                                dispatch(new StockUpdaterJob($shop, $value->product_id));
                            }

                            $payments = PurchasePayment::where('purchase_id', $purchase->id)->get();
                            foreach ($payments as $key => $payment) {
                                $pv = PaymentVoucher::where('pv_no', $payment->pv_no)->first();
                                if (!is_null($pv)) {
                                    $acctrans = SupplierTransaction::where('pv_no', $purchase->pv_no)->where('shop_id', $shop->id)->first();
                                    if (!is_null($acctrans)) {
                                        $acctrans->delete();
                                    }
                                    $pv->delete();
                                }
                                $payment->delete();
                            }

                            $acctrans = SupplierTransaction::where('purchase_id', $purchase->id)->where('shop_id', $shop->id)->first();
                            if ($acctrans) {
                                $acctrans->delete();
                            }
                                
                            $costitems = PurchaseCostItem::where('purchase_id', $purchase->id)->get();
                            $total_cost = 0;
                            foreach ($costitems as $key => $item) {
                                $item->delete();
                            }
                            $purchase->delete();
                        }

                        Log::info('Purchases for shop '.$shop->name.' deleted permanently by '.Auth::user()->first_name.' '.Auth::user()->last_name);
                    }else {
                        Log::info($shop->name.' No Purchases');
                    }

                    $porders = PurchaseOrder::where('shop_id', $shop->id)->get();
                    foreach ($porders as $key => $porder) {
                        $pitems = PurchaseOrderItem::where('purchase_order_id', $porder->id)->get();
                        foreach ($pitems as $key => $value) {
                            $value->delete();
                        }

                        $porder->delete();
                    }

                    $pordertemps = POrderTemp::where('shop_id', $shop->id)->get();
                    foreach ($pordertemps as $key => $ptemp) {
                        $temps = PurchaseOrderTemp::where('p_order_temp_id', $ptemp->id)->get();
                        foreach ($temps as $key => $value) {
                            $value->delete();
                        }
                        $ptemp->delete();
                    }
                    
                    $transorders = TransferOrder::where('shop_id', $shop->id)->get();
                    if ($transorders->count() > 0) {
                        foreach ($transorders as $key => $transorder) {
                            $destinshop = Shop::find($transorder->destination_id);
                            $orderitems = TransferOrderItem::where('transfer_order_id', $transorder->id)->get();
                            foreach ($orderitems as $key => $item) {
                                $item->delete();
                            }

                            $transorderstocks = Stock::where('shop_id', $destinshop->id)->where('order_id', $transorder->id)->get();
                            foreach ($transorderstocks as $key => $orderstock) {
                                $orderstock->delete();
                            }

                            if($transorder->is_transfomation_transfer == 1){
                                $transform =TransformationTransferItem::where('transfer_order_id', $transorder->id)->get();
                                foreach($transform as $value){
                                    $value->delete();

                                }
                            }
                             
                            $rmitem = RmItem::where('shop_id', $shop->id)->where('transfer_order_id', $transorder->id)->first();
                            if (!is_null($rmitem)) {
                                $rmitem->delete();
                            }

                            $transorder->delete();
                        }
                    }else {
                        Log::info($shop->name.' No Stock Transfers');
                    }

                    $products = $shop->products()->get();
                    if ($products->count() > 0) {
                        foreach ($products as $key => $product) {
                            $sales = AnSaleItem::where('shop_id', $shop->id)->where('product_id', $product->id)->get();
                            foreach ($sales as $key => $value) {
                                $value->delete();
                            }
                            $transfers = TransferOrderItem::where('shop_id', $shop->id)->where('product_id', $product->id)->get();
                            foreach ($transfers as $key => $value) {
                                $value->delete();
                            }
                            $stocks = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
                            foreach ($stocks as $key => $value) {
                                $value->delete();
                            }

                            $damages = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->get();
                            foreach ($damages as $key => $value) {
                                $value->delete();
                            }

                            $shop->products()->detach($product);
                            foreach ($shop->categories()->get() as $key => $category) {
                                $catprod = $category->products()->where('product_id', $product->id)->first();
                                if (!is_null($catprod)) {
                                    $category->products()->detach($catprod);
                                }
                            }
                        }
                    }else{
                        Log::info($shop->name.' No Products');
                    }

                    $suppliers = Supplier::where('shop_id', $shop->id)->get();
                    if ($suppliers->count() > 0) {
                        foreach ($suppliers as $key => $supplier) {
                            $supplier->delete();
                        }
                    }else{
                        Log::info($shop->name.' No Suppliers');                        
                    }

                    $services = $shop->services()->get();
                    if ($services->count() > 0) {
                        foreach ($services as $key => $service) {
                            $shop->services()->detach($service);
                        }
                    }else{
                        Log::info($shop->name.' No Services');
                    }

                    $proinvoices = ProInvoice::where('shop_id', $shop->id)->get();
                    foreach ($proinvoices as $key => $value) {
                        $invitems = InvoiceItem::where('pro_invoice_id', $value->id)->get();
                        foreach ($invitems as $i => $item) {
                            $item->delete();
                        }

                        $servinvs = InvoiceServitem::where('pro_invoice_id', $value->id)->get();
                        foreach ($servinvs as $key => $sinv) {
                            $sinv->delete();
                        }

                        $value->delete();
                    }


                    $servitems = InvoiceServiceItemTemp::where('shop_id', $shop->id)->get();
                    foreach ($servitems as $key => $stemp) {
                        $stemp->delete();
                    }

                    $proditems = InvoiceItemTemp::where('shop_id', $shop->id)->get();
                    foreach ($proditems as $key => $ptemp) {
                        $ptemp->delete();
                    }

                    $saletemps = SaleTemp::where('shop_id', $shop->id)->get();
                    foreach ($saletemps as $key => $stemp) {
                        $prodtemps = SaleItemTemp::where('sale_temp_id', $stemp->id)->get();
                        foreach ($proditems as $key => $value) {
                            $value->delete();
                        }

                        $servtemps = ServiceItemTemp::where('sale_temp_id', $stemp->id)->get();
                        foreach ($servtemps as $key => $value) {
                            $value->delete();
                        }

                        $stemp->delete();
                    }
                    $customers = Customer::where('shop_id', $shop->id)->get();
                    if ($customers->count() > 0) {
                        foreach ($customers as $key => $customer) {
                            $customer->delete();
                        }
                    }else{
                        Log::info($shop->name.' No Customers');                        
                    }

                    $actlog = new ActionLog();
                    $actlog->shop_id = $shop->id;
                    $actlog->user_id = Auth::user()->id;
                    $actlog->action_type = 'Delete All Shop Data';
                    $actlog->log_message = 'All Shop Data has been permanently deleted';
                    $actlog->save();
                    return redirect('recyclebin')->with('success', 'All shop data deleted successfully');
                }else{
                    return redirect()->back()->with('error', 'Shop Name entered do not match. Please Enter Correct Shop Name to continue');
                }
            }else{
                return redirect()->back()->with('error', 'Please Enter Correct Shop Name to continue');
            }
        } else {
            return view('errors.401');
        }
    }
}