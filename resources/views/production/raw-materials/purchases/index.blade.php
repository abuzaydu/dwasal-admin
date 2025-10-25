@extends('layouts.prod')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    var currency = '';
    function wegCurr(elem) {
        var defc = "<?php echo $defcurr;?>";
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

        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
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

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right pt-0">
                <a href="{{ route('rm-purchases.create') }}" class="btn btn-sm btn-primary"> <i class="fa fa-plus"></i> {{ trans('navmenu.purchase_new_rm') }}</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                   <div class="table-responsive">
                       <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%; font-size: 14px;">
                            <thead  style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>S/No</th>
                                    <th>{{trans('navmenu.purchase_date')}}</th>
                                    <th>{{trans('navmenu.supplier')}}</th>
                                    @if($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4)
                                    <th>{{trans('navmenu.grn_no')}}</th>
                                    @endif
                                    <th>{{trans('navmenu.amount')}}</th>
                                    <th>{{trans('navmenu.amount_paid')}}</th>
                                    <th>{{trans('navmenu.unpaid')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rmpurchases as $index => $rmpurchase)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{date('d-m-Y', strtotime($rmpurchase->date))}}</td>
                                    <td><a href="{{route('rm-purchases.show', encrypt($rmpurchase->id))}}">{{$rmpurchase->name}}</a></td>
                                    @if($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4)
                                    <td><a href="{{route('rm-purchase-grn', encrypt($rmpurchase->id))}}">{{ sprintf('%04d', $rmpurchase->grn_no)}}</a></td>
                                    @endif
                                    <td>{{number_format($rmpurchase->total_amount)}}</td>
                                    <td>{{number_format($rmpurchase->amount_paid)}}</td>
                                    <td>{{number_format($rmpurchase->total_amount-$rmpurchase->amount_paid)}}</td>
                                    <td>{{$rmpurchase->created_at}}</td>
                                    <td>
                                        @if($shop->subscription_type_id == 2)
                                        <a href="{{route('rm-purchases.show', encrypt($rmpurchase->id))}}">
                                            <span class="lni lni-eye"></span>
                                        </a> | @endif 
                                        @if($rmpurchase->amount_paid < $rmpurchase->total_amount)
                                        <!-- <a href="#" onclick="showModal('<?php echo $rmpurchase->id; ?>')" data-id="{{$rmpurchase->id}}"><i class="fa fa-money"></i></a> | -->
                                         @endif
                                        <a href="{{route('rm-purchases.edit', encrypt($rmpurchase->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form id="delete-form-{{$index}}" method="POST" action="{{route('rm-purchases.destroy' , encrypt($rmpurchase->id))}}" style="display: inline;">
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
                        <form id="frm-example" action="{{url('delete-multiple-rm-purchases')}}" method="POST">
                            @csrf
                            <!-- <button id="submitButton" class="btn btn-danger ">{{trans('navmenu.delete_selected')}}</button> -->
                        </form>
                   </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->  
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_payment')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{route('rm-purchase-payments.store')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="rm_purchase_id" id="id_hide">
                            <div class="form-group col-md-4">
                                <label>{{trans('navmenu.pay_date')}}</label>
                                <div class="input-group date">
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar "></i>
                                    </div>                                
                                    <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-select-sm mb-3" required>
                                        
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                           @if($settings->allow_multi_currency)
                                <div class="col-md-4">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-3" onchange="wegCurr(this)" required>
                                        @foreach($currencies as $curr)
                                        <option>{{$curr->code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4" id="rate-mode-col" style="display: none;">
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-3" onchange="wegRate(this)">
                                    </select>
                                </div>
                                <div class="col-md-4" id="locale" style="display: none;">
                                    <label class="form-label" id="locale-label"></label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-md-4" id="foreign" style="display: none;">
                                    <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3">
                                </div>
                            @endif
                            
                          {{--  @if($shop->subscription_type_id ==3 || $shop->subscription_type_id ==4)
                            <div id="bankdetail" style="display: none;">
                                <div class="form-group col-md-4" id="deposit_mode" style="display: none;">
                                    <label class="form-label">Deposit Mode</label>
                                    <select name="deposit_mode" class="form-control form-select-sm mb-3">
                                        <option>Direct Deposit</option>
                                        <option>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Bank Name </label>
                                    <select name="bank_name" class="form-control form-select-sm mb-3">
                                        @foreach($bdetails as $detail)
                                        <option>{{$detail->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="form-label">Bank Branch </label>
                                    <input id="name" type="text" name="bank_branch" placeholder="Please enter Bank Branch" class="form-control form-select-sm mb-3">
                                </div>

                                <div class="form-group col-md-4" id="cheque" style="display: none;">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-select-sm mb-3">
                                </div>

                                <div class="form-group col-md-4" id="expire" style="display: none;">
                                    <label class="form-label">Expire Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div> 
                                        <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-select-sm mb-3">
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="slip" style="display: none;">
                                    <label class="form-label">Credit Card/Bank Slip Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control form-select-sm mb-3">
                                </div>
                            </div>
                            <div id="mobaccount" style="display: none;">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Mobile Money Operator </label>
                                    <select class="form-control form-select-sm mb-3" name="operator">
                                        <option>AirtelMoney</option>
                                        <option>EzyPesa</option>
                                        <option>M-Pesa</option>
                                        <option>TigoPesa</option>
                                        <option>HaloPesa</option>
                                    </select>
                                </div>
                            </div>
                            @endif --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
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


