@extends('layouts.hr')
   
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
                        <form id="add-form" method="POST" action="{{route('leave-rosters.update' , encrypt($leave_roster->id))}}">
                            @csrf
                            @method('PUT')
                          <div class="row p-3 mb-3 border rounded">
                            <div class="col-md-4 mb-3">
                              <label for="employee" class="form-label">Leave Type</label>
                              <select class="form-select form-select-sm" value="{{$leave_roster->type}}" name="type">
                                <option selected>{{$leave_roster->type}}</option>
                                <option >Sick</option>
                                <option >Maternity</option>
                                <option >Paternity</option>
                                <option>Academic</option>
                                <option>Socail Event</option>
                                <option>Other Emergences</option>
                              </select>
                            </div>

                            <div class="col-md-4 mb-3">
                              <label  class="form-label">From</label>
                              <input type="text" name="start_date" id="start-date" class="form-control form-control-sm" value="{{$leave_roster->start_date}}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                              <label  class="form-label">To</label>
                              <input type="text" name="end_date" id="end-date" class="form-control form-control-sm"  value="{{$leave_roster->end_date}}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reason</label>
                                <textarea name="reason" class="form-control form-control-sm" readonly>{{$leave_roster->reason}}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Approval Comments</label><textarea name="approve_comments" class="form-control form-control-sm" placeholder="Please enter comments for changes on request"></textarea>
                            </div>
                            <div class="col-md-12 mb-3 pt-4">
                                <button type="submit" class=" col-md-3  pl-3 btn btn-primary">Apply Changes And Approve</button>
                                <a href="{{ url('leave-rosters') }}" class="col-md-3  pl-3 btn btn-secondary">Cancel</a>
                            </div>
                          </div>
                      </form>
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

             start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
             
            end.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });

      
        });
    </script>
@endsection