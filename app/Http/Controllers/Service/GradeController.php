<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Grade;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $shop = Shop::find(Session::get('shop_id'));
        $grade = Grade::create([
            'shop_id' => $shop->id,
            'name' => $request['name']
        ]);

        return redirect()->back()->with('success', 'Your Grade registered successfully');
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
        $grade = Grade::find(decrypt($id));
        $page = 'Services';
        $title = 'Edit Grade info';
        $title_sw = 'Hariri Maelezo ya Kifaa';

        return view('services.edit-grade', compact('page', 'title', 'title_sw', 'grade'));
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
        $grade = Grade::find(decrypt($id));
        $grade->name = $request['name'];
        $grade->save();

        return redirect('services')->with('success', 'Grade successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grade = Grade::find(decrypt($id));
        if (!is_null($grade)) {
            $grade->delete();
        }

        return redirect()->back()->with('success', 'Grade successfully deleted');
    }
}
