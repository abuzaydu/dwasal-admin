@extends('layouts.app')
    <script type="text/javascript">
        function confirmApproval(id) {
            Swal.fire({
                title: 'Are you sure to approve the requested refund Cash?',
                showDenyButton: true,
                confirmButtonText: 'Yes Approve',
                denyButtonText: `Don't Approve`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'approve-refund/'+id;
                    Swal.fire('Approved!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Approved', '', 'info')
                }
            })
        }

        function rejectForm(elm) {
            var rej = document.getElementById('reject-form');
            if (elm == 'Show') {
                rej.style.display = 'block';
            }else{
                rej.style.display = 'none';
            }
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row g-3">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table table-striped display nowrap" style="width: 100%; font-size: 14px;">
                                <tbody>
                                    <tr>
                                        <th>Request Date</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($refund->created_at)) }}</td>
                                        <th>Amount</th>
                                        <td>{{$refund->refund_amt}}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved At</th>
                                        <td>@if(!is_null($refund->approved_time)){{ date('d M Y H:i:s', strtotime($refund->approved_time)) }}@endif</td>
                                        <th>Approved By</th>
                                        <td>{{$refund->approved_by}}</td>
                                    </tr>
                                    <tr>
                                        <th>Refunded Date</th>
                                        <td>@if(!is_null($refund->confirm_time)){{ date('d M Y H:i:s', strtotime($refund->confirm_time)) }}@endif</td>
                                        <td>Refunded By</td>
                                        <td>{{$refund->confirmed_by}}</td>
                                    </tr>
                                    @if(!is_null($account))
                                    <tr>
                                        <th>Refunded From</th>
                                        <td>{{ $account->account_name }} {{$account->account_number}}</td>
                                        <th>Status </th>
                                        <td>{{$refund->status}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Remarks</th>
                                        <td colspan="3">{!! $refund->remarks !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        @if(is_null($refund->approved_time))
                        <a href="{{ route('refund-requests.edit', encrypt($refund->id))}}" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        @if(Auth::user()->can('approve-refund'))
                        <a href="javascript:;" onclick="rejectForm('Show')" class="btn btn-sm btn-warning btn-sm"><i class="fa fa-check"></i> Reject</a>
                        <button type="button" onclick="confirmApproval('<?php echo encrypt($refund->id); ?>')" class="btn btn-sm btn-primary btn-sm"><i class="fa fa-check"></i> Approve</button>
                        @endif
                        @endif
                        @if(!is_null($refund->approved_time) && Auth::user()->can('confirm-refund') && is_null($refund->confirm_time))
                        <a href="#"  class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#refundModal" data-backdrop="static" data-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i> Confirm Refund</a>
                        @endif
                    </div>
                    <div id="reject-form" style="display: none;">
                        <form class="row g-1" action="{{ url('reject-refund') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{$refund->id}}">
                            <div class="col-md-6">
                                <label>Reject Reason <span style="color: red;">*</span></label>
                                <input type="text" name="remarks" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-6 pt-4">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Submit Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Default Size -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('refund-requests.update', encrypt($refund->id)) }}">
                    <div class="modal-body row">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-6">
                            <label class="form-label">Amount <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" value="{{$refund->refund_amt}}" required placeholder="Enter Request Amount" class="form-control form-control-sm mb-3" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Refunded from <span  style="color: red; font-weight: bold;">*</span> </label>
                            <select name="account_id" class="form-select form-select-sm mb-3" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference Number</label>
                            <input id="name" type="text" name="ref_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection