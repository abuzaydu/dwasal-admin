<?php

namespace App\Http\Controllers\VML;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Department;
use App\Models\Employee;
use App\Notifications\FcmNotification;

class VisitorController extends Controller
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

    public function dashboard(Request $request)
    {
        $page = 'Visitors Dashboard';
        return view('vml.index', compact('page'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Visitors';
        $departments = Department::where('company_id', Session::get('company_id'))->select('id', 'name')->get();
        $employees = Employee::where('company_id', Session::get('company_id'))->select('id', 'fname', 'lname')->get();
        $visitors = Visitor::where('visitors.shop_id', Session::get('shop_id'))->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname')->get();
        $visitorids = array(
            ['name' => 'NIL'],
            ['name' => 'NIN'],
            ['name' => 'Driving License'],
            ['name' => 'Voters Number'],
            ['name' => 'Passport']
        );

        return view('vml.visitors.index', compact('page', 'visitors', 'employees', 'departments', 'visitorids'));
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
        $visitor = Visitor::where('shop_id', Session::get('shop_id'))->where('mobile', $request['mobile'])->whereNull('time_out')->first();
        if (is_null($visitor)) {
            $user = Auth::user();
            $visitor = new visitor();
            $visitor->shop_id = Session::get('shop_id');
            $visitor->user_id = $user->id;
            $visitor->host_id = $request['host_id'];
            $visitor->department_id = $request['department_id'];
            $visitor->name = $request['name'];
            $visitor->mobile = $request['mobile'];
            $visitor->email = $request['email'];
            $visitor->address = $request['address'];
            $visitor->id_type = $request['id_type'];
            $visitor->id_number = $request['id_number'];
            $visitor->badge_no = $request['badge_no'];
            $visitor->purpose = $request['purpose'];
            $visitor->save();
        }

        return redirect('visitors')->with('success', 'New visitor added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = 'visitor Details';
        $codetype = 'QRCODE';
        $visitor = Visitor::where('visitors.id', decrypt($id))->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'user_id', 'name', 'mobile',  'visitors.email as email', 'address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'is_granted', 'first_name as fname', 'last_name as lname', 'visitors.created_at as created_at')->first();
        if (!is_null($visitor)) {
            $guard = User::find($visitor->user_id);
            // return $visitor;
            return view('vml.visitors.show', compact('page', 'visitor', 'guard', 'codetype'));
        }else{
            return redirect('visitors');
        }
    }

    public function grantPermission($id)
    {
        $visitor = Visitor::find(decrypt($id));
        if (!is_null($visitor)) {
            $user = User::find($visitor->user_id);
            $visitor->is_granted = true;
            $visitor->status = 'Permission Granted';
            $visitor->save();

            $notificationData = [
                'title' => 'Hello!',
                'body' => 'Etrance Permission granted for visitor '.$visitor->name.' Please Allow him/her in.',
                'data' => ['key' => 'value'], // Additional data if needed
            ];
            $user->notify(new FcmNotification($notificationData));
            return redirect()->route('visitors.show', encrypt($visitor->id))->with('success', 'Permission granted successfully');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edi visitor Details';
        $units = UnitMeasure::select('unit_name')->get();
        $visitor = Visitor::find(decrypt($id));
        $employees = Employee::where('company_id', Session::get('company_id'))->select('id', 'fname', 'lname')->get();
        $departments = Department::where('company_id', Session::get('company_id'))->get();
        return view('vml.visitors.edit', compact('page', 'visitor', 'units', 'vehtypes', 'ownerships', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $visitor = Visitor::find(decrypt($id));
        if (!is_null($visitor)) {
            $visitor->host_id = $request['host_id'];
            $visitor->department_id = $request['department_id'];
            $visitor->name = $request['name'];
            $visitor->mobile = $request['mobile'];
            $visitor->email = $request['email'];
            $visitor->address = $request['address'];
            $visitor->id_type = $request['id_type'];
            $visitor->id_number = $request['id_number'];
            $visitor->badge_no = $request['badge_no'];
            $visitor->purpose = $request['purpose'];
            $visitor->save();

            return redirect('visitors')->with('success', 'visitor Details updated successfully');
        }else {
            return redirect('visitors')->with('error', 'visitor not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $visitor = Visitor::find(decrypt($id));
        if (!is_null($visitor)) {
            $visitor->delete();
            return redirect('visitors')->with('success', 'visitor deleted successfully');
        }
    }

}
