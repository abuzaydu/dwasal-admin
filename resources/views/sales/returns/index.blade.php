@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
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
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#manage-returns"><i class='fa fa-list font-18 me-1'></i>Manage Sales Returns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#new-return"><i class='fa fa-export font-18 me-1'></i>New Sales Return</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('refund-requests') }}"><i class='fa fa-list-alt font-18 me-1'></i>Refund Requests</a>
                        </li>
                        
                    </ul>
                    
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="manage-returns" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>#</th>
                                            <th>Return Date</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Discount</th>
                                            @if($settings->is_vat_registered)
                                            <th>Tax Amount</th>
                                            @endif
                                            <th>Created At</th>
                                            <th>Last updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sreturns as $index => $sreturn)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{date('d/m/Y', strtotime($sreturn->return_date))}}</td>
                                            <td><a href="{{ route('sales-returns.show', encrypt($sreturn->id)) }}">{{$sreturn->name}}</a></td>
                                            <td>{{number_format($sreturn->sale_return_amount, 2, '.', ',')}}</td>
                                            <td>{{number_format($sreturn->sale_return_discount, 2, '.', ',')}}</td>
                                            @if($settings->is_vat_registered)
                                            <td>{{number_format($sreturn->return_tax_amount, 2, '.', ',')}}</td>
                                            @endif
                                            <td>{{$sreturn->created_at}}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sreturn->updated_at)->diffForHumans() }}</td>
                                            @if($sreturn->status == 'Paid')
                                            <td></td>
                                            @else
                                            <td>
                                                <a href="{{ route('sales-returns.edit', encrypt($sreturn->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a> |
                                                <form id="delete-form-{{$index}}" method="POST" action="{{ route('sales-returns.destroy', encrypt($sreturn->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="new-return" role="tabpanel">
                            <form class="dashform row g-3" action="{{url('sale-returns')}}" method="POST">
                                @csrf
                                <div class="form-group col-sm-3">
                                    <label class="form-label">Pick Sale Date</label>
                                    <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    </div>
                                </div>
                            </form>
                            <form class="form row g-3" action="{{ route('sales-returns.store') }}" method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <label class="form-label">Select Sale to Refund/Return</label>
                                    <select name="an_sale_id" id="an_sale_id" class="form-select form-select-sm mb-1 select2" onchange='if(this.value != 0) { this.form.submit(); }' required>
                                        <option value=""></option>
                                        @foreach($sales as $sale)
                                        <option value="{{$sale['id']}}">{{$sale['customer']}}({{$sale['date']}}) -> {{$sale['items']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function () {
            $('#example').DataTable();
            $('#creditsales').DataTable();
        });
    </script>
@endsection


    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="sale_date"]');
            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });
        });
    </script>