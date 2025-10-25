@extends('layouts.app')
@section('page-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        var itemlist = document.getElementById('item-list');
        if (elem == 'show') {
            newform.style.display = 'block';
            itemlist.style.display = 'none';
        }else{
            newform.style.display = 'none';
            itemlist.style.display = 'block';;
        }
    }


</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                @if(!$settings->is_cm_business)
                <a href="#" class="btn btn-primary btn-sm" onclick="showHideForm('show')">New Customer</a>
                <a href="{{ url('customer-categories') }}" class="btn btn-warning btn-sm">Customer Categories</a>
                @endif
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body" id="new-form" style="display: none;">
                    <div class="border-rounded p-2">
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{route('customers.store')}}">
                            @csrf
                            <div class="col-sm-4">
                                <label class="form-label">@if($settings->is_cm_business) Rider Name @else {{trans('navmenu.customer_name')}}@endif <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" placeholder="Please enter contact person" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-4">
                                <label for="address" class="form-label">Category</label>
                                <select name="customer_category_id" class="form-select form-select-sm mb-1">
                                    <option>--Select--</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">{{trans('navmenu.mobile')}} </label><br>
                                <input type="tel" class="form-control form-control-sm mb-1" id="inputPhoneNumber" name="phone" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}" data-inputmask='"mask": "9999999999"' data-mask>
                                <input type="hidden" name="phone_country" id="countryCode">
                                <input type="hidden" name="dial_code" id="dialCode">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">{{trans('navmenu.email_address')}}</label>
                                <input id="email" type="email" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-4">
                                <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                                <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.tin')}}</label>
                                <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "999-999-999"' data-mask>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                                <select class="form-select" name="cust_id_type">
                                    @foreach($custids as $cid)
                                    @if($cid['id'] == 6)
                                    <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                    @else
                                    <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.id_number')}}</label>
                                <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="{{ url('customers') }}" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body" id="item-list">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">@if($settings->is_cm_business) Active Riders @else Active Customers  @endif</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_3">@if($settings->is_cm_business)In Active  Riders @else In Active Customers @endif</a></li>
                        @if(!$settings->is_cm_business)
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_4">{{trans('navmenu.import_customers')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('export-customers') }}">{{trans('navmenu.export_customers')}}</a></li>
                        @endif
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="custTable" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>@if($settings->is_cm_business) Rider Name @else {{trans('navmenu.customer_name')}} @endif</th>
                                            <th>{{trans('navmenu.phone_number')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <form id="frm-example" action="{{url('delete-multiple-customers')}}" method="POST">
                                @csrf
                                <button id="submitDel" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="tab_3" role="tabpanel">
                            <div class="table-responsive">
                                <table id="in-active" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>@if($settings->is_cm_business) Rider Name @else {{trans('navmenu.customer_name')}} @endif</th>
                                            <th>{{trans('navmenu.phone_number')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactivecustomers as $key => $customer)
                                        <tr>
                                            <td>{{$key}}</td>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->phone}}</td>
                                            <td>{{$customer->time_created}}</td>
                                            <td>
                                                <a href="{{ url('activate-customer/'.encrypt($customer->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>Activate Customer
                                                </a> | 
                                                <form method="POST" action="{{route('customers.destroy' , encrypt($customer->id))}}" id="delete-inact-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteInActive({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>  
                        <div class="tab-pane fade" id="tab_4" role="tabpanel">
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{url('import-customer')}}"  enctype="multipart/form-data">
                                @csrf
                                <div class="col-sm-6">
                                    <h3>Instruction to Import Customers</h3>
                                    <p>Please download the sample excel file below then use it to create your customers list excel file then Save it to your PC.</p>

                                    <p>Then Click  Browse to fetch your file then click Upload to import.</p>
                                </div>
                                <div class="col-sm-6">
                                    <h3>Download Sample Excel file</h3>
                                    <a href="{{url('excel-sample-customers')}}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Download</a>
                                    <br><br>
                                    <div class="form-group">
                                        <label for="exampleInputFile" class="form-label">Choose Customers excel file</label>
                                        <input type="file" class="form-control" id="exampleInputFile" name="file" required>
                                        @if ($errors->has('file'))
                                        <span class="help-block" style="color: red;">
                                            <strong>{{ $errors->first('file') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <button type="submit" class="btn btn btn-success btn-sm"><i class="fa fa-upload"></i> Upload</button>
                                        <a href="{{ url('customers') }}" type="button" class="btn btn-warning btn-sm">
                                            <i class="fa fa-x"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('side/assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var userlang = "<?php echo app()->getLocale(); ?>";
        var languageUrl = "";
        if (userlang === 'en') {
            languageUrl = "side/assets/vendor/libs/English.json";
        } else {
            languageUrl = "side/assets/vendor/libs/Swahili.json";
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // DataTable
        var custtable = $('#custTable').DataTable({
            language: {
                url: languageUrl
            },
            'columnDefs': [{
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            }],
            'select': {
                'style': 'multi'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('customers.getShopCustomers') }}",
                type: 'POST'
            },
            columns: [
                {
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'phone'
                },
                {
                    data: "date"
                },
                {
                    data: 'action'
                }
            ]
        });

        var counterCheckedProd = 0;
        $('#submitDel').prop("disabled", true);

        $('body').on('change', 'input[type="checkbox"]', function() {
            this.checked ? counterCheckedProd++ : counterCheckedProd--;
            counterCheckedProd > 0 ? $('#submitDel').prop("disabled", false) : $('#submitDel').prop(
                "disabled", true);
        });

        $('#submitDel').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "{{ trans('navmenu.are_you_sure_delete') }}",
                text: "{{ trans('navmenu.no_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                cancelButtonText: "{{ trans('navmenu.no') }}"
            }).then((result) => {
                if (result.value) {
                    $('#frm-example').submit();
                    Swal.fire(
                        "{{ trans('navmenu.deleted') }}",
                        "{{ trans('navmenu.cancelled') }}",
                        'success'
                    )
                }
            })
        })

        // Handle form submission event 
        $('#frm-example').on('submit', function(e) {
            var form = this;
            var rows_selected = custtable.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId) {
                // Create a hidden element 
                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'ids[]')
                    .val(rowId)
                );
            });
        });
    });
</script>