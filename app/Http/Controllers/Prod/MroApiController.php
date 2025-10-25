<?php

namespace App\Http\Controllers\Prod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Session;
use App\Models\Shop;

class MroApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $mros = $shop->mro()->where('is_deleted' , false)->get([
            \DB::raw('id'),
            \DB::raw('name'),]);

        return Response::json($mros);
    }


}
