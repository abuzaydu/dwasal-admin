@extends('layouts.app')
@section('content')
    <div class="block-header pt-4">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotes</a></li>
            <li class="breadcrumb-item active">{{ $page }}</li>
        </ul>
    </div>

    <div class="row clearfix">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>{{ $quotation->quote_number }}</h5>
                    <a href="{{ route('quotations.index') }}" class="btn btn-default btn-sm float-right">Back to List</a>
                    <a href="{{ route('quotations.edit', encrypt($quotation->id)) }}" class="btn btn-primary btn-sm float-right me-2">Edit</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Customer</strong></div>
                        <div class="col-md-3">{{ $quotation->customer_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Email</strong></div>
                        <div class="col-md-3">{{ $quotation->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Mobile</strong></div>
                        <div class="col-md-3">{{ $quotation->phone }}</div>
                        <div class="col-md-3"><strong>Address</strong></div>
                        <div class="col-md-3">{{ $quotation->address ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Status</strong></div>
                        <div class="col-md-3"><span class="badge bg-info">{{ $quotation->status }}</span></div>
                        <div class="col-md-3"><strong>Valid Until</strong></div>
                        <div class="col-md-3">{{ $quotation->valid_until ?? '-' }}</div>
                    </div>
                    <hr>
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->items as $item)
                            <tr>
                                <td>{{ $item->item_description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="row"><div class="col-6">Subtotal</div><div class="col-6 text-end">{{ number_format($quotation->subtotal, 2) }}</div></div>
                            <div class="row"><div class="col-6">Discount</div><div class="col-6 text-end">{{ number_format($quotation->discount, 2) }}</div></div>
                            <div class="row"><div class="col-6">Tax</div><div class="col-6 text-end">{{ number_format($quotation->tax_amount, 2) }}</div></div>
                            <div class="row fw-bold"><div class="col-6">Total</div><div class="col-6 text-end">{{ number_format($quotation->total, 2) }}</div></div>
                        </div>
                    </div>
                    @if($quotation->notes)
                    <hr>
                    <strong>Notes</strong>
                    <p>{{ $quotation->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection