<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Feature;

class PermissionController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Permissions';
        $title = 'Permissions';
        $features = Feature::all();
        $feature = null;
        $permissions = Permission::all();
        if (!empty($request['feature_id'])) {
            $feature = Feature::find($request['feature_id']);
            $permissions = Permission::where('feature_id', $feature->id)->get();
        }
        return view('admin.permissions.index', compact('page', 'title', 'permissions', 'feature', 'features'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Permission::create([
            'feature_id' => $request['feature_id'],
            'name' => $request['name'],
            'display_name' => $request['display_name'],
            'description' => $request['description']
        ]);
        return redirect('admin/permissions');
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
        $features = Feature::all();
        $permission = Permission::find(decrypt($id));
        $page = 'Edit Permission';
        $title = 'Edit Permission';

        return view('admin.permissions.edit', compact('page', 'title', 'features', 'permission'));
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
        $permission = Permission::find(decrypt($id));
        $permission->feature_id = $request['feature_id'];
        $permission->name = $request['name'];
        $permission->display_name = $request['display_name'];
        $permission->description = $request['description'];
        $permission->save();
        return redirect('admin/permissions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Permission::destroy(decrypt($id));
        return redirect('admin/permissions');
    }
}
