@extends('layouts.prod')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
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
            document.getElementById('delete-form-' + id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
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
            b.style.display = 'none';
            ca.style.display ='none';
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


</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('moh-costs') }}">MOH Costs</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-5 col-md-5 col-sm-12 text-right pt-0">
                <form class="dashform row" action="{{ url('f-moh-payments') }}" method="POST">
                @csrf
                
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="float-sm-end">
                      <div class="input-group">
                          <button type="button" class="btn btn-white btn-sm" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-new2">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{$title}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_1-2" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-download font-18 me-1'></i>
                                </div>
                                <div class="tab-title">Export {{$title}}</div>
                            </div>
                        </a>
                    </li>
                    <a class="btn btn-success btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#mohPayModal"><i class="fa fa-plus"></i> New Payment</a>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.pay_mode')}}</th>
                                        <th>{{trans('navmenu.user')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($moh_payments as $index => $mohpay)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$mohpay->pay_date}}</td>
                                        <td>{{ number_format($mohpay->amount)}}</td>
                                        <td>{{$mohpay->pay_mode}}</td>
                                        <td>{{$mohpay->first_name}}</td>
                                        <td>
                                            <a href="{{route('moh-payments.edit', encrypt($mohpay->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('moh-payments.destroy' , encrypt($mohpay->id))}}" style="display:inline;">
                                                 @csrf
                                                 @method('DELETE')
                                                 <a href="#" onclick="confirmDelete('{{$index}}')">
                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                </a> 
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-mohs')}}" method="POST">  @csrf <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div> 
                    </div>

                    <div class="tab-pane fade" id="tab_1-2" role="tabpanel">
                        <div class="table-responsive">
                            <table id="export-payments" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.pay_mode')}}</th>
                                        <th>{{trans('navmenu.user')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($moh_payments as $index => $mohpay)
                                    <tr>
                                        <td>{{ date('d/m/Y H:i:s', strtotime($mohpay->pay_date)) }}</td>
                                        <td>{{ number_format($mohpay->amount)}}</td>
                                        <td>{{$mohpay->pay_mode}}</td>
                                        <td>{{$mohpay->first_name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mohPayModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">New MOH Cost Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-1 form" method="POST" action="{{route('moh-payments.store')}}">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                    </div>                   
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
                            <option value="">Petty Cash</option>
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
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.comments')}}</label>
                        <textarea class="form-control form-control-sm mb-3" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                    </div>
                    <div class="modal-footer">
                        <div class="float-start">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                </div>
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

            var deltable = $('#del-oe-multiple').DataTable({
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


            var shop_name = "<?php echo $shop->name; ?>";
            var duration = "<?php echo $duration; ?>";
            var stmttable = $('#export-payments').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Overhead Expense Payments_" + duration,
                        title: "Overhead Expense Payments",
                        messageTop: 'DATE : ' + duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Overhead Expense Payments_" + duration,
                        title: shop_name + "\n Overhead Expense Payments \n Date : " + duration,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                    }
                ],
            });
            stmttable.buttons().container().appendTo('#export-payments_wrapper .col-md-6:eq(1)');

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
