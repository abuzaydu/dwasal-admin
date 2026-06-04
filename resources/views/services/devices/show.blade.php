@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}"
        rel="stylesheet" />
@endsection

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a>
                    </li>
                    <li class="breadcrumb-item active">{{ $title }}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <button type="button" id="new-hourmeter-btn" class="btn btn-success btn-sm" onclick="showHideHourMeterForm('show')">
                    <i class="bx bx-time-five me-1"></i>
                    Add Device Usage Record
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="deviceTabsContent">
                        {{-- TAB 2: HOUR METER RECORDS --}}
                        <div class="tab-pane fade show active" id="hourmeter-panel" role="tabpanel">
                            {{-- Add Hour Meter Form --}}
                            <div class="p-4 border rounded mb-3" id="new-hourmeter-form" style="display: none;">
                                <h6 class="mb-3 fw-semibold">
                                    <i class="bx bx-time-five me-1 text-success"></i>
                                    New Usage Hour Meter Readings
                                </h6>
                                <form class="row g-3" method="POST" action="{{ route('hour-meter.store') }}">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">
                                            @if ($settings->is_cm_business)
                                                Motorbike
                                            @elseif($settings->enable_trip_logs)
                                                Vehicle
                                            @else
                                                Device
                                            @endif
                                            <span class="text-danger fw-bold">*</span>
                                        </label>
                                        <select name="device_id" required class="form-select form-select-sm">
                                            <option value="{{ $device->id }}">{{ $device->device_number }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date <span class="text-danger fw-bold">*</span></label>
                                        <input type="date" name="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Start Hrs <span class="text-danger fw-bold">*</span></label>
                                        <input type="number" name="start_hr" required min="0" step="any" placeholder="e.g. 100" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">End Hrs <span class="text-danger fw-bold">*</span></label>
                                        <input type="number" name="end_hr" required min="0" step="any" placeholder="e.g. 200" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-success btn-sm px-4">
                                            Submit
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm px-4"
                                            onclick="showHideHourMeterForm('hide')">
                                            {{ trans('navmenu.btn_cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table id="hourmeters" class="table table-striped display nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                @if ($settings->is_cm_business)
                                                    Motorbike
                                                @elseif($settings->enable_trip_logs)
                                                    Vehicle
                                                @else
                                                    Device
                                                @endif
                                            </th>
                                            <th>Date</th>
                                            <th>Start Hrs</th>
                                            <th>End Hrs</th>
                                            <th>Total Working Hrs</th>
                                            <th>{{ trans('navmenu.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($hourMeters)
                                            @foreach ($hourMeters as $key => $record)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $record->device->device_number ?? '-' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                                    <td>{{ $record->start_hr+0 }} hr</td>
                                                    <td>{{ $record->end_hr+0 }} hr</td>
                                                    <td>
                                                        <span class="badge bg-info text-dark">
                                                            {{ $record->total_hr+0 }} hr
                                                        </span>
                                                    </td>
                                                    <td class="d-flex align-items-center gap-2">

                                                        <a href="javascript:void(0);" class="edit-hourmeter-btn"
                                                            data-id="{{ encrypt($record->id) }}"
                                                            data-device-id="{{ $record->device_id }}"
                                                            data-date="{{ $record->date }}"
                                                            data-start-hr="{{ $record->start_hr }}"
                                                            data-end-hr="{{ $record->end_hr }}"
                                                            data-update-url="{{ route('hour-meter.update', encrypt($record->id)) }}"
                                                            title="Edit">
                                                            <i class="fa fa-edit text-primary"></i>
                                                        </a>
                                                        <form method="POST"
                                                            action="{{ route('hour-meter.destroy', encrypt($record->id)) }}"
                                                            id="delete-hourmeter-form-{{ $key }}"
                                                            style="display:inline; margin:0;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="javascript:;"
                                                                onclick="return confirmDeleteHourMeter({{ $key }})"
                                                                title="Delete">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </a>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editHourMeterModal" tabindex="-1"
                        aria-labelledby="editHourMeterModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header py-3">
                                    <h6 class="modal-title fw-semibold" id="editHourMeterModalLabel">
                                        <i class="bx bx-edit-alt me-1 text-warning"></i>
                                        Edit Usage Hours Record
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <form id="edit-hourmeter-form" method="POST" action="">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label">
                                                    @if ($settings->is_cm_business)
                                                        Motorbike
                                                    @elseif($settings->enable_trip_logs)
                                                        Vehicle
                                                    @else
                                                        Device
                                                    @endif
                                                    <span class="text-danger fw-bold">*</span>
                                                </label>
                                                <select name="device_id" id="edit_device_id" required
                                                    class="form-select form-select-sm">
                                                    <option value="" disabled>
                                                        -- Select @if ($settings->is_cm_business)
                                                            Motorbike
                                                        @elseif($settings->enable_trip_logs)
                                                            Vehicle
                                                        @else
                                                            Device
                                                        @endif --
                                                    </option>
                                                    @foreach ($devices as $device)
                                                        <option value="{{ $device->id }}">{{ $device->device_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label">Date <span class="text-danger fw-bold">*</span></label>
                                                <input type="date" name="date" id="edit_date" max="{{ date('Y-m-d') }}" required class="form-control form-control-sm">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Start Hrs <span class="text-danger fw-bold">*</span></label>
                                                <input type="number" name="start_hr" id="edit_start_hr" required min="0" step="any" placeholder="e.g. 100" class="form-control form-control-sm">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">End Hrs <span class="text-danger fw-bold">*</span></label>
                                                <input type="number" name="end_hr" id="edit_end_hr" required min="0" step="any" placeholder="e.g. 200" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer py-2 d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-warning btn-sm px-4"
                                            data-bs-dismiss="modal">
                                            {{ trans('navmenu.btn_cancel') }}
                                        </button>
                                        <button type="submit" class="btn btn-success btn-sm px-4">
                                            Update Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        // TOGGLE DEVICE FORM 
        function showHideDeviceForm(elem) {
            var form = document.getElementById('new-device-form');
            var btn = document.getElementById('new-device-btn');
            var hmBtn = document.getElementById('new-hourmeter-btn');
            if (elem === 'show') {
                form.style.display = 'block';
                btn.style.display = 'none';
                hmBtn.style.display = 'none';
            } else {
                form.style.display = 'none';
                btn.style.display = 'inline-block';
                hmBtn.style.display = 'inline-block';
            }
        }

        //  TOGGLE HOUR METER FORM 
        function showHideHourMeterForm(elem) {
            var form = document.getElementById('new-hourmeter-form');
            var btn = document.getElementById('new-hourmeter-btn');
            var deviceBtn = document.getElementById('new-device-btn');
            if (elem === 'show') {
                form.style.display = 'block';
                btn.style.display = 'none';
                deviceBtn.style.display = 'none';
            } else {
                form.style.display = 'none';
                btn.style.display = 'inline-block';
                deviceBtn.style.display = 'inline-block';
            }
        }

        //  DELETE: DEVICE 
        function confirmDeleteDevice(id) {
            Swal.fire({
                title: "{{ trans('navmenu.are_you_sure') }}",
                text: "{{ trans('navmenu.no_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                cancelButtonText: "{{ trans('navmenu.no') }}"
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete-device-form-' + id).submit();
                }
            });
        }

        //  DELETE: HOUR METER 
        function confirmDeleteHourMeter(id) {
            Swal.fire({
                title: "{{ trans('navmenu.are_you_sure') }}",
                text: "{{ trans('navmenu.no_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                cancelButtonText: "{{ trans('navmenu.no') }}"
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete-hourmeter-form-' + id).submit();
                }
            });
        }

        //  DATATABLES
        $(function() {
            $('#devices').DataTable();

            $('#hourmeter-tab').on('shown.bs.tab', function() {
                if (!$.fn.DataTable.isDataTable('#hourmeters')) {
                    $('#hourmeters').DataTable();
                }
            });
        });
        //open edit modal and populate data
        $(document).ready(function() {
            $('.edit-hourmeter-btn').on('click', function() {
                var deviceId = $(this).data('device-id');
                var date = $(this).data('date');
                var startHr = $(this).data('start-hr');
                var endHr = $(this).data('end-hr');
                var updateUrl = $(this).data('update-url');

                $('#edit_device_id').val(deviceId);
                $('#edit_date').val(date);
                $('#edit_start_hr').val(startHr);
                $('#edit_end_hr').val(endHr);

                $('#edit-hourmeter-form').attr('action', updateUrl);

                $('#editHourMeterModal').modal('show');
            });
        });
    </script>
@endsection
