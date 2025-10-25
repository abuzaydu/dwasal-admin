@extends('layouts.hr')
   <script type="text/javascript">
     function showBalances() {
       var roster = document.getElementById('leave-rosters');
       var balance = document.getElementById('leave-balances');
       roster.style.display = 'none';
       balance.style.display = 'block';
     }
   </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('leave-rosters')}}">Leave Rosters</a></li>
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
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;"></h6>
                            <h6 class="mb-0 text-uppercase" id="list-title">Leave Rosters</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" onclick="showBalances()" class="btn btn-warning btn-sm"> Employees CausalLeave Balance</button>
                            <button type="button" id="new-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveModal" ><i class="bx bxs-plus-square"></i>Add Leave</button>
                        </div>
                    </div>
                    <div class="p-2 border rounded" id="leave-rosters" style=" overflow-x: auto;" >
                      <div class ="table-responsive">
                        <table id="emp-leaves" class="table  table-hover align-middle mb-0 card-table " style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                    <th>Reason</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    {{--<th>Approved By</th>--}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leave_rosters as $key => $leave_roster)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $leave_roster->fname }} {{$leave_roster->mname}} {{$leave_roster->lname}}</td>
                                    <td>{{$leave_roster->name}}</td>
                                    <td>{{ $leave_roster->type }} ({{$leave_roster->reason}})</td>
                                    <td><small class="text-muted">{{Carbon\Carbon::parse($leave_roster->start_date)->format('d-m-Y')}}</small> <b>To</b> <small class="text-muted">{{Carbon\Carbon::parse($leave_roster->end_date)->format('d-m-Y')}}</small>
                                    <b>{{Carbon\Carbon::parse($leave_roster->start_date)->diffInDays(Carbon\Carbon::parse($leave_roster->end_date))}}day</b></td>
                                    <td>{{$leave_roster->status}}</td>
                                    <td>{{$leave_roster->created_at}}</td>
                                    {{--<td>{{$leave_roster->approved_by}}</td>--}}
                                    <td>
                                        @if($leave_roster->status == 'Awaiting for Approval')
                                        <a href="{{ url('leave-approve', encrypt($leave_roster->id)) }}" class="text-secondary" title="approve" ><i class='fa fa-check'></i> Approve</a> |  
                                        <a href="{{ route('leave-rosters.edit', encrypt($leave_roster->id)) }}" title="edit" ><i class='fa fa-pencil mr-1'></i> Approve With Update</a> | <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#rejectLeaveModal" onclick="document.getElementById('leave_id').value = {{$leave_roster->id}};" title="reject"  class="text-warning"><i class='fa fa-times'></i> Reject</a> | 
                                        @endif
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('leave-rosters.destroy', encrypt($leave_roster->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger" title="delete" ><i class='fa fa-trash'></i> Delete</a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="p-2 border rounded" id="leave-balances" style="display: none;">
                      <div class ="table-responsive">
                        <table class="table  table-hover align-middle mb-0 card-table " style="width:100%">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Year</th>
                                    <th>Causal Days</th>
                                    <th>Attended</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lbalances as $bal)
                                <tr>
                                    <td>{{$bal->fname}} {{$bal->lname}}</td>
                                    <td>{{$bal->year}}</td>
                                    <td>{{$bal->c_days}}</td>
                                    <td>{{$bal->used}}</td>
                                    <td>{{$bal->c_days-$bal->used}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addLeaveModal" tabindex="-1" aria-labelledby="addLeaveModal" aria-hidden="false">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Leave</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="basic-form-2" method="POST" action="{{route('leave-rosters.store')}}">
              @csrf
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="employee" class="form-label">Employee</label>
                  <select class="form-select form-select-sm select2" name="employee" required ><option value="">---Select---</option>
                    @foreach($employees as $employee)
                      <option value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="employee" class="form-label">Leave Type</label>
                  <select class="form-select form-select-sm" name="type">
                    <option >Sick</option>
                    <option >Maternity</option>
                    <option >Paternity</option>
                    <option>Academic</option>
                    <option>Socail Event</option>
                    <option>Other Emergences</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label  class="form-label">From</label>
                  <input type="text" name="start_date"  id="start_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mb-3">
                  <label  class="form-label">To</label>
                  <input type="text" name="end_date"  id="end_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="file" class="form-control form-control-sm">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control form-control-sm"></textarea>
                </div>
              </div>
          </div>
            <div class="modal-footer">
              <button type="button" onclick="submit()" class="btn btn-primary">Save</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
           </form>
        </div>
      </div>
    </div>
    <div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-labelledby="addLeaveModal" aria-hidden="false">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Leave rejection reason</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="basic-form" method="POST" action="{{route('leave-reject')}}">
              @csrf
              <div class="row">
                <input type="text" id="leave_id" name="id" hidden >
                <div class="col-md-12 mb-3">
                    <textarea name="reason" class="form-control form-control-sm" placeholder="Enter rejection reason" required></textarea>
                </div>
              </div>
          </div>
            <div class="modal-footer">
              <button type="button" onclick="submit()" class="btn btn-primary">Save</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
           </form>
        </div>
      </div>
    </div>
@endsection

@section('page-scripts')
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var $start = document.querySelector('[name="start_date"]');
            var $end = document.querySelector('[name="end_date"]');

             $start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
             
            $end.DatePickerX.init({
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

</script>
@endsection