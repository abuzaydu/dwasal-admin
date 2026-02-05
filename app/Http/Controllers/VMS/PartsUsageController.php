<?php

namespace App\Http\Controllers\VMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\PartUsage;
use App\Models\PartUsageItem;
use App\Models\Part;
use App\Models\Vehicle;

class PartsUsageController extends Controller
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
    public function index(Request $request)
    {
        $page = 'Parts Usage';
        $company = Company::find(Session::get('company_id'));
        $now = Carbon::now(); 
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $partusages = PartUsage::where('part_usages.company_id', $company->id)->whereBetween('pu_date', [$start, $end])->join('vehicles', 'vehicles.id', '=', 'part_usages.vehicle_id')->join('users', 'users.id', '=', 'part_usages.user_id')->select('part_usages.id as id', 'pu_date', 'pu_code', 'plate_no', 'vehicle_name', 'part_usages.status as status', 'is_approved', 'first_name', 'last_name', 'part_usages.updated_at as updated_at')->get();

        return view('vms.parts.usage.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'partusages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Parts Usage';
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $vehicles = Vehicle::where('company_id', $company->id)->select('id', 'plate_no', 'vehicle_name')->get();

        $pusage = PartUsage::where('company_id', $company->id)->where('user_id', $user->id)->where('status', 'Pending')->first();
        if (is_null($pusage)) {
            $pucode = $this->getAutoCode();
            $pusage = new PartUsage();
            $pusage->company_id = $company->id;
            $pusage->user_id = $user->id;
            $pusage->pu_date = Carbon::now();
            $pusage->pu_code = $pucode;
            $pusage->save();
        }

        $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->join('parts', 'parts.id', '=', 'part_usage_items.part_id')->join('part_categories', 'part_categories.id', '=', 'parts.part_category_id')->select('part_usage_items.id as id', 'part_id', 'name as category', 'part_no', 'part_name', 'uom', 'pu_qty')->get();
        // return $puitems;

        return view('vms.parts.usage.create', compact('page', 'vehicles', 'pusage', 'puitems'));
    }


    public function getAutoCode()
    {
        $company = Company::find(Session::get('company_id'));
        $v = '';
        if(preg_match_all('/\b(\w)/',strtoupper($company->name),$m)) {
            // Log::info($m);
            $v = implode('',$m[1]); // $v is now SOQTU
        }
        $pusage = PartUsage::where('company_id', $company->id)->orderBy('id', 'desc')->first();
        if (!is_null($pusage)) {
            $last = str_replace($v.'/PU-', '', $pusage->code);
            $lastcode = (int)$last;
            // Log::info($last);
            $id = $v.'/PU-'.sprintf('%03d', $lastcode+1);
            return $id;   
        }else{
            $id = $v.'/PU-'.sprintf('%03d', 1);
            return $id; 
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pusage = PartUsage::find($request['part_usage_id']);
        if (!is_null($pusage)) {
            $pusage->vehicle_id = $request['vehicle_id'];
            $pusage->pu_date = $request['pu_date'];
            $pusage->remarks = $request['remarks'];
            $pusage->status = 'Awaiting For Approval';
            $pusage->save();

            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->get();
            foreach ($puitems as $key => $item) {
                $item->date = $pusage->pu_date;
                $item->save();
            }

            return redirect('parts-usage')->with('success', 'Parts usage created successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'Parts Usage Lists';
        $pusage = PartUsage::where('part_usages.id', decrypt($id))->join('vehicles', 'vehicles.id', '=', 'part_usages.vehicle_id')->join('users', 'users.id', '=', 'part_usages.user_id')->select('part_usages.id as id', 'pu_date', 'pu_code', 'plate_no', 'vehicle_name', 'part_usages.status as status', 'first_name', 'last_name', 'part_usages.updated_at as updated_at', 'remarks', 'is_approved', 'approved_by', 'approved_at', 'reject_reason', 'closed_by', 'closed_at')->first();
        if (!is_null($pusage)) {
            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->join('parts', 'parts.id', '=', 'part_usage_items.part_id')->join('part_categories', 'part_categories.id', '=', 'parts.part_category_id')->select('part_usage_items.id as id', 'part_id', 'name as category', 'part_no', 'part_name', 'uom', 'pu_qty')->get();

            return view('vms.parts.usage.show', compact('page', 'pusage', 'puitems'));
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edit Parts Usage';
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $vehicles = Vehicle::where('company_id', $company->id)->select('id', 'plate_no', 'vehicle_name')->get();

        $pusage = PartUsage::find(decrypt($id));
        if (!is_null($pusage)) {
            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->join('parts', 'parts.id', '=', 'part_usage_items.part_id')->join('part_categories', 'part_categories.id', '=', 'parts.part_category_id')->select('part_usage_items.id as id', 'part_id', 'name as category', 'part_no', 'part_name', 'uom', 'pu_qty')->get();
            return view('vms.parts.usage.edit', compact('page', 'vehicles', 'pusage', 'puitems'));
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pusage = PartUsage::find(decrypt($id));
        if (!is_null($pusage)) {
            $pusage->vehicle_id = $request['vehicle_id'];
            $pusage->pu_date = $request['pu_date'];
            $pusage->remarks = $request['remarks'];
            $pusage->save();

            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->get();
            foreach ($puitems as $key => $item) {
                $item->date = $pusage->pu_date;
                $item->save();
            }

            return redirect('parts-usage')->with('success', 'Parts usage updated successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }

    public function rejectPURequest(Request $request)
    {
        $pusage = PartUsage::find($request['id']);
        if (!is_null($pusage)) {
            $pusage->reject_reason = $request['reject_reason'];
            $pusage->status = 'Rejected';
            $pusage->approved_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $pusage->approved_at = Carbon::now();
            $pusage->save();

            return redirect()->route('parts-usage.show', encrypt($pusage->id))->with('success', 'Parts usage Rejected successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }

    public function approvePURequest($id)
    {
        $pusage = PartUsage::find(decrypt($id));
        if (!is_null($pusage)) {
            $pusage->is_approved = true;
            $pusage->status = 'Approved';
            $pusage->approved_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $pusage->approved_at = Carbon::now();
            $pusage->save();

            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->get();
            foreach ($puitems as $key => $item) {
                $part = Part::find($item->part_id);
                
                $part->av_qty -= $item->pu_qty;
                $part->save();
            }

            return redirect()->route('parts-usage.show', encrypt($pusage->id))->with('success', 'Parts usage Approved successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }

    public function closePURequest($id)
    {
        $pusage = PartUsage::find(decrypt($id));
        if (!is_null($pusage)) {
            $pusage->status = 'Closed';
            $pusage->closed_by = Auth::user()->first_name.' '.Auth::user()->last_name;
            $pusage->closed_at = Carbon::now();
            $pusage->save();

            return redirect()->route('parts-usage.show', encrypt($pusage->id))->with('success', 'Parts usage Closed successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pusage = PartUsage::find(decrypt($id));
        if (!is_null($pusage)) {
            $puitems = PartUsageItem::where('part_usage_id', $pusage->id)->get();
            foreach ($puitems as $key => $value) {
                $value->delete();
            }

            $pusage->delete();
           return redirect('parts-usage')->with('success', 'Parts usage Deleted successfully');
        }else{
            return redirect()->back()->with('error', 'Part usage not Found');
        }
    }
}
