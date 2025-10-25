<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Auth;
use Session;
use App\Models\Company;
use App\Models\User;
use App\Models\Feature;
use App\Jobs\AssignRolePermissionsJob;

class CompanyRoleController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Role';
        $currPermissions = array();
        $features = Feature::select('id', 'name')->get();
        $fpermissions = array();
        foreach ($features as $key => $f) {
            $permissions = Permission::where('feature_id', $f->id)->select('id', 'name', 'display_name')->get();
            array_push($fpermissions, ['id' => $f->id, 'name' => $f->name, 'permissions' => $permissions->toArray()]);
        }
        return view('account.roles.create', compact('page', 'fpermissions', 'currPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        if (!is_null($company)) {
            $role = Role::where('name', $request['name'].'_'.$company->id)->first();
            if (is_null($role)) {
                $role = new Role();
                $role->name = $request['name'].'_'.$company->id;
                $role->display_name = $request['name'];
                $role->save();
            }
            $checkrole = $company->roles()->where('role_id', $role->id)->first();
            if (is_null($checkrole)) {
                $company->roles()->attach($role);
            }

            if (is_array($request['permission']) || is_object($request['permission'])) {
                foreach ($request['permission'] as $perm) {
                    $role->givePermissionTo($perm);
                }
                return redirect('users-and-roles')->with('success', 'Role Permissions updated successfully');
            }else{
                return redirect()->back()->with('info', 'No Permissions selected. Please select at least one permission');
            }
        }
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
        $page = 'Edit Role';
        $role = Role::find(decrypt($id));

        $currPermissions = $role->permissions()->pluck('permission_id')->toArray();
        // return $currPermissions;
        $features = Feature::select('id', 'name')->get();
        $fpermissions = array();
        foreach ($features as $key => $f) {
            $permissions = Permission::where('feature_id', $f->id)->select('id', 'name', 'display_name')->get();
            array_push($fpermissions, ['id' => $f->id, 'name' => $f->name, 'permissions' => $permissions->toArray()]);
        }
        // return $fpermissions;

        return view('account.roles.edit', compact('page', 'role', 'fpermissions', 'currPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // ini_set('max_execution_time', 600);
        $user = Auth::user();
        $company = Company::find(Session::get('company_id'));
        $role = Role::find(decrypt($id));
        $roleexist = Role::where('name', $request['name'].'_'.$company->id)->where('id', '!=', $role->id)->first();
        if (!is_null($roleexist)) {
            return redirect()->back()->with('info', 'Role with same name already exists');
        }else{
            $role->name = $request['name'].'_'.$company->id;
            $role->display_name = $request['name'];
            $role->save();

               
            if (is_array($request['permission']) || is_object($request['permission'])) {
                $role->syncPermissions($request['permission']);
                
                return redirect('users-and-roles')->with('success', 'Role Permissions updated successfully');
            }else{
                return redirect()->back()->with('info', 'No Permissions updated successfully');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find(decrypt($id));
        if (!is_null($role)) {
            $users = User::role($role->name)->count();
            if ($users == 0) {
                $role->syncPermissions();
                $company->roles()->detach($role);
                $role->delete();
                return redirect()->back()->with('success', 'Role removed successfully');
            }else{
                return redirect()->back()->with('info', 'Role has User assigned can not be deleted');
            }
        }else{
            return redirect()->back()->with('error', 'Role not Found');
        }
    }
}