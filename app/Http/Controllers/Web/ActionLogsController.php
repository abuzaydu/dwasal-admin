<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\ActionLog;

class ActionLogsController extends Controller
{
    public function index(Request $request)
    {
        $page = 'User Action Logs';
        $title = 'User Action Logs';
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));
          
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $actionlogs = ActionLog::where('shop_id', $shop->id)->whereBetween('action_logs.created_at', [$start, $end])->join('users', 'users.id', '=', 'action_logs.user_id')->select('action_logs.id as id', 'action_type', 'log_message', 'action_logs.created_at as created_at', 'first_name', 'last_name')->get();

        return view('account.logs.index', compact('page', 'title', 'actionlogs', 'is_post_query', 'start_date', 'end_date'));
    }
}
