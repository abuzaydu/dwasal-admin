@extends('layouts.hr')
    <script type="text/javascript">
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
            }
        }

        function showHideReForm(elem) {
            var newform = document.getElementById('new-re-form');
            var newbtn = document.getElementById('new-re-btn');
            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
            }
        }

    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr-events')}}">Events</a> </li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <h6 class="mb-2 pt-2 text-uppercase">{{$page}}</h6>
                    <div class="p-4 border rounded">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <td>Event Title</td>
                                    <td>{{$event->title}}</td>
                            
                                    <td>Type</td>
                                    <td>
                                        @if($event->ca_id == 1)
                                        <span class="bg-primary badge">My Calendar</span>
                                        @elseif($event->ca_id == 2)
                                        <span class="bg-secondary badge">Company</span>
                                        @elseif($event->ca_id == 3)
                                        <span class="bg-info badge">My Calendar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Location</td>
                                    <td>{{$event->location}}</td>
                                
                                    <td>Created At</td><td>{{$event->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>Start Date/Time</td>
                                    <td>{{ date('d M, Y H:i', strtotime($event->start))}}</td>
                                    <td>End Date/Time</td>    
                                    <td>{{ date('d M, Y H:i', strtotime($event->end))}}</td>
                                </tr>
                                <tr>
                                    <td>Participants</td>
                                    <td>
                                        @foreach($emembers as $key => $member)
                                        {{$member->name}}<br>
                                        @endforeach
                                    </td>
                                    <td>Prepared By</td>
                                    <td>
                                        {{$event->name}}<br>
                                        {{$event->email}}<br>
                                        {{$event->phone}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class="card">
                <div class="card-body p-2">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="ms-auto">
                            <button type="button" id="new-re-btn" class="btn btn-warning btn-sm float-end mb-1" onclick="showHideReForm('show')" style="margin-left: 5px;"><i class="fa fa-minus-square-o"></i> Remove Participants</button>
                            <button type="button" id="new-btn" class="btn btn-primary btn-sm float-end mb-1" onclick="showHideForm('show')"><i class="fa fa-plus-square"></i> Update Event</button>
                        </div>
                    </div>
                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('hr-events.update', encrypt($event->id)) }}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label for="name" class="form-label">Title <span style="color: red;">*</span></label>
                                <input type="text" name="title" value="{{$event->title}}" class="form-control form-control-sm mb-1" placeholder="Enter Event Title" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" name="location" value="{{$event->location}}" class="form-control form-control-sm mb-3" placeholder="Enter Event Location" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Schedule Category<span class="text-danger">*</span></label>
                                <select name="category" class="form-select form-select-sm mb-1">
                                    <option value="{{$event->category}}" style="text-transform: capitalize;">{{$event->category}}</option>
                                    <option value="allday">All Day</option>
                                    <option value="milestone">Milestone</option>
                                    <option value="task">Task</option>
                                    <option value="time">Time</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Event Start Date/Time </label>
                                <div class="input-group date">
                                    <input type="text" class="form-control form-control-sm mb-1" name="start" value="{{$event->start}}" id="datetimepicker3" data-target="#datetimepicker3" data-toggle="datetimepicker" placeholder="Pick Start Date" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Event End Date/Time </label>
                                <div class="input-group date">
                                    <input type="text" class="form-control form-control-sm mb-1" name="end" value="{{$event->end}}"  id="datetimepicker4" data-target="#datetimepicker4" data-toggle="datetimepicker" placeholder="Pick End Date" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <label class="form-label">Calendar Type <span class="text-danger">*</span></label>
                                <select name="ca_id" class="form-select form-select-sm mb-1" required>
                                    @if($event->ca_id == 1)
                                    <option value="1">My Calendar</option>
                                    <option value="2">Company</option>
                                    <option value="3">National Holidays</option>
                                    @elseif($event->ca_id == 2)
                                    <option value="2">Company</option>
                                    <option value="1">My Calendar</option>
                                    <option value="3">National Holidays</option>
                                    @elseif($event->ca_id == 3)
                                    <option value="3">National Holidays</option>
                                    <option value="1">My Calendar</option>
                                    <option value="2">Company</option>
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">All Users</label>
                                <select class="form-select form-select-sm mb-1" name="members[]" multiple>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select at least one site.</div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Update</button>
                                <a href="javascript:;" onclick="showHideForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <div class="p-4 border rounded" id="new-re-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ url('remove-participants') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="event_id" value="{{$event->id}}">
                            <div class="col-md-12">
                                <label class="form-label">Event Participants <span style="color: red;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="members[]" multiple required>
                                    @foreach($emembers as $member)
                                    <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Remove</button>
                                <a href="javascript:;" onclick="showHideReForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection