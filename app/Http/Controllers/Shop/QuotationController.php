<?php

namespace App\Http\Controllers\Shop;

use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuoteRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $page = 'Quotations';
    //     $now = Carbon::now();
    //     $start = $now->startOfMonth();
    //     $end = \Carbon\Carbon::now();
    //     $start_date = $start->format('Y-m-d');            
    //     $end_date = $end->format('Y-m-d');
      
    //     //check if user opted for date range
    //     $is_post_query = false;
    //     if (!empty($request['start_date'])) {
    //         $start_date = $request['start_date'];
    //         $end_date = $request['end_date'];
    //         $start = $request['start_date'].' 00:00:00';
    //         $end = $request['end_date'].' 23:59:59';
    //         $is_post_query = true;
    //     }

    //     $duration = 'From '.date('d-m-Y', strtotime($start)).' To '.date('d-m-Y', strtotime($end)).'.';
        

    //     return view('shop.quotes.quotations.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'duration',));
    // }
    public function index(Request $request)
    {
        $page = 'Quotations';
        $now = Carbon::now();
        $start = $now->startOfMonth();
        $end = Carbon::now();
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        $is_post_query = false;
        if (!empty($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        }

        $duration = 'From ' . date('d-m-Y', strtotime($start)) . ' To ' . date('d-m-Y', strtotime($end)) . '.';

        $quotations = Quotation::whereBetween('created_at', [$start, $end])
            ->orderByDesc('id')
            ->get();

        return view('shop.quotes.quotations.index', compact('page', 'is_post_query', 'start_date', 'end_date', 'duration', 'quotations'));
    }

    public function createFromQuoteRequest(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        return redirect('quotations/create?quote_request_id=' . encrypt($request->id));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $page = 'New Quotation';
        $quoteRequest = null;

        if ($request->filled('quote_request_id')) {
            try {
                $decryptedId = decrypt($request->quote_request_id);
                $quoteRequest = QuoteRequest::find($decryptedId);
            } catch (DecryptException $e) {
                $quoteRequest = null;
            }
        }

        return view('shop.quotes.quotations.create', compact('page', 'quoteRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quote_request_id'    => 'nullable|string',
            'customer_name'       => 'nullable|string|max:125',
            'email'               => 'required|email|max:125',
            'phone'               => 'required|string|max:125',
            'address'             => 'nullable|string|max:125',
            'valid_until'         => 'nullable|date',
            'notes'               => 'nullable|string',
            'discount'            => 'nullable|numeric|min:0',
            'tax_amount'          => 'nullable|numeric|min:0',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|integer',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $quotation = DB::transaction(function () use ($request) {
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $discount = $request->discount ?? 0;
            $tax = $request->tax_amount ?? 0;
            $total = $subtotal - $discount + $tax;

            $quoteRequestId = null;
            if ($request->filled('quote_request_id')) {
                try {
                    $quoteRequestId = decrypt($request->quote_request_id);
                } catch (DecryptException $e) {
                    $quoteRequestId = null;
                }
            }
            $year = date('Y');
            $count = Quotation::whereYear('created_at', $year)->count() + 1;
            $quoteNumber = 'QUO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quote_request_id' => $quoteRequestId,
                'quote_number'     => $quoteNumber,
                'customer_name'    => $request->customer_name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'address'          => $request->address,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax_amount'       => $tax,
                'total'            => $total,
                'status'           => 'Draft',
                'valid_until'      => $request->valid_until,
                'notes'            => $request->notes,
                'created_by'       => auth()->check() ? (auth()->user()->first_name ?? auth()->user()->name ?? null) : null,
            ]);

            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id'      => $quotation->id,
                    'product_id'        => $item['product_id'] ?? null,
                    'product_unit_id'  => $item['product_unit_id'],
                    'item_description'  => $item['description'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'total_price'       => $item['quantity'] * $item['unit_price'],
                ]);
            }

            if ($quoteRequestId) {
                QuoteRequest::where('id', $quoteRequestId)->update([
                    'is_quoted'    => 1,
                    'quoted_at'    => now(),
                    'processed_by' => auth()->check() ? (auth()->user()->first_name ?? auth()->user()->name ?? null) : null,
                ]);
            }

            return $quotation;
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation ' . $quotation->quote_number . ' created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('quotations.index')->with('error', 'Invalid quotation.');
        }
        $quotation = Quotation::with('items')->findOrFail($decryptedId);
        $page = 'Quotation Details';

        return view('shop.quotes.quotations.show', compact('quotation', 'page'));
    }

    public function send($id)
    {
        try {
            $quotation = Quotation::findOrFail(decrypt($id));

            if ($quotation->status !== 'Draft') {
                return back()->with('error', 'Only draft quotations can be sent.');
            }
            $quotation->update([
                'status' => 'Sent',
            ]);

            // Optional for later use
            // Mail::to($quotation->email)->send(new QuotationMail($quotation));

            return back()->with('success', 'Quotation sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function accept($id)
    {
        try {
            $quotation = Quotation::findOrFail(decrypt($id));

            if ($quotation->status !== 'Sent') {
                return back()->with('error', 'Only sent quotations can be accepted.');
            }
            $quotation->update([
                'status' => 'Accepted',
            ]);
            return back()->with('success','Quotation accepted. You can now create a Proforma Invoice.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $quotation = Quotation::findOrFail(decrypt($id));

            if ($quotation->status !== 'Sent') {
                return back()->with('error', 'Only sent quotations can be rejected.');
            }
            $quotation->update([
                'status' => 'Rejected',
            ]);
            return back()->with('success', 'Quotation rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('quotations.index')->with('error', 'Invalid quotation.');
        }
        $quotation = Quotation::with('items')->findOrFail($decryptedId);
        $page = 'Edit Quotation';
        $quoteRequest = null;

        return view('shop.quotes.quotations.edit', compact('quotation', 'page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('quotations.index')->with('error', 'Invalid quotation.');
        }
        $request->validate([
            'customer_name'       => 'nullable|string|max:125',
            'email'               => 'required|email|max:125',
            'phone'               => 'required|string|max:125',
            'address'             => 'nullable|string|max:125',
            'valid_until'         => 'nullable|date',
            'notes'               => 'nullable|string',
            'discount'            => 'nullable|numeric|min:0',
            'tax_amount'          => 'nullable|numeric|min:0',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|integer',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $decryptedId) {
            $quotation = Quotation::findOrFail($decryptedId);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $discount = $request->discount ?? 0;
            $tax = $request->tax_amount ?? 0;
            $total = $subtotal - $discount + $tax;

            $quotation->update([
                'customer_name' => $request->customer_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'subtotal'      => $subtotal,
                'discount'      => $discount,
                'tax_amount'    => $tax,
                'total'         => $total,
                'status'        => 'Draft',
                'valid_until'   => $request->valid_until,
                'notes'         => $request->notes,
            ]);

            $quotation->items()->delete();
            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id'      => $quotation->id,
                    'product_id'        => $item['product_id'] ?? null,
                    'item_description'  => $item['description'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'total_price'       => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('quotations.index')->with('error', 'Invalid quotation.');
        }
        $quotation = Quotation::findOrFail($decryptedId);
        $quotation->delete(); 

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
