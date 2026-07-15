@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/parsleyjs/css/parsley.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('quote-requests') }}">Quotes</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                    data-bs-target="#customerModal"><i class="fa fa-plus"></i> New Customer</button>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">New Quote Request</h5>
                    <form action="{{ url('quote-requests') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <input type="hidden" name="customer_id" id="cust-id">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="search_customer_key" class="form-label">Search Customer <span class="text-danger">*</span></label>
                                <input id="search_customer_key" placeholder="Type customer name to search..." class="form-control @error('name') is-invalid @enderror" autocomplete="off">
                                <ul id="searchResultCustomer" class="list-group" style="position: absolute; z-index: 999; width: 91%;"></ul>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Customer Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" readonly>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required readonly>
                                <div class="invalid-feedback">Please select a customer with a valid email.</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required readonly>
                                <div class="invalid-feedback">Please select a customer with a mobile number.</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" readonly>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="product" class="form-label">Product(s)</label>
                            <textarea name="product" id="product" rows="2" class="form-control @error('product') is-invalid @enderror" placeholder="e.g. Product A, Product B">{{ old('product') }}</textarea>
                            @error('product')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="4" class="form-control @error('message') is-invalid @enderror"
                                required>{{ old('message') }}</textarea>
                            <div class="invalid-feedback">Please enter your message.</div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ url('quote-requests') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit Quote Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- New Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="customerModalLabel">{{ trans('navmenu.new_customer') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 form-validate" method="POST" action="{{ url('new-customer') }}">
                        @csrf
                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">{{ trans('navmenu.customer_name') }} <span style="color: red; font: bold;">*</span></label>
                            <input id="register-username" type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">{{ trans('navmenu.phone_number') }}</label>
                            <input id="register-username" type="text" name="phone" placeholder="{{ trans('navmenu.hnt_customer_mobile') }}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class=" col-md-6">
                            <label for="register-email" class="form-label">{{ trans('navmenu.email_address') }}</label>
                            <input id="register-email" type="text" name="email" placeholder="{{ trans('navmenu.hnt_customer_email') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class=" col-md-6">
                            <label for="address" class="form-label">{{ trans('navmenu.postal_address') }}</label>
                            <input id="address" type="text" name="postal_address" placeholder="{{ trans('navmenu.hnt_postal_address') }}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class=" col-md-6">
                            <label for="address" class="form-label">{{ trans('navmenu.physical_address') }}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{ trans('navmenu.hnt_physical_address') }}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class=" col-md-6">
                            <label for="address" class="form-label">{{ trans('navmenu.street') }}</label>
                            <input id="address" type="text" name="street" placeholder="{{ trans('navmenu.hnt_street') }}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">{{ trans('navmenu.tin') }}</label>
                            <input id="register-username" type="text" name="tin" placeholder="{{ trans('navmenu.hnt_customer_tin') }}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "999-999-999"' data-mask>
                        </div>
                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">{{ trans('navmenu.vrn') }}</label>
                            <input id="register-username" type="text" name="vrn" placeholder="{{ trans('navmenu.hnt_customer_vrn') }}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{ trans('navmenu.cust_id_type') }}</label>
                            <select class="form-select form-select-sm mb-1" name="cust_id_type">
                                @foreach ($custids as $cid)
                                    @if ($cid['id'] == 6)
                                        <option value="{{ $cid['id'] }}" selected>{{ $cid['name'] }}</option>
                                    @else
                                        <option value="{{ $cid['id'] }}">{{ $cid['name'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{ trans('navmenu.btn_save') }}</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/vendor/parsleyjs/js/parsley.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#search_customer_key').on('keyup', function() {
                var query = $(this).val();

                if (query.length < 2) {
                    $("#searchResultCustomer").empty();
                    return;
                }

                $.ajax({
                    url: "{{ url('search-customer') }}",
                    type: 'GET',
                    data: {
                        'search_customer_key': query
                    },
                    success: function(response) {
                        var len = response.length;
                        $("#searchResultCustomer").empty();
                        for (var i = 0; i < len; i++) {
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResultCustomer").append(
                                "<li class='list-group-item' style='cursor:pointer;' value='" +
                                id + "'>" + name + "</li>"
                            );
                        }

                        $("#searchResultCustomer li").on('click', function() {
                            selectCustomer(this);
                        });
                    }
                });
            });

            $('#search_customer_key').on('input', function() {
                if ($('#cust-id').val() !== '' && $(this).val() === '') {
                    clearCustomerFields();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search_customer_key, #searchResultCustomer').length) {
                    $('#searchResultCustomer').empty();
                }
            });
        });

        function selectCustomer(element) {
            var customerId = $(element).attr('value');
            var customerName = $(element).text();

            $('#search_customer_key').val(customerName);
            $('#searchResultCustomer').empty();

            $.ajax({
                url: "{{ url('fetch-customer') }}",
                type: 'GET',
                data: {
                    'customer_id': customerId
                },
                success: function(customer) {
                    $('#cust-id').val(customer.id);
                    $('#name').val(customer.name);
                    $('#email').val(customer.email);
                    $('#phone').val(customer.phone);
                    $('#address').val(customer.physical_address);
                }
            });
        }

        function clearCustomerFields() {
            $('#cust-id').val('');
            $('#name').val('');
            $('#email').val('');
            $('#phone').val('');
            $('#address').val('');
        }
    </script>
@endsection
