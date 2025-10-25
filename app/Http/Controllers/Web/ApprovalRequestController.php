<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\DiscountApproval;
use App\Models\Product;
use App\Models\SaleTemp;
use App\Models\SaleItemTemp;
use App\Models\ProInvoice;
use App\Models\InvoiceApproval;

class ApprovalRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Approval Requests';
        $title = 'Approval Requests';
        $shop = Shop::find(Session::get('shop_id'));
        $discapprovals = DiscountApproval::where('shop_id', $shop->id)->join('products', 'products.id', '=', 'discount_approvals.product_id')->join('users', 'users.id', '=', 'discount_approvals.user_id')->select('discount_approvals.id as id', 'slug', 'disc_percent', 'discount', 'status', 'users.first_name as user', 'approver', 'approved_time', 'comments')->get();
        $invapprovals = InvoiceApproval::where('invoice_approvals.shop_id', $shop->id)->join('pro_invoices', 'pro_invoices.id', '=', 'invoice_approvals.pro_invoice_id')->join('users', 'users.id', '=', 'invoice_approvals.user_id')->select('invoice_approvals.id as id', 'pro_invoice_id', 'invoice_no', 'invoice_approvals.status as status', 'users.first_name as user', 'approver', 'approved_at', 'comments')->get();
        return view('approval-requests.index', compact('page', 'title', 'discapprovals', 'invapprovals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $discapproval = DiscountApproval::find(decrypt($id));
        $discapproval->approver = Auth::user()->first_name;
        $discapproval->status = 'Approved';
        $discapproval->approved_time = Carbon::now();
        $discapproval->save();

        return redirect('approval-requests')->with('success', 'Discount Approved successfully');
    }

    public function approveInvoice($id)
    {
        $invoice = ProInvoice::find(decrypt($id));
        if (!is_null($invoice)) {
            $invapproval = InvoiceApproval::where('pro_invoice_id', $invoice->id)->where('status', 'Awaiting for Approval')->first();
            if (!is_null($invapproval)) {
                $invapproval->approver = Auth::user()->first_name;
                $invapproval->status = 'Approved';
                $invapproval->approved_at = Carbon::now();
                $invapproval->save();

                $invoice->status = 'Approved';
                $invoice->save();

                return redirect()->route('pro-invoices.show', encrypt($invoice->id))->with('success', 'Proforma Invoice Approved successfully');
            }
        }else{
            return redirect()->back()->with('info', 'Proforma Invoice not Found');
        }
    }

    public function rejectProformaInvoice($id)
    {
        $page = 'Reject Proforma Invoice';
        $title = 'Reject Proforma Invoice';
        $invoice = ProInvoice::find(decrypt($id));

        return view('approval-requests.reject-proforma', compact('page', 'title', 'invoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $invoice = ProInvoice::find($request['id']);
        if (!is_null($invoice)) {
            $invapproval = InvoiceApproval::where('pro_invoice_id', $invoice->id)->where('status', 'Awaiting for Approval')->first();
            if (!is_null($invapproval)) {
                $invapproval->approver = Auth::user()->first_name;
                $invapproval->status = 'Rejected';
                $invapproval->comments = $request['comments'];
                $invapproval->approved_at = Carbon::now();
                $invapproval->save();

                $invoice->status = 'Rejected';
                $invoice->save();
            
                return redirect()->route('pro-invoices.show', encrypt($invoice->id))->with('success', 'Proforma Invoice Rejected successfully');
            }
        }else{
            return redirect()->back()->with('info', 'Proforma Invoice not Found');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $discapproval = DiscountApproval::find(decrypt($id));
        $product = Product::find($discapproval->product_id);
        $page = 'Reject Discount';
        $title = 'Reject Discount of '.($discapproval->disc_percent+0).' % for '.$product->slug;

        return view('approval-requests.edit', compact('page', 'title', 'discapproval'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $discapproval = DiscountApproval::find(decrypt($id));
        $discapproval->status = 'Rejected';
        $discapproval->comments = $request['comments'];
        $discapproval->approver = Auth::user()->first_name;
        $discapproval->approved_time = Carbon::now();
        $discapproval->save();

        $saletemp = SaleTemp::find($discapproval->sale_temp_id);
        if (!is_null($saletemp)) {
            $saleItemTemp = SaleItemTemp::where('sale_temp_id', $saletemp->id)->where('product_id', $discapproval->product_id)->first();
            if (!is_null($saleItemTemp)) {
                $saleItemTemp->disc_percent = 0;
                $saleItemTemp->total_discount = 0;
                $saleItemTemp->discount = 0;    
                if ($saleItemTemp->with_vat == 'yes') {
                    $vat_amount =  ($saleItemTemp->price-$saleItemTemp->total_discount)*($settings->tax_rate/100);
                    $saleItemTemp->vat_amount = $vat_amount;
                }else{
                    $saleItemTemp->vat_amount = 0;
                }
                $saleItemTemp->save();
            }
        }

        return redirect('approval-requests')->with('success', 'Discount Rejected successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Notifications marked as Read successfully');
    }
}
