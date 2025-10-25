<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
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
    public function index()
    {
        $page = 'Modules';
        $title = 'Modules';
        $modules = Module::all();

        return view('admin.modules.index', compact('page', 'title', 'modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = "New Module";
        $title = "New Module";
        return view('admin.modules.create', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $module = new Module();
        $module->name = $request['name'];
        $module->display_name = $request['display_name'];
        $module->price = $request['price'];
        $module->duration = 'Monthly';
        $module->description = $request['description'];
        $module->save();

        return redirect('admin/modules')->with('success', 'Module created successfully');
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
        $module = Module::find(decrypt($id));
        $page = 'Edit Module';
        $title = 'Edit Module';
        return view('admin.modules.edit', compact('page', 'title', 'module'));
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
        $module = Module::find(decrypt($id));
        $module->name = $request['name'];
        $module->price = $request['price'];
        $module->display_name = $request['display_name'];
        $module->description = $request['description'];
        $module->save();

        return redirect('admin/modules')->with('success', 'Module updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Module::destroy(decrypt($id));

        return redirect('admin/modules')->with('success', 'Module deleted successfully');
    }
}
