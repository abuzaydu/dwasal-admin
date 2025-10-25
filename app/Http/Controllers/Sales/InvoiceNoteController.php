<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\InvoiceNote;

class InvoiceNoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = 'Invoice Notes';
        $title = 'Invoice Notes';
        $shop = Shop::find(Session::get('shop_id'));
        $notes = InvoiceNote::where('shop_id', $shop->id)->get();

        return view('sales.invoice-notes.index', compact('page', 'title', 'notes'));
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
        $note = new InvoiceNote();
        $note->shop_id = $shop->id;
        $note->used_in = $request['used_in'];
        $note->note_type = $request['note_type'];
        $note->content = $request['content'];
        $note->save();

        return redirect('invoice-notes')->with('success', 'Note created successfully');
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
        $page = 'Edit Invoice note';
        $title = 'Edit Invoice note';
        $note = InvoiceNote::find(decrypt($id));

        return view('sales.invoice-notes.edit', compact('page', 'title', 'note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = InvoiceNote::find(decrypt($id));
        $note->used_in = $request['used_in'];
        $note->note_type = $request['note_type'];
        $note->content = $request['content'];
        $note->save();
        
        return redirect('invoice-notes')->with('success', 'Note updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = InvoiceNote::find(decrypt($id));
        if (!is_null($note)) {
            $note->delete();
        }
        return redirect('invoice-notes')->with('success', 'Note deleted successfully');
    }
}
