<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Part;
use App\Models\PartCategory;
use App\Models\PartLocation;
use App\Models\PartPurchaseItem;
use App\Models\PartUsageItem;
use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PartsController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {        
        $page = 'Parts Management';
        $company = Company::find(Session::get('company_id'));
        $parts = Part::where('company_id', $company->id)->get();
        $partcategories = PartCategory::where('company_id', $company->id)->get();
        $partlocations = PartLocation::where('company_id', $company->id)->get();

        return view('vms.parts.index', compact('page', 'parts', 'partcategories', 'partlocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $part = Part::where('company_id', Session::get('company_id'))->where('part_name', $request->part_name)->first();
        if (is_null($part)) {
            $part = new Part();
            $part->company_id = Session::get('company_id');
            $part->part_category_id = $request->part_category_id;
            $part->part_location_id = $request->part_location_id;
            $part->part_no = $request->part_no;
            $part->part_name = $request->part_name;
            $part->uom = $request->uom;
            $part->av_qty = $request->av_qty;
            $part->description = $request->description;
            $part->remarks = $request->remarks;
            $part->save();

            return redirect('parts')->with('success', 'Part added successfully');
        }else{
            return redirect('parts')->with('info', 'Part already added');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Part Details';
        $part = Part::where('parts.id', decrypt($id))->join('part_categories', 'part_categories.id', '=', 'parts.part_category_id')->select('parts.id as id', 'part_location_id', 'part_no', 'part_name','uom', 'av_qty', 'active', 'description', 'name')->first();
        $location = PartLocation::find($part->part_location_id);
        $ppitems = PartPurchaseItem::where('part_id', $part->id)->get();
        $puitems = PartUsageItem::where('part_id', $part->id)->get();
        return view('vms.parts.show', compact('page', 'part', 'location', 'ppitems', 'puitems'));
    }


    public function autoSearch(Request $request)
    {
        if ($request->ajax()) {
            $company = Company::find(Session::get('company_id'));
            if (!empty($request->search_key) && strlen($request->search_key) >= 2) {
                $data = Part::where('company_id', $company->id)->where(DB::raw('CONCAT_WS(" ", `part_no`, `part_name`)'),'LIKE', '%'.$request->search_key.'%')->select('id', 'part_no', 'part_name')->get();

                return $data;
            }else{
                // $products = $shop->products()->select('product_id as id', 'product_code', 'name', 'in_stock')->take(15)->get();
                // return $products;
            }
        }
    }

    public function fetchPart(Request $request)
    {
        $company = Company::find(Session::get('company_id'));
        $part = Part::where('company_id', $company->id)->where('id', $request->part_id)->select('id', 'part_no', 'part_name')->first();

        return $part;
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Part Details';
        $part = Part::find(decrypt($id));
        $partcategories = PartCategory::where('company_id', $part->company_id)->get();
        $partlocations = PartLocation::where('company_id', $part->company_id)->get();

        return view('vms.parts.edit', compact('page', 'part', 'partcategories', 'partlocations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $part = Part::find(decrypt($id));
        if (!is_null($part)) {
            $part->part_category_id = $request->part_category_id;
            $part->part_location_id = $request->part_location_id;
            $part->part_no = $request->part_no;
            $part->part_name = $request->part_name;
            $part->uom = $request->uom;
            $part->av_qty = $request->av_qty;
            $part->description = $request->description;
            $part->remarks = $request->remarks;
            $part->status = $request->status;
            $part->save();

            return redirect('parts')->with('success', 'Part Details updated successfully');
        }else{
            return redirect('parts')->with('success', 'Part not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $part = Part::find(decrypt($id));
        if (!is_null($part)) {
            $part->delete();

            return redirect('parts')->with('success', 'Part Details deleted successfully');
        }else{
            return redirect('parts')->with('success', 'Part not Found');
        }

    }

    public function verifyAvQty($id)
    {
        $part = Part::find(decrypt($id));
        if (!is_null($part)) {
            $ppitems = PartPurchaseItem::where('part_id', $part->id)->sum('pp_qty');
            $puitems = PartUsageItem::where('part_id', $part->id)->sum('pu_qty');

            $part->av_qty = $ppitems-$puitems;
            $part->save();

            return redirect()->back()->with('success', 'Part '.$part->part_no.' - '.$part->part_name.' available Quantity updated successfully');
        }else{
            return redirect()->back()->with('error', 'Part not Found');
        }
    }
}
