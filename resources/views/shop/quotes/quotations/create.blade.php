@extends('layouts.app')
@section('content')
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotes</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>New Quotation</h5>
                    @if($quoteRequest)
                        <span class="badge bg-info float-right">From Quote Request #{{ $quoteRequest->id }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
                        @csrf
                        @if($quoteRequest)
                            <input type="hidden" name="quote_request_id" value="{{ encrypt($quoteRequest->id) }}">
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $quoteRequest->name ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $quoteRequest->email ?? '') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $quoteRequest->phone ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $quoteRequest->address ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                            </div>
                        </div>

                        <hr>
                        <h6>Line Items</h6>
                        <table class="table table-sm" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Description</th>
                                    <th style="width: 15%">Qty</th>
                                    <th style="width: 18%">Unit Price</th>
                                    <th style="width: 15%">Total</th>
                                    <th style="width: 7%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td><input type="text" name="items[0][description]" class="form-control form-control-sm" required>
                                        @if($quoteRequest && $quoteRequest->product)
                                        <small class="text-muted">Requested: {{ $quoteRequest->product }}</small>
                                        @endif
                                    </td>
                                    <td><input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control form-control-sm qty-input" value="1" required></td>
                                    <td><input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control form-control-sm price-input" value="0" required></td>
                                    <td><span class="row-total">0.00</span></td>
                                    <td><button type="button" class="btn btn-sm btn-link text-danger remove-row" title="Remove"><i class="fa fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" id="addRow" class="btn btn-sm btn-default mb-3"><i class="fa fa-plus"></i> Add Item</button>

                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="row mb-2">
                                    <div class="col-6">Subtotal</div>
                                    <div class="col-6 text-end" id="subtotalDisplay">0.00</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Discount</div>
                                    <div class="col-6"><input type="number" step="0.01" min="0" name="discount" id="discountInput" class="form-control form-control-sm text-end" value="0"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Tax</div>
                                    <div class="col-6"><input type="number" step="0.01" min="0" name="tax_amount" id="taxInput" class="form-control form-control-sm text-end" value="0"></div>
                                </div>
                                <div class="row mb-2 fw-bold">
                                    <div class="col-6">Total</div>
                                    <div class="col-6 text-end" id="totalDisplay">0.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('quotations.index') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Quotation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
<script>
$(function () {
    var rowIndex = 1;

    function recalc() {
        var subtotal = 0;
        $('.item-row').each(function () {
            var qty = parseFloat($(this).find('.qty-input').val()) || 0;
            var price = parseFloat($(this).find('.price-input').val()) || 0;
            var rowTotal = qty * price;
            $(this).find('.row-total').text(rowTotal.toFixed(2));
            subtotal += rowTotal;
        });
        var discount = parseFloat($('#discountInput').val()) || 0;
        var tax = parseFloat($('#taxInput').val()) || 0;
        var total = subtotal - discount + tax;

        $('#subtotalDisplay').text(subtotal.toFixed(2));
        $('#totalDisplay').text(total.toFixed(2));
    }

    $('#addRow').on('click', function () {
        var row = `
            <tr class="item-row">
                <td><input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm" required></td>
                <td><input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="form-control form-control-sm qty-input" value="1" required></td>
                <td><input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm price-input" value="0" required></td>
                <td><span class="row-total">0.00</span></td>
                <td><button type="button" class="btn btn-sm btn-link text-danger remove-row" title="Remove"><i class="fa fa-times"></i></button></td>
            </tr>`;
        $('#itemsTable tbody').append(row);
        rowIndex++;
    });

    $(document).on('click', '.remove-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            recalc();
        }
    });

    $(document).on('input', '.qty-input, .price-input, #discountInput, #taxInput', recalc);

    recalc();
});
</script>
@endsection