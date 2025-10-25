<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Feature;
use App\Models\Shop;
use App\Models\User;
use Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }


    public function index()
    {
        $page = 'Roles';
        $title = 'Roles';
        $roles = Role::all();
        // foreach ($roles as $key => $role) {
        //     if ($role->display_name == 'Admin') {
        //         Log::info($role->name);
        //         $role->syncPermissions();
        //         $permissions = Permission::all();
        //         foreach ($permissions as $key => $value) {
        //             $role->givePermissionTo($value);
        //         }
        //     }
        // }
        return view('admin.roles.index', compact('page', 'title', 'roles'));
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
        $role = Role::create([
            'name' => $request['name'],
            'display_name' => $request['display_name'],
            'description' => $request['description']
        ]);
        return redirect('admin/roles');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = 'Role Permissions';
        $title = 'Role Permissions';
        $role = Role::find(decrypt($id));
        $currPermissions = $role->permissions()->pluck('permission_id')->toArray();
        // return $currPermissions;
        $features = Feature::select('id', 'name')->get();
        $fpermissions = array();
        foreach ($features as $key => $f) {
            $permissions = Permission::where('feature_id', $f->id)->select('id', 'name', 'display_name')->get();
            array_push($fpermissions, ['id' => $f->id, 'name' => $f->name, 'permissions' => $permissions->toArray()]);
        }

        return view('admin.roles.show', compact('page', 'role', 'fpermissions', 'currPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find(decrypt($id));
        $page = 'Edit Role';
        $title = 'Edit Role';

        return view('admin.roles.edit', compact('page', 'title', 'role'));
    }

    public function modify()
    {
        $roles = Role::all();

        foreach ($roles as $key => $r) {
            if ($r->display_name != 'Administrator') {
                Log::info($r->display_name.' should be assign to each shop');
                $shops = Shop::all();
                foreach ($shops as $key => $shop) {
                    $shoprole = $shop->roles()->where('role_id', $r->id)->first();
                    if (!is_null($shoprole)) {
                        Log::info('Role '.$R->display_name.' already exists');
                    }else{
                        $shop->roles()->attach($r);
                    }
                }

                // $r->name = $r->display_name;
                // $r->save();
            }
        }
        return $roles;
        // $user = User::where('phone', '0772119707')->first();
        // $shops = Shop::all();
        // foreach ($shops as $key => $shop) {
        //     if ($key > 0) {
        //         $user->shops()->attach($shop);
        //     }else{
        //         $user->shops()->attach($shop, ['is_default' => true]);
        //     }
        // }

        // return $user->shops()->select('id', 'name')->get();
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
        $role = Role::find(decrypt($id));
        $role->name = $request['name'];
        $role->display_name = $request['name'];
        $role->description = $request['description'];
        $role->save();

        if (is_array($request['permission']) || is_object($request['permission'])) {
            $sameroles = Role::where('name', $role->name)->get();
            foreach ($sameroles as $key => $r) {
                $r->syncPermissions();
                foreach ($request['permission'] as $perm) {
                    $r->givePermissionTo($perm);
                }
            }
            return redirect()->back()->with('success', 'Role Permissions updated successfully');
        }else{
            return redirect()->back()->with('info', 'No Permissions updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::destroy(decrypt($id));
        return redirect('admin/roles');
    }
}
