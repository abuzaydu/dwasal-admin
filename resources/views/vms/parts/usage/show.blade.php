@extends('layouts.vms')
@section('content')
    <script type="text/javascript">
        function confirmApproval(id) {
            Swal.fire({
                title: 'Are you sure you want to approve the request?',
                showDenyButton: true,
                confirmButtonText: 'Yes Approve',
                denyButtonText: `Don't Approve`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'approve-part-usage/'+id;
                    Swal.fire('Approved!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Approved', '', 'info')
                }
            })
        }

        function confirmClose(id) {
            Swal.fire({
                title: 'Are you sure you want to close this request?',
                showDenyButton: true,
                confirmButtonText: 'Yes close',
                denyButtonText: `Don't close`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'close-part-usage/'+id;
                    Swal.fire('Closed!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Closed', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a  href="{{ url('parts-usage') }}">Part Usage</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
               
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
                                        <th>{{trans('navmenu.date')}}</th>
                                        <td>{{date('d-m-Y', strtotime($pusage->pu_date))}}</td>
                                        <th>Code</th>
                                        <td>{{ $pusage->pu_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vehicle</th>
                                        <td>{{$pusage->plate_no}} {{$pusage->vehicle_name}}</td>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <td>{{$pusage->status}}</td>
                                    </tr>
                                    <tr>
                                        <th>Requested By</th>
                                        <td>{{$pusage->first_name}} {{$pusage->last_name}}</td>
                                        <th>Last Updated At</th>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pusage->updated_at)->diffForHumans() }} </td>
                                    </tr>
                                    <tr>
                                        <th>Remarks</th>
                                        <td colspan="3">{{$pusage->description}}</td>
                                    </tr>

                                    @if($pusage->status == 'Rejected')
                                    <tr>
                                        <th>Rejected By</th>
                                        <td>{{$pusage->approved_by}}</td>
                                        <th>Rejected At</th>
                                        <td>@if(!is_null($pusage->approved_at)){{ date('d M Y H:i:s', strtotime($pusage->approved_at)) }}@endif</td>
                                    </tr>
                                    <tr>
                                        <th>Reject Reason</th>
                                        <td colspan="3"><span class="text-danger">{{$pusage->reject_reason}}</span></td>
                                    </tr>
                                    @else
                                    @if($pusage->is_approved)
                                    <tr>
                                        <th>Approved By</th>
                                        <td>{{$pusage->approved_by}}</td>
                                        <th>Approved At</th>
                                        <td>@if(!is_null($pusage->approved_at)){{ date('d M Y H:i:s', strtotime($pusage->approved_at)) }}@endif</td>
                                    </tr>
                                    @if($pusage->status == 'Closed')
                                    <tr>
                                        <th>Closed By</th>
                                        <td>{{$pusage->closed_by}}</td>
                                        <th>Closed At</th>
                                        <td>@if(!is_null($pusage->closed_at)){{ date('d M Y H:i:s', strtotime($pusage->closed_at)) }}@endif</td>
                                    </tr>
                                    @endif
                                    @endif
                                    @endif
                                </tbody>
                            </table>
                            <hr>
                            <h6>Item List</h6>
                            <table class="table table-striped" style="width: 100%;">
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th>Category</th>
                                    <th style="text-align: left;">Item</th>
                                    <th style="text-align: center;">UOM</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                </tr>
                                @foreach($puitems as $key => $item)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$item->category}}</td>
                                    <td>{{$item->part_no}} {{$item->part_name}}</td>
                                    <td style="text-align: center;">{{$item->uom}}</td>
                                    <td style="text-align: center;">{{$item->pu_qty+0}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        @if($pusage->is_approved)
                        @if($pusage->status != 'Closed')
                        <a href="#"  class="btn btn-success btn-sm" onclick="confirmClose('<?php echo encrypt($pusage->id); ?>')" style="margin-right: 2px;"><i class="fa fa-money"></i> Closed Parts Usage Request</a>
                        @endif
                        @else
                        <a href="{{ route('parts-usage.edit', encrypt($pusage->id))}}" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#rejectModal" class="btn btn-sm btn-warning btn-sm"><i class="fa fa-check"></i> Reject</button>
                        <button type="button" onclick="confirmApproval('<?php echo encrypt($pusage->id); ?>')" class="btn btn-sm btn-primary btn-sm"><i class="fa fa-check"></i> Approve</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="addLeaveModal" aria-hidden="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Part Usage rejection reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="basic-form" method="POST" action="{{ url('reject-part-usage') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" value="{{$pusage->id}}">
                            <div class="col-md-12 mb-3">
                                <textarea name="reject_reason" class="form-control form-control-sm" placeholder="Enter rejection reason" required>{{$pusage->reject_reason}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="submit()" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $( document ).ready(function() {
        inputamt = $("#inputAmount");
        var n = inputamt.val();
        var output = getCommaSeparatedTwoDecimalsNumber(n);
        inputamt.val(output);

        inputamt.on('focus', function(){
            var n = $(this).val();
            let output = parseFloat(n.replace(/,/g, ''));
            $(this).val(output);
        });

        inputamt.on('blur', function(){
            var n = $(this).val();
            var output = getCommaSeparatedTwoDecimalsNumber(n);
            $(this).val(output);
        });
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>