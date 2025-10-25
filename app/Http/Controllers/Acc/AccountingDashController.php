<?php

namespace App\Http\Controllers\Acc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountingDashController extends Controller
{
    public function index(Request $request)
    {
        $page = 'Accounting Dashboard';
        $title = 'Accounting Dashboard';
        $title_sw = 'Accounting Dashboard';

        return view('accounting.index', compact('page', 'title', 'title_sw'));
    }
}
