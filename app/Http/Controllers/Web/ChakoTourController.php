<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ChakoTour;

class ChakoTourController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Chako Tours';
        $title = 'Chako Tours';
        $now = Carbon::now();
        $start = $now->startOfDay();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
        $is_post_query = false;
        
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $shop = Shop::find(Session::get('shop_id'));

        $tours = ChakoTour::where('shop_id', $shop->id)->whereBetween('tour_date', [$start, $end])->get();

        return view('services.tours.index', compact('page', 'title', 'shop', 'tours', 'is_post_query', 'start_date', 'end_date'));
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
        $shop = Shop::find(Session::get('shop_id'));
        $user = Auth::user();
        $tourdate = Carbon::now();
        if (!empty($request['tour_date'])) {
            $crtime = Carbon::now();
            $time = date('H:i:s', strtotime($crtime));
            $tourdate = $request['tour_date'].' '.$time;
        }
        $tour = new ChakoTour();
        $tour->shop_id = $shop->id;
        $tour->user_id = $user->id;
        $tour->tour_date = $tourdate;
        $tour->category = $request['category'];
        $tour->internal_guider = $request['internal_guider'];
        $tour->external_guider = $request['external_guider'];
        $tour->no_of_visitors = $request['no_of_visitors'];
        $tour->comments = $request['comments'];
        $tour->save();

        return redirect('chako-tours')->with('success', 'Tour records created successfully');
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
        $page = 'Edit Tour record';
        $title = 'Edit Tour record';
        $tour = ChakoTour::find(decrypt($id));

        return view('services.tours.edit', compact('page', 'title', 'tour'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $crtime = Carbon::now();
        $time = date('H:i:s', strtotime($crtime));
        $tourdate = $request['tour_date'].' '.$time;
        
        $tour = ChakoTour::find(decrypt($id));
        $tour->tour_date = $tourdate;
        $tour->category = $request['category'];
        $tour->internal_guider = $request['internal_guider'];
        $tour->external_guider = $request['external_guider'];
        $tour->no_of_visitors = $request['no_of_visitors'];
        $tour->comments = $request['comments'];
        $tour->save();

        return redirect('chako-tours')->with('Tour record updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tour = ChakoTour::find(decrypt($id));
        if (!is_null($tour)) {
            $tour->delete();
            return redirect('chako-tours')->with('success', 'Tour record updated successfully');
        }else{
            return redirect('chako-tours')->with('error', 'Tour record not Found');
        }
    }
}
