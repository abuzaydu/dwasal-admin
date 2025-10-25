@extends('layouts.inv')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
     function weg(elem) {
        var x = document.getElementById("select_invoice");
        if(elem.value !== "old") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#invoice_no").val('');
        }
    }

    
    var currency = '';
    function wegCurr(elem) {
        var defc = "<?php echo $defcurr; ?>";
        var rateMode = document.getElementById('ex-rate-mode');
        var rateModeCol = document.getElementById('rate-mode-col');
        var locale = document.getElementById('locale');
        if (elem.value != defc) {
            currency = elem.value;
            var option1 = document.createElement("option");
            option1.value = 'locale';
            option1.text = "1 "+defc+" Equals ? "+currency;
            rateMode.appendChild(option1);
            var option2 = document.createElement("option");
            option2.value = 'foreign';
            option2.text = "1 "+currency+" Equals ? "+defc;
            rateMode.appendChild(option2);
            rateModeCol.style.display = 'block';
            locale.style.display = 'block';
            document.getElementById('locale-label').innerHTML = 'Rate Amount in '+currency;
        }else{
            rateModeCol.style.display = 'none';
            locale.style.display = 'none';
        }
    }

    function wegRate(exrm) {
        var locale = document.getElementById('locale');
        var foreign = document.getElementById('foreign');
        if (exrm.value == 'locale') {
            locale.style.display = 'block';
            foreign.style.display = 'none';
        }else{
            locale.style.display = 'none';
            foreign.style.display = 'block';
        }
    }

    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');
        var ca = document.getElementById('cashaccount');
        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            ca.style.display = 'none';
            if (elem.value === 'Bank') {
                m.style.display = 'none';
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                m.style.display = 'none';
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            ca.style.display = 'none';
            b.style.display = 'none';
            dpm.style.display = "none";
            slip.style.display = 'none'
            chq.style.display = 'none';
            expire.style.display = "none";
            m.style.display = 'block';
        }else{
            ca.style.display = 'block';
            b.style.display = 'none';
            m.style.display = 'none';
            dpm.style.display = 'none';
            slip.style.display = "none";
            chq.style.display = "none";
            expire.style.display = "none";
        }
    }

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
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

    function confirmDeleteSupplier(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-supplier'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('f-purchases') }}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-5 pt-2">
                        <select name="supplier_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($suppliers as $supplier)
                            @if($currsupp == $supplier->id)
                            <option value="{{$supplier->id}}" selected>{{$supplier->name}}</option>
                            @else
                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <button type="button" class="btn btn-default pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-end px-3 py-3">
                            <ul class="nav nav-tabs nav-tabs-new2" role="tablist"  >
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#purchases" role="tab" aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                            </div>
                                            <div class="tab-title">{{trans('navmenu.purchases')}}</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" href="{{ url('suppliers') }}" role="tab" aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class='fa fa-outline font-18 me-1'></i>
                                            </div>
                                            <div class="tab-title">{{trans('navmenu.supplier_accounts')}}</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" href="{{ route('purchases.create') }}">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-icon"><i class='fa fa-plus-circle font-18 me-1'></i>
                                            </div>
                                            <div class="tab-title">{{trans('navmenu.new_purchase')}}</div>
                                        </div>
                                    </a>
                                </li>
                               
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active table-responsive" id="purchases" role="tabpanel">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.supplier')}}</th>
                                        <th>{{trans('navmenu.grn_no')}}</th>
                                        <th>{{trans('navmenu.invoice_no')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.amount_paid')}}</th>
                                        <th>{{trans('navmenu.unpaid')}}</th>
                                        <th>Total Additional Cost</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.user')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $index => $purchase)
                                    <tr>
                                        <td>{{$purchase->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($purchase->time_created))}}</td>
                                        <td><a href="{{url('purchase-items/'.encrypt($purchase->id))}}">{{$purchase->name}}</a></td>
                                        <td><a href="{{ route('purchases.show', encrypt($purchase->id))}}">{{ sprintf('%04d', $purchase->grn_no)}}</a></td>
                                        <td>{{$purchase->invoice_no}}</td>
                                        <td>{{number_format($purchase->total_amount, 2,'.', ',')}}</td>
                                        <td>{{number_format($purchase->amount_paid, 2,'.', ',')}}</td>
                                        <td>{{number_format($purchase->total_amount-$purchase->amount_paid, 2,'.', ',')}}</td>
                                        <td>{{number_format($purchase->total_cost, 2,'.', ',')}}</td>
                                        <td>{{$purchase->created_at}}</td>
                                        <td>{{$purchase->user}}</td>
                                        <td>
                                            <a href="{{route('purchases.show' , encrypt($purchase->id))}}"><i class="bx  bx-show-alt"></i></a> | 
                                            @if($purchase->amount_paid < $purchase->total_amount)
                                            <a href="#" onclick="showModal('<?php echo $purchase->id; ?>')" data-id="{{$purchase->id}}"><i class="fa fa-money"></i></a> |@endif 
                                           @if(Auth::user()->can('edit-purchase')) <a href="{{route('purchases.edit' , encrypt($purchase->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a> |@endif
                                            @if(Auth::user()->can('delete-purchase'))
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('purchases.destroy' , encrypt($purchase->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>
                                            @endif 
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(Auth::user()->can('delete-purchase'))
                            <form id="frm-example" action="{{url('delete-multiple-purchases')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger ">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="supplier_accounts" role="tabpanel">
                            <table id="example2" class="table align-middle mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.supplier_name')}}</th>
                                        <th>{{trans('navmenu.contact_number')}}.</th>
                                        <th>{{trans('navmenu.email_address')}}</th>
                                        <th>{{trans('navmenu.address')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $i => $supplier)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td><a href="{{ route('suppliers.show', encrypt($supplier->id)) }}">{{$supplier->name}}</a></td>
                                        <td>{{$supplier->contact_no}}</td>
                                        <td>{{$supplier->email}}</td>
                                        <td>{{ $supplier->address}}</td>
                                        <td>{{$supplier->created_at}}</td>
                                        <td>
                                            <a href="{{route('suppliers.edit' , encrypt($supplier->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            |
                                            <form id="delete-form-supplier-{{$i}}" method="POST" action="{{route('suppliers.destroy' , encrypt($supplier->id))}}" style="display: inline;"> 
                                             @csrf
                                             @method('DELETE')
                                             <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteSupplier({{$i}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>  
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
        </div>
    </div>

    <!-- Modal -->  
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_payment')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('supplier-acc-payments')}}">
                @csrf
                <div class="modal-body row">
                    <input type="hidden" name="purchase_id" id="id_hide">

                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>                         
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                        </div>
                        @if($settings->allow_multi_currency)
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-3" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-3" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-3" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                        @endif
                            
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-3" id="cashaccount">
                            <label class="form-label">Cash Account </label>
                            <select class="form-select form-select-sm mb-1" name="cash_acc_id"> 
                                @foreach($accounts->where('type', 'Cash') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="deposit_mode" style="display: none;">
                            <label class="form-label">Deposit Mode</label>
                            <select name="deposit_mode" class="form-select form-select-sm mb-3">
                                <option>Direct Deposit</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="bankdetail" style="display: none;">
                            <label class="form-label">Bank Account </label>
                            <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Bank') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>                          
                        </div>

                        <div class="col-md-3" id="cheque" style="display: none;">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-3" id="expire" style="display: none;">
                            <label class="form-label">Expire Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div> 
                                <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-3">
                            </div>
                        </div>

                        <div class="col-md-3" id="slip" style="display: none;">
                            <label class="form-label">Bank Slip Number</label>
                            <input id="name" type="text" name="slip_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-3" id="mobaccount" style="display: none;">
                            <label class="form-label">Mobile Money Account </label>
                            <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-3" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            var table = $('#example2').DataTable({
                'scrollX': true,
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');


            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
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
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
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
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
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
                }
            });
        });
    </script>
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function()
    {
        var $min = document.querySelector('[name="pay_date"]');
        $min.DatePickerX.init({
            mondayFirst: true,
            // minDate    : new Date(),
            format     : 'yyyy-mm-dd',
            maxDate    : new Date()
        });
    });

</script>