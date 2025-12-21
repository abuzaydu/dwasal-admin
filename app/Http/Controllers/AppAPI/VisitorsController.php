<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use \Carbon\Carbon;
use App\Models\Visitor;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Department;
use Log;

class VisitorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitors = Visitor::where('visitors.shop_id', $request['shop_id'])->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname')->get();
            // Log::info($visitors);
        return response()->json($visitors);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $company = Company::find($request['company_id']);
        $departments = Department::where('company_id', $request['company_id'])->select('id', 'name')->get();
        $comemployees = $company->users()->select('id', 'first_name as fname', 'last_name as lname')->get();
        $employees = [['id' => 0, 'name' => 'Select Host ']];
        foreach ($comemployees as $key => $value) {
            array_push($employees, ['id' => $value->id, 'name' => $value->fname.' '.$value->lname]);
        }
        // Log::info($employees);
        return response()->json(['statuscode' => 200, 'employees' => $employees, 'departments' => $departments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log::info($request);
        $visitor = Visitor::where('shop_id', $request['shop_id'])->where('mobile', $request['mobile'])->whereNull('time_out')->first();
        if (is_null($visitor)) {
            $visitor = new Visitor();
            $visitor->shop_id = $request['shop_id'];
            $visitor->user_id = $request['user_id'];
            $visitor->host_id = $request['host_id'];
            $visitor->name = $request['name'];
            $visitor->mobile = $request['mobile'];
            $visitor->email = $request['email'];
            $visitor->address = $request['address'];
            $visitor->id_type = $request['id_type'];
            $visitor->id_number = $request['id_number'];
            $visitor->badge_no = $request['badge_no'];
            $visitor->purpose = $request['purpose'];
            $visitor->status = 'Awaiting Host permission';
            $visitor->save();
        }
        // Log::info($visitor);
        return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Initialized successfully']);
    }

    public function visitorPhoto(Request $request)
    {
        $visitor = Visitor::find($request['visitor_id']);
        if (!is_null($visitor)) {
            $location = null;
            if ($request->hasFile('photo')) {
                //  Let's do everything here
                if ($request->file('photo')->isValid()) {
                    //
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,png|max:1014',
                    ]);

                    $photo_path = storage_path('/visitors/'.$visitor->visitor_photo);
                    if (File::exists($photo_path)) {
                        unlink($photo_path);
                    }

                    $extension = $request->photo->extension();
                    $request->photo->storeAs('/visitors', $visitor->id.'.'.$extension);
                    $location = $visitor->id.'.'.$extension;
                }
            }else{
                $location = $visitor->visitor_photo;
            }
            $visitor->visitor_photo = $location;
            $visitor->save();

            return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Photo added successfully']);

        }else{
            return response()->json(['statuscode' => 400, 'message' => 'Visitor not found']);
        }
    }

    public function visitorCheckIn(Request $request)
    {
        $visitor = Visitor::find($request['visitor_id']);
        if(!is_null($visitor)){
            $visitor->status = 'Checked In';
            $visitor->time_in = Carbon::now();
            $visitor->save();

            return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Checked In successfully']);

        }else{
            return response()->json(['statuscode' => 400, 'message' => 'Visitor not found']);
        }
    }

    public function visitorCheckOut(Request $request)
    {
        $visitor = Visitor::find($request['visitor_id']);
        if(!is_null($visitor)){
            $visitor->status = 'Checked Out';
            $visitor->time_out = Carbon::now();
            $visitor->save();
            
            return response()->json(['statusCode' => 200, 'visitor' => $visitor, 'message' => 'Visitor Checked Out successfully']);

        }else{
            return response()->json(['statuscode' => 400, 'message' => 'Visitor not found']);
        }
    }

    public function myVisitors(Request $request)
    {
        $visitors = Visitor::where('host_id', $request['host_id'])->where('visitors.shop_id', $request['shop_id'])->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname')->get();
            // Log::info($visitors);
        return response()->json($visitors);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
