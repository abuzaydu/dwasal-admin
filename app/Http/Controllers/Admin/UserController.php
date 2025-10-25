<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use \Carbon\Carbon;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;
use App\Models\CustomPassReset;
use App\Models\SocialMediaAgent;
use Cache;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Illuminate\Support\Facades\Crypt;
use Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Users';
        $title = 'Users';
        $status1 = 'active';

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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $users = User::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
        if (!empty($request['search_key'])) {
            $users = User::where(\DB::raw('CONCAT_WS(" ", `first_name`, `last_name`, `phone`, `email`)'),'LIKE', '%'.$request->search_key.'%')->orderBy('created_at', 'desc')->get();
        }
        $roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        $staffs = null;
        $passcodes = CustomPassReset::orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.users.index', compact('page', 'title', 'users', 'roles', 'staffs', 'passcodes', 'is_post_query', 'start_date', 'end_date', 'duration', 'status1'));
    }

    public function exportUsers(Request $request){

        $page = 'Users';
        $title = 'Users';

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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }


        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $users = User::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.export-users', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date', 'duration', 'status2'));
    }

    public function passwordResets(Request $request){
        $page = 'Password Reset Requests';
        $title = 'Password Reset Requests';
        $status3 = 'active';
      
        $passcodes = CustomPassReset::orderBy('created_at', 'desc')->get();
        
        return view('admin.users.reset', compact('page', 'title', 'passcodes', 'status3'));
    }

    public function staffs(Request $request)
    {
        $page = 'Users';
        $title = 'Users';
        $status4 = 'active';
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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $staffs = User::orderBy('first_name', 'asc')->paginate(10)->withQueryString();
        return view('admin.users.staffs', compact('page', 'title', 'staffs', 'is_post_query', 'start_date', 'end_date', 'duration', 'status4'));
    }

    public function activeUsers()
    {
        $users = $this->onlineUsers();
        $page = 'Users';
        $title = 'Active Users';
        $start_date = null;
        $end_date = null;
        $is_post_query = false;
        // return $users;

        return view('admin.users.active', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date'));
    }

    public function guestUsers()
    {
        $guests = [];      // Get active guests within the last 48 hours

        $page = 'Users';
        $title = 'Guest Users';

        return view('admin.users.guests', compact('page', 'title', 'guests', 'is_post_query', 'start_date', 'end_date'));
    }

    public function shops(Request $request)
    {
        $page = 'Shops';
        $title = 'Registered Shops';

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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));
        $companies = Company::select('id', 'name')->get();
        $shops = [];
        $currcoid = null;
        if (!empty($request['company_id'])) {
            $currcoid = $request['company_id'];
            $shops = Shop::where('company_id', $currcoid)->join('subscription_types', 'subscription_types.id', '=', 'shops.subscription_type_id')->join('companies', 'companies.id', '=', 'shops.company_id')->select('shops.id as id', 'shops.name as name', 'companies.name as company', 'street', 'district', 'city', 'title', 'shops.created_at as created_at', 'is_warehouse')->get();
        }else {
            $shops = Shop::whereBetween('shops.created_at', [$start, $end])->join('subscription_types', 'subscription_types.id', '=', 'shops.subscription_type_id')->join('companies', 'companies.id', '=', 'shops.company_id')->select('shops.id as id', 'shops.name as name', 'companies.name as company', 'street', 'district', 'city', 'title', 'shops.created_at as created_at', 'is_warehouse')->get();
        }

        return view('admin.users.shops', compact('page', 'title', 'shops', 'companies', 'currcoid', 'is_post_query', 'start_date', 'end_date', 'duration'));

    }

    public function editShop($id)
    {
        $page = 'Edit Shop Details';
        $title = 'Edit Shop Details';
        $shop = Shop::find(decrypt($id));
        $company = Company::find($shop->company_id);

        return view('admin.users.edit-shop', compact('page', 'title', 'shop', 'company'));
    }

    public function updateShop(Request $request)
    {
        $shop = Shop::find($request['id']);
        $shop->parent_shop_id = $request['parent_shop_id'];
        $shop->save();

        return redirect('admin/shops')->with('success', 'Warehouse Parent shop updated successfully');
    }

    public function exportShops(Request $request){
        $page = 'Shops';
        $title = 'Registered Shops';

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
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' .date('d-m-Y', strtotime($start)). ' T0 ' .date('d-m-Y', strtotime($end));

        $usershops = User::join('shop_user', 'shop_user.user_id', '=', 'users.id')->where('is_owner', true)->join('shops', 'shops.id', '=', 'shop_user.shop_id')->whereBetween('shops.created_at', [$start, $end])->join('payments', 'payments.shop_id', '=', 'shops.id')->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.name as name', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')->get();

        return view('admin.users.export', compact('page', 'title', 'usershops', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    public function activeShops(Request $request)
    {
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';

        $shops = User::join('shop_user', 'shop_user.user_id', '=', 'users.id')->join('shops', 'shops.id', '=', 'shop_user.shop_id')->where('is_owner', true)->join('payments', 'payments.shop_id', '=', 'shops.id')->where('is_real', true)->where('is_expired', false)->select('users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'shops.id as id', 'shops.name as name', 'shops.street as street', 'shops.district as district', 'shops.city as city', 'shop_user.is_default as is_default', 'shops.created_at as created_at', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired')->groupBy('id')->orderBy('created_at', 'desc')->get();
        $page = 'Shops';
        $title = 'Active Shops';

        return view('admin.users.active-shops', compact('page', 'title', 'shops', 'is_post_query', 'start_date', 'end_date', 'duration'));

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = User::create([
                'ba_id' => 1,
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'country_code' => 'TZ'
            ]);

            if ($user) {
                $user->assignRole($request['role']);
            }

            $message = 'User was registered successfully!';
            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode = '1062') {
                $message = 'Ooops! Mobile number already used in our System.';
                return redirect()->back()->with('error', $message);
            }
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find(decrypt($id));
        $page = 'Assign User role';
        $title = 'User roles';
        $company = $user->companies()->first();
        $urole = $user->roles()->first();
        $roles = null;
        if (!is_null($company)) {
            $roles = $company->roles()->select('roles.id as id', 'name', 'display_name')->get();
        }else{
            $roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        }
        return view('admin.users.edit', compact('page', 'title', 'user', 'urole', 'roles'));
    }

    //Assign Role
    public function assignUserRole(Request $request)
    {
        $user = User::find($request['user_id']);
        $user->assignRole($request['role']);
        return redirect('admin/users');
    }

    //Detach Role
    public function detachUserRole(Request $request)
    {
        $user = User::find($request['user_id']);
        $user->removeRole($request['role']);
        return redirect('admin/users');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find(decrypt($id));
        $user->phone = $request['phone'];
        $user->save();
        
        $currroles = $user->roles()->get();
        if (!is_null($currroles)) {
            foreach ($currroles as $key => $role) {
                $user->removeRole($role);
            }
        }
        if (!empty($request['role'])) {
            $user->assignRole($request['role']);
        }

        return redirect('admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find(decrypt($id));
        $shops = $user->shops()->count();

        if ($shops == 0) {
            $user->delete();
            return redirect()->back()->with('success', 'User deleted completely because has no Business belongs to him/her');
        }else{

            return redirect()->back()->with('info', 'User can not be removed because has Business belonging to him/her');
        }
    }

   public function changeSubscriptionType($id)
   {
       $shop = Shop::find($id);
       if ($shop->subscription_type_id == 1) {
            $shop->subscription_type_id = 2;
            $shop->save();
       }else{
            $shop->subscription_type_id = 1;
            $shop->save();
       }

       return redirect()->back()->with('success', 'Subscription changed successful');
   }


    public function newRole(Request $request)
    {
        $role = Role::create([
            'name' => $request['name'],
            'guard_name' => 'web',
            'name' => $request['name'],
            'description' => $request['description']
        ]);

        return redirect()->back();
    }


    public function profile()
    {
        $page = 'Profile';
        $title = 'My Profile';
        return view('admin.users.profile', compact('page', 'title', 'users'));
    }


    public function updateProfile(Request $request)
    {
        $user = User::find($request['id']);
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->save();

        $message = 'Your information updated successfully';
        Alert::success('Success!', $message);
        return redirect()->back();
    }

    public function createAgentCode(Request $request)
    {
        $user = User::find($request['user_id']);
        if (!is_null($user)) {
            $smagent = SocialMediaAgent::where('user_id', $user->id)->first();
            if (!is_null($smagent)) {
                return redirect()->back()->with('info', 'User already assigned as Agent with Agent code : '.$smagent->agent_code);
            }else{
                $code = $this->generateCode(4);
                SocialMediaAgent::create([
                    'user_id' => $user->id,
                    'agent_code' => $code
                ]);
            }
        }
        return redirect()->back()->with('success', 'Agent code was successfully created');
    }

    public function generateCode($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;

    }

    public function agentsCustomers(Request $request)
    {
        $page = 'Users';
        $title = 'Customers from Agents';

        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;            
        $end_date = null;
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }else{
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }
        
        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';

        $users = AgentCustomer::join('users', 'users.id', '=', 'agent_customers.user_id')->get();
        $agents = User::role('agent')->get();

        return view('admin.users.customers-by-agents', compact('page', 'title', 'users', 'is_post_query', 'start_date', 'end_date', 'duration', 'agents'));
    }

    public function clearResetCodes()
    {
        CustomPassReset::truncate();
        return redirect('admin/users')->with('success', 'Record were removed successfully');
    }

    public static function onlineUsers() {
        // Get the array of users
        $users = Cache::get('online-users');
        if(!$users) return null;
        
        // Add the array to a collection so you can pluck the IDs
        $onlineUsers = collect($users);
        // Get all users by ID from the DB (1 very quick query)
        $dbUsers = User::find($onlineUsers->pluck('id')->toArray());
        
        // Prepare the return array
        $displayUsers = [];

        // Iterate over the retrieved DB users
        foreach ($dbUsers as $user){
            // Get the same user as this iteration from the cache
            // so that we can check the last activity.
            // firstWhere() is a Laravel collection method.
            $onlineUser = $onlineUsers->firstWhere('id', $user['id']) ;
            // Append the data to the return array
            $displayUsers[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                // This Bool operation below, checks if the last activity
                // is older than 3 minutes and returns true or false,
                // so that if it's true you can change the status color to orange.
                'away' => $onlineUser['last_activity_at'] < now()->subMinutes(3),
            ];
        }
        return collect($displayUsers);
    }
}
