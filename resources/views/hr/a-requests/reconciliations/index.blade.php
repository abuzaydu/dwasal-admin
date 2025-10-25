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

        function submitEditForm(key) {
            document.getElementById('edit-form-' + key).submit();
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row g-1">
        <div class="col-12 mb-0">
            <form class="report-form row g-1" action="{{ url('f-reconciliations') }}" method="POST">
                @csrf
                @csrf
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="mrequests" class="table card-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Reconcile ID</th>
                                <th>Reconcile Date</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reconciliations as $key => $reconcile)
                            <tr class="row-selectable">
                                <td style="text-align: center;"><a href="{{ route('reconciliations.show', encrypt($reconcile->id)) }}">{{ $reconcile->reconcile_id }}</a></td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($reconcile->reconcile_date))}}</td>
                                <td>{{ $reconcile->name}}</td>
                                <td>{{ $reconcile->status }}</td>
                                <td>
                                    <a href="{{ route('reconciliations.show', encrypt($reconcile->id)) }}" class="btn btn-link btn-sm color-400"><i class="fa fa-file-text-o"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>    
        </div>
    </div>
@endsection