<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PragmaRX\Countries\Package\Countries;
use File;
use Session;
use Auth;
use Log;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserTheme;
use App\Models\BusinessType;
use App\Models\BusinessSubType;
use App\Models\SubscriptionType;
use App\Models\BankDetail;
use App\Models\Employee;


class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'My Profile';
        $title = 'My Profile';
        $title_sw = 'Wsifu Wangu';
        // $user = Auth::user();
        // if ($user->phone == '0772119707') {
        //     $role = $user->roles()->first();
        //     Log::info($role);
        //     $role->syncPermissions();
        //     $permissions = Permission::all();
        //     foreach ($permissions as $key => $value) {
        //         $role->givePermissionTo($value);
        //     }
        // }
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
            $users = $company->users()->get();
            $shops = $company->shops()->get();
            $roles = $company->roles()->select('roles.id as id', 'name', 'display_name')->get();
            
            return view('account.index', compact('page', 'title', 'title_sw', 'users', 'shops', 'roles'));
        }else{
            return redirect('/');
        }
    }

    public function usersAndRoles(Request $request)
    {
        $page = 'Users';
        $title = 'Users & Roles';
        $title_sw = 'Akaunti Yangu';
        $company = Company::find(Session::get('company_id'));
        if (!empty($request['company_id'])) {
            $company = Company::find($request['company_id']);
        }
        if (!is_null($company)) {
            $users = $company->users()->where('is_active', true)->get();

            $inactiveusers = $company->users()->where('is_active', false)->get();
            $roles = $company->roles()->select('roles.id as id', 'name', 'display_name')->get();
            
            return view('account.users.index', compact('page', 'title', 'title_sw', 'company', 'users', 'inactiveusers', 'roles'));
        }else{
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'New User';
        $title = 'Add New User';
        $title_sw = 'Ongeza Mtumiaji Mpya';
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
                
            $roles = $company->roles()->select('id', 'display_name')->get();
            $defaultpages = array(
                'user-profile' => 'My Account',
                'home' => 'Dashboard',
                'sale-orders' => 'Sales Orders',
                'pos' => 'Point of Sale',
                'an-sales' => 'Invoices',
                'transfer-orders' => 'Stock Transfers',
                'products' => 'Products',
                'purchase-orders' => 'Purchase Orders',
                'petty-cash' => 'Petty Cash',
                'expenses' => 'Expenses',
                'hr-dash' => 'HR Dashboard',
            );


            $employees = Employee::where('company_id', $company->id)->select('id', 'fname', 'lname')->get();

            return view('account.users.create', compact('page', 'title', 'title_sw', 'roles', 'defaultpages', 'employees'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $amdin = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $user = User::where('phone', $request['phone'])->first();
        if (!is_null($user)) {
            $company->users()->attach($user);
            $message = 'User was added successfully';
            return redirect('users-and-roles')->with('success', $message);
        } else {
            $country_code = '255';
            if (!empty($request['phone_country'])) {
                $country_code = $request['phone_country'];
            }
            $user = new User();
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->phone = $request['phone'];
            $user->country_code = $country_code;
            $user->dial_code = $request['dial_code'];
            $user->country = $request['country'];
            $user->default_page = $request['default_page'];
            $user->save();

            $company->users()->attach($user, ['is_default' => true]);

            $role = Role::find($request['role']);
            $user->assignRole($role);
                        
            $usertheme = new UserTheme();
            $usertheme->user_id = $user->id;
            $usertheme->header_color = '#2874a6';
            $usertheme->sidebar_background = 'sidebarcolor1';
            $usertheme->save();
            $message = 'User was added successfully';
            return redirect('users-and-roles')->with('success', $message);
        }
    }

    public function createUser(Request $request)
    {
        $employee = Employee::find($request['employee_id']);
        if (!is_null($employee)) {    
            $company = Company::find(Session::get('company_id'));
            $user = User::where('phone', $employee->mobile)->first();
            if (!is_null($user)) {
                $company->users()->attach($user);
                $message = 'User was added successfully';
                return redirect('users-and-roles')->with('success', $message);
            }else{
                $country_code = '255';
                $user = new User();
                $user->email = $employee->email;
                $user->password = bcrypt('12345678');
                $user->first_name = $employee->fname;
                $user->last_name = $employee->lname;
                $user->phone = $employee->mobile;
                $user->country = 'Tanzania';
                $user->default_page = $request['default_page'];
                $user->save();

                $company->users()->attach($user, ['is_default' => true]);

                $role = Role::find($request['role']);
                $user->assignRole($role);
                            
                $usertheme = new UserTheme();
                $usertheme->user_id = $user->id;
                $usertheme->header_color = '#2874a6';
                $usertheme->sidebar_background = 'sidebarcolor1';
                $usertheme->save();
                $message = 'User was added successfully';
                return redirect('users-and-roles')->with('success', $message);
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
        $page = 'User information';
        $title = 'User information';
        $title_sw = 'Taarifa za Mtumiaji';
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find(decrypt($id));
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
            $roles = $company->roles()->select('roles.id as id', 'display_name')->get();
            $urole = $user->roles()->first();

            $user_permissions = null;
            if (!is_null($urole)) {   
                $user_permissions = $urole->permissions;
            }
            $shops = Auth::user()->shops()->join('companies', 'companies.id', '=', 'shops.company_id')->select('shops.id as id', 'shops.name as name', 'is_warehouse', 'companies.name as company', )->get();
            $usershops = array();
            foreach ($shops as $key => $shop) {
                $ushop = $user->shops()->where('shop_id', $shop->id)->first();
                if (!is_null($ushop)) {
                    array_push($usershops, ['id' => $shop->id, 'name' => $shop->name, 'company' => $shop->company, 'access' => true, 'is_default' => $ushop->pivot->is_default]);
                }else{
                    array_push($usershops, ['id' => $shop->id, 'name' => $shop->name, 'company' => $shop->company, 'access' => false, 'is_default' => false]);
                }    
            }

            // return $roles;
            return view('account.users.show', compact('page', 'title', 'title_sw', 'user', 'user_permissions', 'shops', 'usershops', 'roles', 'urole'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit User Info';
        $title = 'Edit User Info';
        $title_sw = 'Hariri Taarifa za Mtumiaji';
        $user = User::find(decrypt($id));
        $defaultpages = array(
            'home' => 'Dashboard',
            'sale-orders' => 'Sales Orders',
            'pos' => 'Point of Sale',
            'an-sales' => 'Invoices',
            'products' => 'Products',
            'transfer-orders' => 'Stock Transfers',
            'purchase-orders' => 'Purchase Orders',
            'petty-cash' => 'Petty Cash',
            'expenses' => 'Expenses',
            'hr-dash' => 'HR Dashboard',
        );
        return view('account.users.edit', compact('page', 'title', 'title_sw', 'user','defaultpages'));
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
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->default_page = $request['default_page'];
        $location = null;
        if ($request->hasFile('photo')) {
            //  Let's do everything here
            if ($request->file('photo')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $photo_path = storage_path('/public/'.$user->user_photo);
                if (File::exists($photo_path)) {
                    unlink($photo_path);
                }

                $extension = $request->photo->extension();
                $request->photo->storeAs('/public/photos', $user->id.'_usr.'.$extension);
                $location = 'photos/'.$user->id.'_usr.'.$extension;
            }
        }else{
            $location = $user->user_photo;
        }
        $user->user_photo = $location;
        $user->save();

        $message = 'Your information updated successfully';
        return redirect('users-and-roles')->with('success', $message);
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
        if (!is_null($user)) {
            $shops = $user->shops()->get();
            foreach ($shops as $key => $shop) {
                $shop->users()->detach($user);
            }

            $user->is_active = false;
            $user->save();

            $message = 'User ' . $user->name . ' was successfully deactivated from accessing the system';
            return redirect('users-and-roles')->with('success', $message);
        }else{
            return redirect('users-and-roles')->with('info', 'User not Found');
        }
    }

    public function activateUser($id)
    {
        $user = User::find(decrypt($id));
        if ($user) {
            $user->is_active = true;
            $user->save();

            $message = 'User ' . $user->name . ' was successfully activated to access the system';
            return redirect('users-and-roles')->with('success', $message);            
        }else{
            return redirect('users-and-roles')->with('info', 'User not Found');
        }
    }

    public function removeUser($id)
    {
        $company = Company::find(Session::get('company_id'));
        $user = User::find(decrypt($id));
        if ($user) {
            $company->users()->detach($user);
            $message = 'User ' . $user->name . ' was successfully Removed';
            return redirect('users-and-roles')->with('success', $message);            
        }else{
            return redirect('users-and-roles')->with('info', 'User not Found');
        }
    }

    public function changePassForm()
    {
        $page = "Change Password";
        return view('account.users.change-pass', compact('page'));
    }


    public function changePass(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (Hash::check($request['curr_password'], $user->password)) {

            $this->passvalidator($request->all())->validate();

            $user->password = bcrypt($request['password']);
            $user->save();

            return redirect('login')->with('success', 'Hi ' . $user->name . ', Your Password has been reseted successfuly. Login now');
        } else {
            return redirect()->back()->with('error', 'Your current password does not matches with the password you provided. Please try again.');
        }
    }


    public function resetUserPassForm($id)
    {
        $page = "Change Password";
        $user = User::find(decrypt($id));
        $user->password = bcrypt('12345678');
        $user->save();

        return redirect()->route('user-profile.show', encrypt($user->id))->with('success', 'User Password reseted successfuly');
    }


    public function resetUserPass(Request $request)
    {
        $user = User::find($request->id);
        
        $this->passvalidator($request->all())->validate();

        return redirect('login')->with('success', 'Hi ' . $user->name . ', User Password has been reseted successfuly. Login now');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function passvalidator(array $data)
    {
        return Validator::make($data, [
            'curr_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }



    public function assignBusiness(Request $request)
    {
        $shop = Shop::find($request['shop_id']);

        if (!is_null($shop)) {
            $user = User::find($request['user_id']);
            $attshop = $user->shops()->where('shop_id', $shop->id)->first();
            if (is_null($attshop)) {
                $ushops = $user->shops()->count();
                if ($ushops > 0) {
                    $user->shops()->attach($shop);
                }else{
                    $user->shops()->attach($shop, ['is_default' => true]);
                }

                $company = Company::find($shop->company_id);
                $compuser = $company->users()->where('user_id', $user->id)->first();
                if (is_null($compuser)) {
                    $company->users()->attach($user);
                }
                return redirect()->back()->with('success', 'User attached to ' . $shop->name . ' successfully');
            } else {
                return redirect()->back()->with('info', 'User already attached to ' . $shop->name);
            }
        } else {
            return redirect()->back();
        }
    }

    public function detachBusiness(Request $request)
    {
        $shop = Shop::find($request['shop_id']);
        if (!is_null($shop)) {
            $user = User::find($request['user_id']);
            $user->shops()->detach($shop);
            $attshop = $user->shops()->where('is_default', false)->first();
            if (!is_null($attshop)) {
                $attshop->pivot->is_default = true;
                $attshop->pivot->save();
            }
            return redirect()->back()->with('success', 'User detached from ' . $shop->name . ' successfully');
        } else {
            return redirect()->back();
        }
    }


    //Assign Role
    public function assignUserRole(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = User::find($request['user_id']);
        $currroles = $user->roles()->get();
        if (!is_null($currroles)) {
            foreach ($currroles as $key => $role) {
                $user->removeRole($role);
            }
        }
        if (!empty($request['role'])) {
            $role = Role::find($request['role']);
            if (!is_null($role)) {
                $user->assignRole($role);
            }
        }
        // return $request;
        return redirect()->back()->with('success', 'User Role was Changed successfully');
    }
    

    public function viewReceipt($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $page = 'Receipt';
        $title = 'Service Payment Receipt';
        $title_sw = 'Risiti ya malipo ya Huduma';
        $receipt = Payment::find($id);
        return view('account.receipt', compact('title', 'title_sw', 'page', 'receipt', 'shop', 'user'));
    }

    public function getBSubTypes(Request $request)
    {
        $bsubtypes = BusinessSubType::where('business_type_id', $request['business_type_id'])->get();

        return response()->json($bsubtypes->toArray());
    }

    public function changeTheme(Request $request)
    {
        $usertheme = UserTheme::where('user_id', Auth::user()->id)->first();
        if (!is_null($usertheme)) {
            if (!empty($request['theme_style'])) {
                $usertheme->theme_style = $request['theme_style'];
                $usertheme->save();
            }elseif (!empty($request['header_color'])){
                $usertheme->header_color = $request['header_color'];
                $usertheme->save();
            }elseif (!empty($request['sidebar_background'])) {
                $usertheme->sidebar_background = $request['sidebar_background'];
                $usertheme->save();
            }

            Session::put('theme_style', $usertheme->theme_style);
            Session::put('headercolor', $usertheme->header_color);
            Session::put('sidebarcolor', $usertheme->sidebar_background);
        }else{
            $usertheme = new UserTheme();
            $usertheme->user_id = Auth::user()->id;
            if (!empty($request['theme_style'])) {
                $usertheme->theme_style = $request['theme_style'];
                $usertheme->save();
            }elseif (!empty($request['header_color'])){
                $usertheme->header_color = $request['header_color'];
                $usertheme->save();
            }elseif (!empty($request['sidebar_background'])) {
                $usertheme->sidebar_background = $request['sidebar_background'];
                $usertheme->save();
            }

            Session::put('theme_style', $usertheme->theme_style);
            Session::put('headercolor', $usertheme->header_color);
            Session::put('sidebarcolor', $usertheme->sidebar_background);
        }

        return response()->json(['status' => 'OK']);
    }
}
