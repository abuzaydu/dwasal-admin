<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use Auth;
use App\Models\Shop;
use App\Models\MaterialWip;
use App\Models\MaterialWipStock;
use App\Models\MaterialWipStockTemp;

class MaterialWIPsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page ='Material WIPs';
        $title = 'Material WIPs';

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
        $mwips = MaterialWipStock::where('material_wip_stocks.shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('material_wips', 'material_wips.id', '=', 'material_wip_stocks.material_wip_id')->select('material_wip_stocks.id as id', 'title', 'date', 'opening_qty', 'produced', 'used', 'dam_qty', 'closing_qty')->get();
        // return $mwips;

        return view('production.wips.mwips.index', compact('page', 'title', 'mwips','is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Material WIPs';
        $title = 'New Material WIPs';
        $now = Carbon::now();
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        // return $yesterday;
        $date = $now->format('Y-m-d');
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $materials = MaterialWip::where('shop_id', $shop->id)->select('id', 'title')->get();
        foreach ($materials as $key => $material) {
            $opening_qty = 0;
            $lastwip = MaterialWipStock::where('shop_id', $shop->id)->where('material_wip_id', $material->id)->where('date', $yesterday)->first();
            if (!is_null($lastwip)) {
                $opening_qty = $lastwip->closing_qty;
            }
            $wiptemp = MaterialWipStockTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('material_wip_id', $material->id)->first();
            if (is_null($wiptemp)) {
                $wiptemp = new MaterialWipStockTemp();
                $wiptemp->shop_id = $shop->id;
                $wiptemp->user_id = $user->id;
                $wiptemp->material_wip_id = $material->id;
                $wiptemp->date = $date;
                $wiptemp->opening_qty = $opening_qty;
                $wiptemp->save();
            }
        }

        return view('production.wips.mwips.create', compact('page', 'title', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $wiptemps = MaterialWipStockTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($wiptemps as $key => $temp) {
            $wip = new MaterialWipStock();
            $wip->shop_id = $temp->shop_id;
            $wip->user_id = $temp->user_id;
            $wip->material_wip_id = $temp->material_wip_id;
            $wip->date = $request['date'];
            $wip->opening_qty = $temp->opening_qty;
            $wip->produced = $temp->produced;
            $wip->used = $temp->used;
            $wip->dam_qty = $temp->dam_qty;
            $wip->closing_qty = ($wip->opening_qty+$wip->produced)-($wip->used+$wip->dam_qty);
            $wip->save();

            $temp->delete();
        }

        return redirect('prod-mwips')->with('success', 'Work In Progress recorded successfully');
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
        $page = 'Edit Material WIP';
        $title = 'Edit Material WIP';
        $mwip = MaterialWipStock::find(decrypt($id));
        $material = MaterialWip::find($mwip->material_wip_id);

        return view('production.wips.mwips.edit', compact('page', 'title', 'mwip', 'material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wip = MaterialWipStock::find(decrypt($id));
        if (!is_null($wip)) {
            $wip->date = $request['date'];
            $wip->produced = $request['produced'];
            $wip->used = $request['used'];
            $wip->dam_qty = $request['dam_qty'];
            $wip->closing_qty = ($wip->opening_qty+$wip->produced)-($wip->used+$wip->dam_qty);
            $wip->save();
        }

        return redirect('prod-mwips')->with('success', 'Work In Progress updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wip = MaterialWipStock::find(decrypt($id));
        if (!is_null($wip)) {
            $wip->delete();
        }
        return redirect('prod-mwips')->with('success', 'Work In Progress record deleted successfully');
    }

    public function deleteMultiple(Request $request)
    {
        foreach($request->input('ids') as $id) {
            $wip = MaterialWipStock::find($id);
            if (!is_null($wip)) {
                $wip->delete();
            }
        }

        return redirect('prod-mwips')->with('success', 'Work In Progress records deleted successfully');
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $wiptemps = MaterialWipStockTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($wiptemps as $key => $value) {
            $value->delete();
        }

        return redirect('prod-mwips')->with('success', 'Work In Progress records cancelled successfully');
    }
}