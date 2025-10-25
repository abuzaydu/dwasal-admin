<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryDashController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $page = 'Inventory Dashboard';
        $title = 'Inventory Dashboard';
        $title_sw = 'Inventory Dashboard';
        return view('products.dashboard', compact('page', 'title', 'title_sw'));
    }
}
