<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\User;
use App\Models\Shop;
use App\Models\PmTransfer;
use App\Models\PmTransferItem;
use App\Models\PmItem;
use App\Models\PmUseItem;
use App\Models\PmDamage;
use App\Models\PackingMaterial;
use App\Jobs\PMUpdaterJob;

class PmTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'PM Transfers';
        $title = 'Packing Material Transfers';

        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $shop = Shop::find(Session::get('shop_id'));
        $transfers = PmTransfer::where('shop_id', $shop->id)->whereBetween('pm_transfer_date', [$start, $end])->orderBy('pm_transfer_date', 'desc')->get();
        $inc_transfers = PmTransfer::where('destin_id', $shop->id)->whereBetween('pm_transfer_date', [$start, $end])->orderBy('pm_transfer_date', 'desc')->get();

        return view('production.packing-materials.transfers.index', compact('page', 'title', 'transfers', 'inc_transfers', 'is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New PM Transfer';
        $title = 'New PM Transfer';

        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $shops = $user->shops()->where('shop_id', '!=', $shop->id)->get();
        $lastpmt = PmTransfer::where('shop_id', $shop->id)->orderByRaw('CONVERT(pmt_no, SIGNED) desc')->first();
        $pmt_no = 0;
        if (!is_null($lastpmt)) {
            $pmt_no = $lastpmt->pmt_no+1;
        }else{
            $pmt_no = 1;
        }

        $pmt = PmTransfer::where('shop_id', $shop->id)->where('user_id', $user->id)->where('status', 'Pending')->first();
        if (is_null($pmt)) {
            $pmt = new PmTransfer();
            $pmt->shop_id = $shop->id;
            $pmt->user_id = $user->id;
            $pmt->pmt_no = $pmt_no;
            $pmt->pm_transfer_date = Carbon::now();
            $pmt->save();
        }

        $materials = $shop->packingMaterials()->select('packing_materials.id as id', 'name')->get();
        $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_transfer_items.packing_material_id')->select('pm_transfer_items.id as id', 'name', 'qty', 'unit_cost')->get();
        return view('production.packing-materials.transfers.create', compact('page', 'title', 'shop', 'shops', 'pmt', 'materials', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $timenow = Carbon::now();
        $time = date('H:i:s', strtotime($timenow));
        $pmt_date = $request['pm_transfer_date'] . ' ' . $time;
        $pmt = PmTransfer::find($request['id']);
        if (!is_null($pmt)) {
            $shop = Shop::find($pmt->shop_id);
            $pmt->destin_id = $request['destin_id'];
            $pmt->reason = $request['reason'];
            $pmt->pm_transfer_date = $pmt_date;
            $pmt->status = 'Submitted';
            $pmt->save();

            $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->get();
            foreach ($items as $key => $item) {

                dispatch(new PMUpdaterJob($item->packing_material_id, $shop));
            }
            return redirect('pm-transfers')->with('success', 'Transfer Submitted successfully');
        }else{
            return redirect()->back()->with('error', 'item not found');
        }
    }
 
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'PM Transfer';
        $title = 'PM Transfer';
        $pmt = PmTransfer::find(decrypt($id));
        if (!is_null($pmt)) {
            $company = Company::find(Session::get('company_id'));
            $shop = Shop::find(Session::get('shop_id'));
            $source = Shop::find($pmt->shop_id);
            $destin = Shop::find($pmt->destin_id);
            $user = User::find($pmt->user_id);
            $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_transfer_items.packing_material_id')->select('pm_transfer_items.id as id', 'name', 'src_qty', 'des_qty', 'qty', 'rec_qty', 'unit_cost')->get();

            return view('production.packing-materials.transfers.show', compact('page', 'title', 'company', 'shop', 'source', 'destin', 'user', 'pmt', 'items'));
        }
    }


    public function receiveForm($id)
    {
        $page = 'Receive PM Transfer';
        $title = 'Receive PM Transfer';
        $pmt = PmTransfer::find(decrypt($id));
        if (!is_null($pmt)) {
            $shop = Shop::find(Session::get('shop_id'));
            $source = Shop::find($pmt->shop_id);
            $destin = Shop::find($pmt->destin_id);
            $user = User::find($pmt->user_id);
            $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->join('packing_materials', 'packing_materials.id', '=', 'pm_transfer_items.packing_material_id')->select('pm_transfer_items.id as id', 'name', 'src_qty', 'des_qty', 'qty', 'rec_qty', 'unit_cost')->get();

            return view('production.packing-materials.transfers.receive', compact('page', 'title', 'shop', 'source', 'destin', 'user', 'pmt', 'items'));
        }
    }

    public function receivePM(Request $request)
    {
        $pmt = PmTransfer::find($request['pm_transfer_id']);
        if (!is_null($pmt)) {
            $user = Auth::user();
            $pmt->status = 'Received';
            $pmt->received_at = Carbon::now();
            $pmt->receiver = $user->first_name.' '.$user->last_name;
            $pmt->save();

            $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->get();
            foreach ($items as $key => $item) {
                $destin = Shop::find($pmt->destin_id);

                $pmitem  = new PmItem;
                $pmitem->packing_material_id = $item->packing_material_id;
                $pmitem->shop_id = $pmt->destin_id;
                $pmitem->qty = $item->rec_qty;
                $pmitem->unit_cost = $item->unit_cost;
                $pmitem->total = $pmitem->qty*$pmitem->unit_cost;
                $pmitem->date = Carbon::now();
                $pmitem->save();

                $shop_packing_material = $destin->packingMaterials()->where('packing_material_id', $item->packing_material_id)->where('is_deleted' , false)->first();
                if (is_null($shop_packing_material)) {
                    $material = PackingMaterial::find($item->packing_material_id);
                    $destin->packingMaterials()->attach($material, ['in_store' => $pmitem->qty, 'unit_cost' => $pmitem->unit_cost, 'description' => '']);
                }else{
                    $item->des_qty = $shop_packing_material->pivot->in_store;
                    $item->save();
                }

                dispatch(new PMUpdaterJob($pmitem->packing_material_id, $destin));
            }

            return redirect()->route('pm-transfers.show', encrypt($pmt->id))->with('success', 'PM Transfer received successfully');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $timenow = Carbon::now();
        $time = date('H:i:s', strtotime($timenow));
        $pmt_date = $request['pm_transfer_date'] . ' ' . $time;
        $pmt = PmTransfer::find(decrypt($id));
        if (!is_null($pmt)) {
            $pmt->pm_transfer_date = $pmt_date;
            $pmt->reason = $request['reason'];
            $pmt->save();

            return redirect('pm-transfers')->with('success', 'PM Transfer updated successfully');
        }else{
            return redirect('pm-transfers')->with('error', 'Item not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pmt = PmTransfer::find(decrypt($id));
        if (!is_null($pmt)) {
            $items = PmTransferItem::where('pm_transfer_id', $pmt->id)->get();
            foreach ($items as $key => $value) {
                $value->delete();
            }
        }

        return redirect('pm-transfers')->with('success', 'PM Transfer deleted successfully');
    }
}
