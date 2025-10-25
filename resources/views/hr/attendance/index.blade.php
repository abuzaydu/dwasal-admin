@extends('layouts.hr')
   
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('attendance')}}">Employee Attendance</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-xl-12 mx-auto mb-3 ">
        <div class="row">
        <form class="col-md-6 float-start"  id="filter" method="POST" action="{{url('hr-attendance')}}">
          @csrf
            <div class="col-md-6">
              <select class="form-select form-select-sm mb-1" name="month" onchange="this.form.submit()">
                @foreach($data as $d)
                @if($d['month'].' '.$d['year'] == $curmonth)
                <option selected value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                @else
                <option value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                @endif
                @endforeach
              </select>
            </div>
        </form>
        <div class="col-md-6">
            <a href="{{url('attendance-setting')}}" class="col-md-3 btn btn-primary  float-end"> Settings</a>
        </div>
      </div>
      </div>  
      <div class="col-xl-12 mx-auto">
        <div class="card">
          <div class="card-body">
            <div class="float-start">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#punchInModal">PunchIn</button>
              <button  class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#punchOutModal">PunchOut</button>
            </div>
            <div class="card-title pt-3 float-end">
                <span class="px-1"><i class="fa fa-check text-success"></i> = Full Day Present</span>
                <span class="px-1"><i class="fa fa-exclamation text-muted"></i> = Half Day </span>
                <span class="px-1" ><i class="text-info">S</i> = Weekend </span>
                <span class="px-1" ><small style="color: lightcoral;">H </small> = Holiday</span>
                <span class="px-1"><i class="fa fa-clock-o text-warning"></i> = Came Late</span>
                <span class="px-1"><i class="fa fa-clock-o" style="color: brown;"></i> = Left earlier</span>
                <span class="px-1"><i class="fa fa-times text-danger"></i> = Absence</span>
            </div>
            <div id="table-responsive">
              <table class="table align-middle mb-0 card-table display no-wrap" style="width: 100%; white-space: nowrap; display: block; overflow-x: auto;">
                <thead class="mb-3">
                  <th>S/no</th>
                  <th>Employee Name</th>
                  @for($i = 1; $i < 32; $i++)
                    <th style="text-align: center;">{{ $i }}</th>
                  @endfor
                </thead>
                <tbody>
                  @foreach($attendance as $key => $value)
                  <tr>
                    <td style="text-align: center;">{{$key+1}}</td>
                    <td>{{$value[0]['fname']}} {{$value[0]['lname']}}</td>
                      @for($i=0 ; $i < 31 ; $i++)

                          @if(!$value[$i]['is_null'])
                            @if($value[$i]['is_present'])
                              @if($value[$i]['is_fullday'])
                                @if($value[$i]['is_late'])
                                <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})"><i class="fa fa-clock-o" style="color: gold;"></i></a></td>
                                @else
                                <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})"><i class="fa fa-check" style="color: green;"></i></a></td>
                                @endif
                              @else
                                @if($value[$i]['is_late'])
                                <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})"><i class="fa fa-exclamation" style="color: pink;"></i></a></td>
                                @else
                                <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})"><i class="fa fa-clock-o" style="color: brown;"></i> </a></td>
                                @endif
                              @endif
                            @else
                              @if(Carbon\Carbon::parse($value[$i]['created_at'])->isWeekend())
                              <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})" class="text-info" style="font: bold;">S</a></td>
                              @elseif($value[$i]['is_holiday'])
                              <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})" style="color: lightcoral; font: bold;">H </a></td>
                              @else
                              <td style="text-align: center;"><a data-bs-toggle="modal" data-bs-target="#EditModal" data-bs-backdrop="static" data-bs-keyboard="false"  onclick="EditAttendance({{$value[$i]}})" ><i class="fa fa-times" style="color: red;"></i></a></td>
                              @endif
                            @endif
                          @else
                            <td style="text-align: center;"></td>
                          @endif
                      @endfor
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  <div class="modal fade" id="punchInModal" tabindex="-1" aria-labelledby="punchInModal" aria-hidden="false">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Punch In</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="punchin" method="POST" action="{{route('attendance-punchin')}}">
              @csrf
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="employee" class="form-label">Employee</label>
                  <select class="form-select form-select-sm" name="employee[]" multiple required>
                    @foreach($employees as $employee)
                      <option value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Start Of Day</label>
                  <input type="datetime-local" name="start_of_day" id="start-date" class="form-control form-control-sm">
                </div>
              </div>
            </form> 
          </div>
          <div class="modal-footer">
            <button type="button"  onclick="document.getElementById('punchin').submit()" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="punchOutModal" tabindex="-1" aria-labelledby="punchOutModal" aria-hidden="false">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Punch Out</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="punchout" method="POST" action="{{route('attendance-punchout')}}">
              @csrf
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="employee" class="form-label">Employee</label>
                  <select class="form-select form-select-sm" name="employee[]" multiple>
                    @foreach($employees as $employee)
                      <option value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label  class="form-label">End of Day</label>
                  <input type="datetime-local" name="end_of_day" id="end-date" class="form-control form-control-sm">
                </div>
              </div>
          </div>
            <div class="modal-footer">
              <button type="button" onclick="document.getElementById('punchout').submit()" class="btn btn-primary">Save changes</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
           </form>
        </div>
      </div>
    </div>

      <div class="modal fade" id="EditModal" tabindex="-1" aria-labelledby="EditModal" aria-hidden="false">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Attendance</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-form" method="POST" action="{{route('hr-attendance.update' , encrypt(1))}}">
              @csrf
              @method('PUT')
              <input type="text" name="id" id="id" hidden>
              <div class="row">
                <div class="col-md-12 mb-3 align-middle">
                  <div class="row mb-3" align="center"><b><span id="employee-lable"></span></b></div>
                  <div class="row mb-3" align="center"><b><span id="date-time"></span></b></div>
                  <div class="row mb-3" align="center">
                    <span class="col-md-6">Sign In : <b><span id="time-in"></span></b></span>
                    <span class="col-md-6">Sign Out : <b><span  id="time-out"></span></span></b></span>
                  </div>
                  <div class="row mb-3" align="center"><span class="form-label" id="work-hours"></span></div>
                  
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Start Of Day</label>
                  <input type="text" name="e_start_of_day" id="start_time" class="form-control form-control-sm">
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">End of Day</label>
                  <input type="text" name="e_end_of_day" id="end_time" class="form-control form-control-sm">
                </div>
              </div>
            </form> 
          </div>
          <div class="modal-footer">
            <form>
            <button type="button"  onclick="document.getElementById('edit-form').submit()" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
@endsection
@section('page-scripts')

<link rel="stylesheet" href="{{ asset('assets/css/timepicker.min.css') }}">
<style type="text/css">
  #start_time{
    z-index: 100000 !important;
  }
  #end_time{
    z-index: 100000 !important;
  }
</style>
<!-- jQuery -->
<!-- <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script> -->
<!-- Timepicker Js -->
<script src="{{ ('assets/js/timepicker.min.js') }}"></script>

<script>
  $(document).ready(function() {
    $('#EditModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#start_time').timepicker();
    $('#end_time').timepicker();
  });
</script>


<script type="text/javascript">
  function EditAttendance(data){

     var day = new Date(data.created_at);
     var dt = new Date(data.start_of_day);
     var et = new Date(data.end_of_day);
    document.getElementById('employee-lable').innerHTML = data.fname + ' '+ data.lname;
    document.getElementById('date-time').innerHTML = day.toDateString();
    document.getElementById('id').value = data.id;

    if(data.start_of_day != null){ 
      document.getElementsByName('e_start_of_day')[0].value = dt.getHours().toString().padStart(2,'0') +":"+dt.getMinutes().toString().padStart(2,'0');
      document.getElementById('time-in').innerHTML = dt.getHours().toString().padStart(2,'0') +":"+dt.getMinutes().toString().padStart(2,'0');
    }else{

      document.getElementById('time-in').innerHTML = '--:--';
    }

    if(data.end_of_day != null){
      document.getElementById('time-out').innerHTML = et.getHours().toString().padStart(2,'0') +":"+et.getMinutes().toString().padStart(2,'0');
      document.getElementsByName('e_end_of_day')[0].value = et.getHours().toString().padStart(2,'0') +":"+et.getMinutes().toString().padStart(2,'0');
      
    }else{
      document.getElementById('time-out').innerHTML = '--:--';
    } 
  }
</script>

@endsection