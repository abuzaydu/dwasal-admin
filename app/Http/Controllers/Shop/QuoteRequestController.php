<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Models\QuoteRequest;

class QuoteRequestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = 'Quote Requests';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = \Carbon\Carbon::now();
        $start_date = $start->format('Y-m-d');            
        $end_date = $end->format('Y-m-d');
      
        //check if user opted for date range
        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'].' 00:00:00';
            $end = $request['end_date'].' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        
        $quoterequests = QuoteRequest::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();

        return view('shop.quotes.requests.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'duration', 'quoterequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page = 'New Quote Request';
        $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NIN'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

        return view('shop.quotes.requests.create', compact('page','custids'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $request->validate([
            'name'    => 'nullable|string|max:125',
            'email'   => 'required|email|max:125',
            'phone'   => 'required|string|max:125',
            'address' => 'nullable|string|max:125',
            'product' => 'nullable|string',
            'message' => 'required|string',
        ]);

        QuoteRequest::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'product' => $request->product,
            'message' => $request->message,
            'status'  => 'SENT',
        ]);

        return redirect()->route('quote-requests.index')->with('success', 'Quote request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('quote-requests.index')->with('error', 'Invalid quote request.');
        }

        $qrequest = QuoteRequest::findOrFail($decryptedId);
        $page = 'Quote Request Details';

        return view('shop.quotes.requests.show', compact('qrequest', 'page'));
    }





    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('quote-requests.index')->with('error', 'Invalid quote request.');
        }

        $request->validate([
            'name'    => 'nullable|string|max:125',
            'email'   => 'required|email|max:125',
            'phone'   => 'required|string|max:125',
            'address' => 'nullable|string|max:125',
            'product' => 'nullable|string',
            'message' => 'required|string',
            'status'  => 'required|string|max:125',
        ]);

        $qrequest = QuoteRequest::findOrFail($decryptedId);

        $qrequest->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'product' => $request->product,
            'message' => $request->message,
            'status'  => $request->status,
        ]);

        return redirect()->route('quote-requests.index')->with('success', 'Quote request updated successfully.');
    }

    public function approve(Request $request, $id)
    { 
        try {
            $decryptedId = decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('quote-requests.index')->with('error', 'Invalid quote request.');
        }
        $qrequest = QuoteRequest::findOrFail($decryptedId);
        $qrequest->update([
            'status'       => 'Approved',
            'processed_by' => auth()->check() ? (auth()->user()->first_name ?? auth()->user()->name ?? null) : null,
        ]);
        return redirect()->route('quote-requests.show', encrypt($qrequest->id))->with('success', 'Quote request approved. You can now create a quotation.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('quote-requests.index')->with('error', 'Invalid quote request.');
        }
        $qrequest = QuoteRequest::findOrFail($decryptedId);
        $qrequest->delete();

        return redirect()->route('quote-requests.index')->with('success', 'Quote request deleted successfully.');
    }
}
