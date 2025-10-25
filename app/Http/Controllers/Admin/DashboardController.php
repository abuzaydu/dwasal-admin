<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Charts\PaymentChart;
use \DB;
use App\Models\User;
use App\Models\Shop;
use App\Models\Payment;
use Cache;

class DashboardController extends Controller
{
    private $start;
    private $end;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function index(Request $request)
    {
        $now = \Carbon\Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        $month = $now->format('F');
        $year = $now->year;
        $currmonth = $month . ', ' . $year;

        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $total_users = User::whereBetween('created_at', [$start, $end])->count();

        $total_active = count($this->onlineUsers());
        $total_guests = 0;

        $total_shops = Shop::whereBetween('created_at', [$start, $end])->count();

        $total_active_shops = Shop::join('payments', 'payments.shop_id', '=', 'shops.id')->where('payments.is_real', 1)->where('payments.is_expired', 0)->whereBetween('payments.created_at', [$start, $end])->orderBy('created_at')->count();

        //get Totals
        $total_payments = Payment::where('is_real', 1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('created_at')
            ->orderBy('created_at', 'asc')->get();

            // dd($total_payments);

        $total_reg_shops = Shop::select(DB::raw('count(id) as total_reg'), DB::raw("CONCAT_WS(', ',MONTHNAME(created_at),YEAR(created_at)) as date"))->groupby('date')->orderBy('created_at', 'desc')->get();

        $total_act_shops = Payment::where('amount_paid', '>', 0)->where('is_real', true)->select(DB::raw('count(id) as total_act'), DB::raw("CONCAT_WS(', ',MONTHNAME(activation_time),YEAR(activation_time)) as date"))->groupby('date')->orderBy('activation_time', 'desc')->get();

        $totals = array();
        foreach ($total_reg_shops as $key => $reg) {
            foreach ($total_act_shops as $key => $act) {
                foreach ($total_payments as $key => $pay) {
                    if (($act->date === $reg->date) && ($act->date === $pay->date)) {
                        array_push($totals, array_merge($reg->toArray(), $act->toArray(), $pay->toArray()));
                    }
                }
            }
        }

        $page = 'Home';
        $title = 'Dashboard';
        return view('admin.index', compact('page', 'title', 'total_users', 'total_active', 'total_guests', 'total_shops', 'total_active_shops', 'total_reg_shops', 'total_act_shops', 'total_payments', 'totals', 'is_post_query', 'start_date', 'end_date'));
    }


    public function totals()
    {
        $page = 'Payments';
        $title = 'Monthly Totals';

        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;
        $this->start = '2018-08-01 00:00:00';
        $this->end = \Carbon\Carbon::now();
        //get Totals
        $total_payments = Payment::where('status', '!=', 'Rejected')->whereBetween('created_at', [$this->start, $this->end])->where('amount_paid', '>=', 2000)->where('is_real', true)->select(DB::raw('SUM(amount_paid) as amount'), DB::raw("CONCAT_WS(', ',MONTHNAME(created_at),YEAR(created_at)) as date"))
            ->groupby('date')->orderBy('created_at', 'desc')
            ->get();;

        $total_reg_shops = Shop::whereBetween('created_at', [$this->start, $this->end])
            ->select(DB::raw('count(id) as total_reg'), DB::raw("CONCAT_WS(', ',MONTHNAME(created_at),YEAR(created_at)) as date"))
            ->groupby('date')->orderBy('created_at', 'desc')
            ->get();;

        $total_act_shops = Payment::where('amount_paid', '>', 0)->whereBetween('created_at', [$this->start, $this->end])->whereBetween('activation_time', [$this->start, $this->end])
            ->select(DB::raw('count(id) as total_act'), DB::raw("CONCAT_WS(', ',MONTHNAME(activation_time),YEAR(activation_time)) as date"))
            ->groupby('date')->orderBy('activation_time', 'desc')
            ->get();

        $totals = array();
        foreach ($total_reg_shops as $key => $reg) {
            foreach ($total_act_shops as $key => $act) {
                foreach ($total_payments as $key => $pay) {
                    if (($act->date === $reg->date) && ($act->date === $pay->date)) {
                        array_push($totals, array_merge($reg->toArray(), $act->toArray(), $pay->toArray()));
                    }
                }
            }
        }

        // return json_encode($total_act_shops);

        return view('admin.totals', compact('page', 'title', 'totals', 'is_post_query', 'start_date', 'end_date'));
    }


    public function newActivations(Request $request)
    {

        $page = 'Payments';
        $title = 'Monthly New Activations';

        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;

        $starting = '2019-07-01 00:00:00';
        $initialpays = Payment::where('status', '!=', 'Rejected')->where('amount_paid', '=', 7000)->where('is_real', true)->select(DB::raw('count(id) as qty'), DB::raw('amount_paid as amount'), DB::raw("CONCAT_WS(', ',MONTHNAME(created_at), YEAR(created_at)) as date"))
            ->groupby('date')->orderBy('created_at', 'desc')
            ->get();

        $new_activations = array();
        foreach ($initialpays as $key => $value) {

            $res = str_replace(",", "", $value->date);
            $startmonth = date('Y-m-d H:i:s', strtotime('01 ' . $res));
            $endmonth = date('Y-m-t 23:59:59', strtotime($res));
            $monthly = $value->qty;

            $quarterly = Payment::where('status', '!=', 'Rejected')->where('amount_paid', '=', 17000)->where('is_real', true)->whereBetween('created_at', [$startmonth, $endmonth])->count();
            $semi_annually = Payment::where('status', '!=', 'Rejected')->where('amount_paid', '=', 32000)->where('is_real', true)->whereBetween('created_at', [$startmonth, $endmonth])->count();
            $annually = Payment::where('status', '!=', 'Rejected')->where('amount_paid', '=', 62000)->where('is_real', true)->whereBetween('created_at', [$startmonth, $endmonth])->count();
            $uncategorized = Payment::where('status', '!=', 'Rejected')->whereRaw('(amount_paid-2000)%5000 = 0')->where('is_real', true)->whereBetween('created_at', [$startmonth, $endmonth])->count();
            array_push($new_activations, ['date' => $value->date, 'monthly' => $monthly, 'quarterly' => $quarterly, 'semi_annually' => $semi_annually, 'annually' => $annually, 'uncategorized' => $uncategorized - ($monthly + $quarterly + $semi_annually + $annually)]);
        }
        return view('admin.new-activations', compact('page', 'title', 'new_activations', 'start_date', 'end_date', 'is_post_query'));
    }

    public static function onlineUsers()
    {
        // Get the array of users
        $users = Cache::get('online-users');
        if (!$users) return collect([]);

        // Add the array to a collection so you can pluck the IDs
        $onlineUsers = collect($users);
        // Get all users by ID from the DB (1 very quick query)
        $dbUsers = User::find($onlineUsers->pluck('id')->toArray());

        // Prepare the return array
        $displayUsers = [];

        // Iterate over the retrieved DB users
        foreach ($dbUsers as $user) {
            // Get the same user as this iteration from the cache
            // so that we can check the last activity.
            // firstWhere() is a Laravel collection method.
            $onlineUser = $onlineUsers->firstWhere('id', $user['id']);
            // Append the data to the return array
            $displayUsers[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                // This Bool operation below, checks if the last activity
                // is older than 3 minutes and returns true or false,
                // so that if it's true you can change the status color to orange.
                'away' => $onlineUser['last_activity_at'] < now()->subMinutes(3),
            ];
        }
        return collect($displayUsers);
    }
}
