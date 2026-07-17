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
                    <h5>Edit {{ $quotation->quote_number }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('quotations.update', encrypt($quotation->id)) }}" method="POST" id="quotationForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $quotation->customer_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $quotation->email) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $quotation->phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $quotation->address) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    @foreach (['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $quotation->status == $status ? 'selected' : '' }}>{{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->valid_until) }}">
                            </div>
                        </div>

                        <hr>
                        <h6>Line Items</h6>

                        {{-- Item search --}}
                        <div class="row mb-2">
                            <div class="col-md-8">
                                <label class="form-label">Search Item</label>
                                <div class="input-group mb-0">
                                    <input type="text" id="search_key" class="form-control form-control-sm" placeholder="Search product to add..." autocomplete="off">
                                    <button class="btn btn-outline-danger btn-sm" type="button" id="clearSearch"><i class="fa fa-close"></i></button>
                                </div>
                                <ul id="searchResult3" class="list-group position-absolute" style="z-index: 1000; width: 66%;"></ul>
                            </div>
                        </div>

                        <table class="table table-sm" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 40%">Description</th>
                                    <th style="width: 15%">Qty</th>
                                    <th style="width: 18%">Unit Price</th>
                                    <th style="width: 20%">Total</th>
                                    <th style="width: 7%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotation->items as $i => $item)
                                    <tr class="item-row" data-product-id="{{ $item->product_id }}">
                                        <td>
                                            <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                                            <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $item->item_description }}" {{ $item->product_id ? 'readonly' : '' }} required>
                                        </td>
                                        <td><input type="number" step="0.01" min="0.01" name="items[{{ $i }}][quantity]" class="form-control form-control-sm qty-input" value="{{ $item->quantity }}" required></td>
                                        <td><input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price-input" value="{{ $item->unit_price }}" required></td>
                                        <td><span class="row-total">{{ number_format($item->total_price, 2) }}</span></td>
                                        <td><button type="button" class="btn btn-sm btn-link text-danger remove-row" title="Remove"><i class="fa fa-times"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted small" id="noItemsMsg" style="{{ $quotation->items->count() ? 'display:none;' : '' }}">
                            No items added yet. Search and select a product above.
                        </p>

                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="row mb-2">
                                    <div class="col-6">Subtotal</div>
                                    <div class="col-6 text-end" id="subtotalDisplay">{{ number_format($quotation->subtotal, 2) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Discount</div>
                                    <div class="col-6"><input type="number" step="0.01" min="0" name="discount" id="discountInput" class="form-control form-control-sm text-end" value="{{ $quotation->discount }}"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Tax</div>
                                    <div class="col-6"><input type="number" step="0.01" min="0" name="tax_amount" id="taxInput" class="form-control form-control-sm text-end" value="{{ $quotation->tax_amount }}"></div>
                                </div>
                                <div class="row mb-2 fw-bold">
                                    <div class="col-6">Total</div>
                                    <div class="col-6 text-end" id="totalDisplay">{{ number_format($quotation->total, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="2" class="form-control">{{ old('notes', $quotation->notes) }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('quotations.show', encrypt($quotation->id)) }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        $(function() {
            var rowIndex = {{ $quotation->items->count() }};

            function recalc() {
                var subtotal = 0;
                $('.item-row').each(function() {
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
                $('#noItemsMsg').toggle($('.item-row').length === 0);
                $('#saveBtn').prop('disabled', $('.item-row').length === 0);
            }

            function addItemRow(product) {
                // If this product is already in the table, just focus its qty field
                var existing = $('.item-row[data-product-id="' + product.id + '"]');
                if (existing.length) {
                    existing.find('.qty-input').focus().select();
                    return;
                }

                var i = rowIndex++;
                var description = product.slug || product.name || '';
                var price = product.cost_per_unit || product.retail_price || 0;

                var row = `
            <tr class="item-row" data-product-id="${product.id}">
                <td>
                    <input type="hidden" name="items[${i}][product_id]" value="${product.id}">
                    <input type="text" name="items[${i}][description]" class="form-control form-control-sm" value="${description}" readonly required>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="items[${i}][quantity]" class="form-control form-control-sm qty-input" value="1" required></td>
                <td><input type="number" step="0.01" min="0" name="items[${i}][unit_price]" class="form-control form-control-sm price-input" value="${price}" required></td>
                <td><span class="row-total">0.00</span></td>
                <td><button type="button" class="btn btn-sm btn-link text-danger remove-row" title="Remove"><i class="fa fa-times"></i></button></td>
            </tr>`;
                $('#itemsTable tbody').append(row);
                recalc();
            }

            // Search products
            $('#search_key').on('keyup', function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $('#searchResult3').empty();
                    return;
                }
                $.ajax({
                    url: "{{ url('search-product') }}",
                    type: 'GET',
                    data: {
                        'search_key': query
                    },
                    success: function(response) {
                        $('#searchResult3').empty();
                        $.each(response, function(i, prod) {
                            var qty = +prod.in_stock;
                            var stockColor = qty > 0 ? 'blue' : 'red';
                            $('#searchResult3').append(
                                `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${prod.id}" style="cursor:pointer;">
                            <div>${prod.slug}</div>
                            <span style="color:${stockColor}">(${qty})</span>
                        </li>`
                            );
                        });
                    }
                });
            });

            // Select a product from the search results
            $(document).on('click', '#searchResult3 li', function() {
                var productId = $(this).data('id');
                $.ajax({
                    url: "{{ url('fetch-product') }}",
                    type: 'GET',
                    data: {
                        'product_id': productId
                    },
                    success: function(product) {
                        addItemRow(product);
                        $('#search_key').val('');
                        $('#searchResult3').empty();
                    }
                });
            });

            $('#clearSearch').on('click', function() {
                $('#search_key').val('');
                $('#searchResult3').empty();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalc();
            });

            $(document).on('input', '.qty-input, .price-input, #discountInput, #taxInput', recalc);

            recalc();
        });
    </script>
@endsection
