<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\FoodType;
use App\Models\RmUse;

class FoodTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Food Types';
        $title = 'Food Types';
        $shop = Shop::find(Session::get('shop_id'));
        $foodtypes = FoodType::where('shop_id', $shop->id)->get();

        return view('production.food-types.index', compact('page', 'title', 'foodtypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $ftype = FoodType::where('shop_id', $shop->id)->where('name', $request['name'])->first();
        if (is_null($ftype)) {
            $ftype = new FoodType();
            $ftype->shop_id = $shop->id;
            $ftype->name = $request['name'];
            $ftype->description = $request['description'];
            $ftype->save();
        }

        return redirect('food-types')->with('success', 'Food Type created successfully');
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
        $page = 'Edit Food Type';
        $title = 'Edit Food Type';
        $ftype = FoodType::find(decrypt($id));

        return view('production.food-types.edit', compact('page', 'title', 'ftype'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ftype = FoodType::find(decrypt($id));
        if (!is_null($ftype)) {
            $ftype->name = $request['name'];
            $ftype->description = $request['description'];
            $ftype->save();
            return redirect('food-types')->with('success', 'Food Type updated successfully');
        }else{
            return redirect('food-types')->with('error', 'Food Type not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ftype = FoodType::find(decrypt($id));
        if (!is_null($ftype)) {
            $rmuses = RmUse::where('food_type_id', $ftype->id)->count();
            if ($rmuses > 0) {
                return redirect('food-types')->with('info', 'Food Type cannot be removed because has productions done');
            }else{
                $ftype->delete();
                return redirect('food-types')->with('success', 'Food Type updated successfully');
            }
        }else{
            return redirect('food-types')->with('error', 'Food Type not found');
        }
    }
}
