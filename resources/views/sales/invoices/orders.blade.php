@extends('layouts.app')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');

        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            } else {
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        } else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        } else {
            b.style.display = 'none';
            m.style.display = 'none';
        }
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div>
        <form class="dashform row g-3" action="{{ url('an-sales') }}" method="POST">
            @csrf
            <div class="col-sm-5"></div>
            <div class="form-group col-sm-3">
                <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i
                            class="fa fa-calendar"></i></span>
                    <input type="text" name="sale_date" id="saledate" placeholder="{{ trans('navmenu.pick_date') }}"
                        class="form-control form-control-sm mb-1" autocomplete="off">
                </div>
            </div>
            <input type="hidden" name="start_date" id="start_input" value="">
            <input type="hidden" name="end_date" id="end_input" value="">
            <!-- Date and time range -->
            <div class="form-group col-sm-4">
                <div class="input-group">
                    <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i
                                class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </div>
            <!-- /.form group -->
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="manage-sales" role="tabpanel">
                    <div class="table-responsive">
                        <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold;">
                                <tr>
                                    <th></th>
                                    <th style="text-align: center;">{{ trans('navmenu.user') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.customer') }}</th>
                                    <th style="text-align: center;">{{trans('navmenu.order_no')}}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.saledate') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.last_updated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $index => $sale)
                                    <tr>
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ $sale->first_name }}</td>
                                        <td>{{ $sale->name }}</td>
                                        <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                        <td style="text-align: center;">{{ date('d, M Y', strtotime($sale->time_created)) }}</td>
                                        <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}
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
@endsection
