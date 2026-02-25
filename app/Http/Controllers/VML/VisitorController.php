<?php

namespace App\Http\Controllers\VML;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\FcmNotification;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

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
            $company = Company::find(Session::get('company_id'));
        $departments = Department::where('company_id', $company->id)->select('id', 'name')->get();
        $employees = $company->users()->select('id', 'first_name as fname', 'last_name as lname')->get();
        $visitorsLogs = Visitor::whereDate('created_at', Carbon::today())
            ->latest()
            ->limit(10)
            ->get();
    
    $period = $request->get('period', 'today');

    // Build the date range based on period
    $dateRange = match($period) {
        'weekly'  => [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()],
        'monthly' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        'yearly'  => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
        'total'   => [null, null], // no date filter
        default   => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()], // 'today'
    };

    // Helper closure to apply date range
    $applyRange = function ($query) use ($dateRange) {
        if ($dateRange[0] && $dateRange[1]) {
            $query->whereBetween('created_at', $dateRange);
        }
        return $query;
    };

    $totalVisitors    = $applyRange(Visitor::query())->count();
    $pendingVisitors  = $applyRange(Visitor::whereIn('status', ['Awaiting Host permission', 'Permission Granted']))->count();
    $checkedinVisitors = $applyRange(Visitor::where('status', 'Checked In'))->count();
    $checkedoutVisitors  = $applyRange(Visitor::where('status', 'Checked Out'))->count(); // reuse same period

      

        return view('vml.index', compact('page', 'visitorsLogs','departments', 'employees', 'totalVisitors', 'pendingVisitors', 'checkedinVisitors', 'checkedoutVisitors','period'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Visitors';
        $company = Company::find(Session::get('company_id'));
        $departments = Department::where('company_id', $company->id)->select('id', 'name')->get();
        $employees = $company->users()->select('id', 'first_name as fname', 'last_name as lname')->get();
        $visitors = Visitor::where('visitors.shop_id', Session::get('shop_id'))
        ->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'visitors.name as name', 'visitors.mobile as mobile',  'visitors.email as email', 'visitors.address as address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'first_name as fname', 'last_name as lname')
        ->orderBy('visitors.created_at', 'desc')->get();
        $visitorids = array(
            ['name' => 'NIL'],
            ['name' => 'NIN'],
            ['name' => 'Driving License'],
            ['name' => 'Voters Number'],
            ['name' => 'Passport']
        );
        $badges = $company->badges()->where('status', 'available')->get();

        return view('vml.visitors.index', compact('page', 'visitors', 'employees', 'departments', 'visitorids', 'badges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

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
            $visitor->came_in_with = $request['came_in_with'];
            $visitor->save();
        Badge::where('badge_number', $request['badge_no'])
        ->update(['status' => 'in_use']);
       
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
        $visitor = Visitor::where('visitors.id', decrypt($id))->join('users', 'users.id', '=', 'visitors.host_id')->select('visitors.id as id', 'user_id', 'name', 'mobile',  'visitors.email as email', 'address', 'id_type', 'id_number', 'visitor_photo', 'badge_no', 'purpose', 'time_in', 'time_out', 'status', 'is_granted', 'came_in_with', 'came_out_with', 'first_name as fname', 'last_name as lname', 'visitors.created_at as created_at')->first();
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
    try {
        $visitor = Visitor::findOrFail(decrypt($id));

        $user = User::find($visitor->user_id);

        // Update visitor status
        $visitor->update([
            'is_granted' => true,
            'status' => 'Permission Granted'
        ]);

        // Prepare professional notification message
        if ($user) {
            $notificationData = [
                'title' => 'Visitor Entry Approved',
                'body' => 'Entry permission has been granted for visitor ' . $visitor->name . '. Kindly allow access at the entrance.',
                'data' => [
                    'visitor_id' => $visitor->id,
                    'type' => 'visitor_permission'
                ],
            ];

            $user->notify(new FcmNotification($notificationData));
        }

        return redirect()
            ->route('visitors.show', encrypt($visitor->id))
            ->with('success', 'Visitor permission granted successfully.');

    } catch (\Exception $e) {

        return redirect()
            ->back()
            ->with('error', 'An error occurred while granting permission.');
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = 'Edi visitor Details';
        $visitor = Visitor::find(decrypt($id));
        $company = Company::find(Session::get('company_id'));
        $employees = $company->users()->select('id', 'first_name as fname', 'last_name as lname')->get();
        $departments = Department::where('company_id', Session::get('company_id'))->get();
        $visitorids = array(
            ['name' => 'NIL'],
            ['name' => 'NIN'],
            ['name' => 'Driving License'],
            ['name' => 'Voters Number'],
            ['name' => 'Passport']
        );

        return view('vml.visitors.edit', compact('page', 'visitor', 'employees', 'departments', 'visitorids'));
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
            $visitor->came_in_with = $request['came_in_with'];
            $visitor->came_out_with = $request['came_out_with'];
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
