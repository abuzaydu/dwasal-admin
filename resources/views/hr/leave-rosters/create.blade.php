@extends('layouts.hr')
   <script type="text/javascript">
        function rejectCom(elem) {
            Swal.fire({
                icon: 'error',
                title: 'Oops... Request Rejected!',
                text: elem+'!'
            });
        }

        function approveCom(elem) {
            Swal.fire('Why My Request Changed?', elem, 'question')
        }
   </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('leave-rosters')}}">Request Leave</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body ">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <form id="add-form" method="POST" action="{{route('request-leave')}}" style="display: block;">
                            @csrf
                          <div class="row p-3 border rounded">
                            <div class="col-md-4">
                              <label for="employee" class="form-label">Leave Type </label>
                              <select class="form-select form-select-sm" name="type">
                                <option>Casual</option>
                                <option >Sick</option>
                                <option >Maternity</option>
                                <option >Paternity</option>
                                <option>Academic</option>
                                <option>Socail Event</option>
                                <option>Other Emergences</option>
                              </select>
                            </div>

                            <div class="col-md-4">
                                <label  class="form-label">From</label>
                                <div class="input-group date">
                                    <input type="text" name="start_date" id="end-date" class="form-control form-control-sm" placeholder="Pick Start Date">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label  class="form-label">To</label>
                                <div class="input-group date">
                                    <input type="text" name="end_date" id="end-date" class="form-control form-control-sm" placeholder="Pick End Date">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Reason</label>
                                <textarea name="reason" class="form-control form-control-sm" placeholder="Enter Reason"></textarea>
                            </div>
                            <div class="col-md-4 pt-4">
                                <button type="submit" class="btn btn-primary btn-sm">Request</button>
                                <button type="reset" class="btn btn-secondary btn-sm">Cancel</button>
                            </div>
                          </div>
                      </form>
                    </div>
                    <div class="d-lg-flex align-items-center gap-1">
                        <form id="edit-form" method="POST" action="{{route('update-leave')}}" style="display: none;">
                            @csrf
                            <input type="text" name="id" id="id" style="display : none;">
                          <div class="row p-3 mb-0 border rounded">
                            <div class="col-md-4">
                              <label for="employee" class="form-label">Leave Type</label>
                              <select class="form-select form-select-sm mb-1" name="e_type">
                                <option>Casual Leave</option>
                                <option >Sick</option>
                                <option >Maternity</option>
                                <option >Paternity</option>
                                <option>Academic</option>
                                <option>Social Event</option>
                                <option>Other Emergences</option>
                              </select>
                            </div>

                            <div class="col-md-4">
                              <label  class="form-label">From</label>
                              <input type="text" name="e_start_date" id="e_end-date" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                              <label  class="form-label">To</label>
                              <input type="text" name="e_end_date" id="e_end-date" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reason</label>
                                <textarea name="e_reason" class="form-control form-control-sm mb-1"></textarea>
                            </div>
                            <div class="col-md-6 pt-4">
                                <button type="submit" class=" col-md-3  pl-3 btn btn-primary">Request</button>
                                <button onclick="showHideForm('hide')" class="  col-md-3  pl-3 btn btn-secondary">Cancel</button>
                            </div>
                          </div>
                      </form>
                    </div>
                    <div class="p-2 border rounded" style=" overflow-x: auto;" >
                       <div class ="table-responsive">
                        <table class="table  table-hover align-middle mb-0 card-table " style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Leave Type</th>
                                    <th>Reason</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Approved By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leave_rosters as $key => $leave_roster)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $leave_roster->type }}</td>
                                    <td>{{$leave_roster->reason}}</td>
                                    <td><small class="text-muted">{{Carbon\Carbon::parse($leave_roster->start_date)->format('d-m-Y')}}</small> <b>To</b> <small class="text-muted">{{Carbon\Carbon::parse($leave_roster->end_date)->format('d-m-Y')}}</small>
                                    <b>{{Carbon\Carbon::parse($leave_roster->start_date)->diffInDays(Carbon\Carbon::parse($leave_roster->end_date))}}day</b></td>
                                    <td>
                                        @if($leave_roster->status == 'Rejected')
                                        {{$leave_roster->status}} <a href="javascript:;" onclick="rejectCom('<?php echo $leave_roster->reject_reason; ?>')" class="text-danger">View Comments</a>
                                        @else
                                        {{$leave_roster->status}}
                                        @if(!is_null($leave_roster->approve_comments))
                                        <a href="javascript:;" onclick="approveCom('<?php echo $leave_roster->approve_comments; ?>')" class="text-warning">View Comments</a>
                                        @endif
                                        @endif
                                    </td>
                                    <td>{{$leave_roster->created_at}}</td>
                                    <td>{{ $leave_roster->fname }} {{$leave_roster->mname}} ({{$leave_roster->name}})</td>
                                    <td> 
                                        @if($leave_roster->status == 'Awaiting for Approval')
                                         <a onclick="EditRequest({{$leave_roster}})" title="edit" ><i class='fa fa-pencil mr-1'></i></a>
                                        |
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('leave-rosters.destroy', encrypt($leave_roster->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger" title="delete" ><i class='fa fa-trash'></i></a>
                                        </form>
                                        @endif
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
    </div>
@endsection

@section('page-scripts')
<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
    <script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var start = document.querySelector('[name="start_date"]');
            var end = document.querySelector('[name="end_date"]');
            var e_start = document.querySelector('[name="e_start_date"]');
            var e_end = document.querySelector('[name="e_end_date"]');

             start.DatePickerX.init({
                mondayFirst: true,
                minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
             
            end.DatePickerX.init({
                mondayFirst: true,
                minDate    : new Date(),
                format     : 'yyyy-mm-dd',

                // maxDate    : new Date()
            });

             e_start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',

                // maxDate    : new Date()
            });

            e_end.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',

                // maxDate    : new Date()
            });
        });
    </script>
<script type="text/javascript">
    
    function confirmDelete(id) {
        Swal.fire({
            title: "Sure you want to delete ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "delete",
            cancelButtonText: "cancel"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "Deleted",
                    'success'
                )
            }else{
                Swal.fire(
                    "Cancelled",
                    'success'
                )
            }
        })
    }

    function EditRequest(data) {

        document.getElementById('add-form').style.display = 'none';
        document.getElementById('edit-form').style.display = 'block';

        document.getElementById('id').value = data.id;
        document.getElementsByName('e_type')[0].value = data.type;
        document.getElementsByName('e_start_date')[0].value = data.start_date;
        document.getElementsByName('e_end_date')[0].value = data.end_date;
        document.getElementsByName('e_reason')[0].value = data.reason;
    }

</script>
@endsection