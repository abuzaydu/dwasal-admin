<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionType;
use Illuminate\Support\Facades\Crypt;

class SubscriptionTypeController extends Controller
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
        $page = 'Subscription Types';
        $title ='Subscription Types';

        $subscriptions = SubscriptionType::all();

        return view('admin.subscriptions.index', compact('page', 'title', 'subscriptions'));
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
        $subscr_type = SubscriptionType::create([
            'title' => $request['title'],
            'description' => $request['description']
        ]);

        return redirect()->back()->with('message', 'SubscriptionType was successfully added');
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
        $page = 'Subscription Types';
        $title = 'Edit Subscription Type';
        $subscr_type = SubscriptionType::find(decrypt($id));

        return view('admin.subscriptions.edit', compact('page', 'title', 'subscr_type'));
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
        $subscr_type = SubscriptionType::find(decrypt($id));
        $subscr_type->title = $request['title'];
        $subscr_type->description = $request['description'];
        $subscr_type->save();

        return redirect('admin/subscriptions')->with('message', 'SubscriptionType was successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $subscr_type = SubscriptionType::find(decrypt($id));
        if (!is_null($subscr_type)) {
            $subscr_type->delete();
            return redirect('admin/subscriptions')->with('message', 'SubscriptionType was successfully updated');
        }
    }
}
