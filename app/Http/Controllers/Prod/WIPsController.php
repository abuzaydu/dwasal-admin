<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Session;
use Auth;
use App\Models\Shop;
use App\Models\User;
use App\Models\Wip;
use App\Models\Product;
use App\Models\WipTemp;

class WIPsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page ='Work In Progress';
        $title = 'Work In Progress';

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
        $wips = Wip::where('shop_id', $shop->id)->whereBetween('date', [$start, $end])->join('products', 'products.id', '=', 'wips.product_id')->select('wips.id as id', 'name', 'date', 'bf_balance', 'produced', 'finished_qty', 'wip_damage', 'closing_qty')->get();
        // return $wips;

        return view('production.wips.index', compact('page', 'title', 'wips','is_post_query', 'start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Work In Progress';
        $title = 'New Work In Progress';
        $now = Carbon::now();
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        // return $yesterday;
        $date = $now->format('Y-m-d');
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $products = $shop->products()->select('id', 'name')->get();
        foreach ($products as $key => $product) {
            $bf_balance = 0;
            $lastwip = Wip::where('shop_id', $shop->id)->where('product_id', $product->id)->where('date', $yesterday)->first();
            if (!is_null($lastwip)) {
                $bf_balance = $lastwip->closing_qty;
            }
            $wiptemp = WipTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->where('product_id', $product->id)->first();
            if (is_null($wiptemp)) {
                $wiptemp = new WipTemp();
                $wiptemp->shop_id = $shop->id;
                $wiptemp->user_id = $user->id;
                $wiptemp->product_id = $product->id;
                $wiptemp->date = $date;
                $wiptemp->bf_balance = $bf_balance;
                $wiptemp->save();
            }
        }

        return view('production.wips.create', compact('page', 'title', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $wiptemps = WipTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($wiptemps as $key => $temp) {
            $wip = new Wip();
            $wip->shop_id = $temp->shop_id;
            $wip->user_id = $temp->user_id;
            $wip->product_id = $temp->product_id;
            $wip->date = $request['date'];
            $wip->bf_balance = $temp->bf_balance;
            $wip->produced = $temp->produced;
            $wip->finished_qty = $temp->finished_qty;
            $wip->wip_damage = $temp->wip_damage;
            $wip->closing_qty = ($wip->bf_balance+$wip->produced)-($wip->finished_qty+$wip->wip_damage);
            $wip->save();

            $temp->delete();
        }

        return redirect('prod-wips')->with('success', 'Work In Progress recorded successfully');
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
        $page = 'Edit Work  In Progress';
        $title = 'Edit Work In Progress';
        $wip = Wip::find(decrypt($id));
        $product = Product::find($wip->product_id);

        return view('production.wips.edit', compact('page', 'title', 'wip', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wip = Wip::find(decrypt($id));
        if (!is_null($wip)) {
            $wip->date = $request['date'];
            $wip->produced = $request['produced'];
            $wip->finished_qty = $request['finished_qty'];
            $wip->wip_damage = $request['wip_damage'];
            $wip->closing_qty = ($wip->bf_balance+$wip->produced)-($wip->finished_qty+$wip->wip_damage);
            $wip->save();
        }

        return redirect('prod-wips')->with('success', 'Work In Progress updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wip = Wip::find(decrypt($id));
        if (!is_null($wip)) {
            $wip->delete();
        }
        return redirect('prod-wips')->with('success', 'Work In Progress record deleted successfully');
    }

    public function deleteMultiple(Request $request)
    {
        foreach($request->input('ids') as $id) {
            $wip = Wip::find($id);
            if (!is_null($wip)) {
                $wip->delete();
            }
        }

        return redirect('prod-wips')->with('success', 'Work In Progress records deleted successfully');
    }

    public function cancel()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $wiptemps = WipTemp::where('shop_id', $shop->id)->where('user_id', $user->id)->get();
        foreach ($wiptemps as $key => $value) {
            $value->delete();
        }

        return redirect('prod-wips')->with('success', 'Work In Progress records cancelled successfully');
    }
}
