@extends('layouts.hr')
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr-events')}}">Events Calender</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>
    <div class="row g-3 mb-1">
        <div class="col-md-12 col-sm-12">
            <ul class="nav nav-tabs nav-tabs-new2">
                <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#event-calendar">Event Calendar</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#event-list">Event List</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#new-event">New Schedule</a></li>
            </ul>
        </div>
    </div>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="event-calendar" role="tabpanel">
            <div class="row g-3 row-deck">
                <div class="col-12">
                    <div class="card tui-calendar">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center" id="menu-navi">
                                <div class="d-flex align-items-center my-1">
                                    <button class="btn btn-primary move-today" type="button" data-action="move-today">Today</button>
                                </div>
                                <div class="fs-5 fw-bold my-1" id="renderRange"></div>
                                <div class="d-flex align-items-center my-1">
                                    <div class="dropdown morphing scale-left">
                                        <button class="btn btn-primary dropdown-toggle" id="dropdownMenu-calendarType" type="button" data-bs-toggle="dropdown"><i id="calendarTypeIcon"></i><span class="ms-1" id="calendarTypeName">Dropdown</span></button>
                                        <ul class="dropdown-menu border-0 shadow" role="menu">
                                            <li role="presentation"><a class="dropdown-item dropdown-menu-title" role="menuitem" data-action="toggle-daily"><i class="fa fa-bars me-2"></i>Daily</a></li>
                                            <li role="presentation"><a class="dropdown-item dropdown-menu-title" role="menuitem" data-action="toggle-weekly"><i class="fa fa-th-large me-2"></i>Weekly</a></li>
                                            <li role="presentation"><a class="dropdown-item dropdown-menu-title" role="menuitem" data-action="toggle-monthly"><i class="fa fa-th me-2"></i>Month</a></li>
                                            <li role="presentation"><a class="dropdown-item dropdown-menu-title" role="menuitem" data-action="toggle-weeks2"><i class="fa fa-th-large me-2"></i>2 weeks</a></li>
                                            <li role="presentation"><a class="dropdown-item dropdown-menu-title" role="menuitem" data-action="toggle-weeks3"><i class="fa fa-th-large me-2"></i>3 weeks</a></li>
                                            <li class="dropdown-divider" role="presentation"></li>
                                            <li role="presentation"><a class="dropdown-item" role="menuitem" data-action="toggle-workweek"> <input class="tui-full-calendar-checkbox-square" type="checkbox" value="toggle-workweek" checked=""><span class="checkbox-title"></span>Show weekends</a></li>
                                            <li role="presentation"><a class="dropdown-item" role="menuitem" data-action="toggle-start-day-1"> <input class="tui-full-calendar-checkbox-square" type="checkbox" value="toggle-start-day-1"><span class="checkbox-title"></span>Start Week on Monday</a></li>
                                            <li role="presentation"><a class="dropdown-item" role="menuitem" data-action="toggle-narrow-weekend"> <input class="tui-full-calendar-checkbox-square" type="checkbox" value="toggle-narrow-weekend"><span class="checkbox-title"></span>Narrower than weekdays</a></li>
                                        </ul>
                                    </div>
                                    <div class="ms-2">
                                        <button class="btn btn-outline-secondary move-day" type="button" data-action="move-prev"><i class="fa fa-angle-left" data-action="move-prev"></i></button>
                                        <button class="btn btn-outline-secondary move-day" type="button" data-action="move-next"><i class="fa fa-angle-right" data-action="move-next"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-light px-4 py-2" id="lnb">
                            <div class="d-flex flex-wrap justify-content-between align-items-center" id="lnb-calendars">
                                <div class="d-flex flex-wrap" id="calendarList"></div>
                                <div class="lnb-calendars-item">
                                    <label><input class="tui-full-calendar-checkbox-square" type="checkbox" value="all" checked=""><span></span><strong>View all</strong></label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="border" id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="event-list" role="tabpanel">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="p-4 border rounded">
                                <table id="events" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Event Title</th>
                                            <th>Type</th>
                                            <th>Start Date/Time</th>
                                            <th>End Date/Time</th>
                                            <th>Author</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($events as $key => $event)
                                        <tr>
                                            <td>{{ $key+1}}</td>
                                            <td><a href="{{ route('hr-events.show', encrypt($event->id)) }}"> {{$event->title}}</a></td>
                                            <td>
                                                @if($event->ca_id == 1)
                                                <span class="bg-primary badge">My Calendar</span>
                                                @elseif($event->ca_id == 2)
                                                <span class="bg-secondary badge">Company</span>
                                                @elseif($event->ca_id == 3)
                                                <span class="bg-info badge">My Calendar</span>
                                                @endif
                                            </td>
                                            <td>{{ date('d M, Y H:i', strtotime($event->start))}}</td>
                                            <td>{{ date('d M, Y H:i', strtotime($event->end))}}</td>
                                            <td>{{$event->first_name}} {{$event->last_name}}</td>
                                            <td style="text-align: center;">
                                                <form id="delete-form-{{$key}}" method="POST" action="{{ route('hr-events.destroy', encrypt($event->id))}}" style="display: inline;"> 
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')" title="Delete"><i class='fa fa-trash mr-1'></i></a>
                                                </form>
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

        <div class="tab-pane fade" id="new-event" role="tabpanel">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form class="row g-1" id="basic-form" novalidate method="POST" action="{{ route('hr-events.store') }}">
                                @csrf
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Title <span style="color: red;">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-sm mb-1" placeholder="Enter Event Title" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" name="location" class="form-control form-control-sm mb-3" placeholder="Enter Event Location" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Schedule Category<span class="text-danger">*</span></label>
                                    <select name="category" class="form-select form-select-sm mb-1">
                                        <option value="allday">All Day</option>
                                        <option value="milestone">Milestone</option>
                                        <option value="task">Task</option>
                                        <option value="time">Time</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom02" class="form-label">Event Start Date/Time </label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm mb-1" name="start" id="datetimepicker3" data-target="#datetimepicker3" data-toggle="datetimepicker" placeholder="Pick Start Date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom02" class="form-label">Event End Date/Time </label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm mb-1" name="end"  id="datetimepicker4" data-target="#datetimepicker4" data-toggle="datetimepicker" placeholder="Pick End Date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <label class="form-label">Calendar Type <span class="text-danger">*</span></label>
                                    <select name="ca_id" class="form-select form-select-sm mb-1" required>
                                        <option value="1">My Calendar</option>
                                        <option value="2">Company</option>
                                        <option value="3">National Holidays</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="single-opt">
                                    <label class="form-label">Participants <span style="color: red;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="members[]" multiple>
                                        <option value="">---Select Event Participants--</option>
                                        @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    
                                <div class="col-12">
                                    <button class="btn btn-primary btn-sm" type="submit">Save</button>
                                    <a href="{{ url('hr-events')}}" class="btn btn-warning btn-sm">Cancel</a>
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
<script src="{{asset('hr/assets/js/bundle/hr-events.js')}}"></script>
<script type="text/javascript">
     function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        var events = document.getElementById('event-cal');

        if (elem == 'show') {
            newform.style.display = 'block';
            events.style.display = 'none';
        }else{
            newform.style.display = 'none';
            events.style.display = 'block';
        }
    }


</script>
@endsection