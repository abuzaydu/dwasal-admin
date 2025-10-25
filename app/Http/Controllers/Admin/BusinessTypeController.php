<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessType;
use App\Models\BusinessSubType;
use Illuminate\Support\Facades\Crypt;
use File;

class BusinessTypeController extends Controller
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
        $page = 'Business Types';
        $title ='Business Types';

        $types = BusinessType::all();

        return view('admin.btypes.index', compact('page', 'title', 'types'));
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
        $btype = BusinessType::find($request['business_type_id']);
        if (!is_null($btype)) {
            $bsubtype = new BusinessSubType();
            $bsubtype->business_type_id = $btype->id;
            $bsubtype->name = $request['name'];
            $bsubtype->name_sw = $request['name_sw'];
            $bsubtype->description = $request['description'];
            $bsubtype->description_sw = $request['description_sw'];
            $bsubtype->save();

            return redirect('types')->with('success', 'Business Type was successfully added');
        }else{
            return redirect('types')->with('error', 'Something went wrong. Please fill the forma again.');
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
        $page = 'Business Types';
        $title = 'Edit Business Type';
        $btype = BusinessType::find(decrypt($id));
        return view('admin.btypes.edit', compact('page', 'title', 'btype'));
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
        $btype = BusinessType::find(decrypt($id));
        $btype->type = $request['type'];
        $btype->description = $request['description'];
        $btype->type_sw = $request['type_sw'];
        $btype->description_sw = $request['description_sw'];
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $img_path = storage_path('/public/btypes/'.$btype->type_icon);
                if (File::exists($img_path)) {
                    unlink($img_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/public/btypes', $btype->id.'.'.$extension);
                $location = $btype->id.'.'.$extension;
                $btype->type_icon = $location;
            }
        }
        $btype->save();

        return redirect('admin/types')->with('message', 'BusinessType was successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $type = BusinessType::find(decrypt($id));
        if (!is_null($type)) {
            $type->delete();
            return redirect()->back()->with('message', 'BusinessType was successfully updated');
        }
    }
}
