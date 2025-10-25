@extends('layouts.prod')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-' + id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row" action="{{ url('f-prod-labour-costs') }}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-8">
                      <div class="input-group">
                          <button type="button" class="btn btn-white btn-sm" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="col-md-4">
                       <a class="btn btn-primary btn-sm" href="{{ route('prod-labour-costs.create') }}"><i class="fa fa-plus"></i> New Labour Cost</a> 
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-new2">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i></div>
                                <div class="tab-title">Labour Costs</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_1-2" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-download font-18 me-1'></i></div>
                                <div class="tab-title"> Export Labour Costs</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ url('plc-payments') }}">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-money font-18 me-1'></i></div>
                                <div class="tab-title"> Labour Cost Payments</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>PLC No.</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.paid')}}</th>
                                        <th>{{trans('navmenu.unpaid')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($labourcosts as $index => $plc)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{ date('d/m/Y H:i:s', strtotime($plc->date)) }}</td>
                                        <td><a href="{{route('prod-labour-costs.show', encrypt($plc->id))}}">{{ sprintf('%04d', $plc->plc_no) }}</a></td>
                                        <td>{{ number_format($plc->amount, 2, '.',',') }}</td>
                                        <td>{{ number_format($plc->amount_paid, 2, '.',',') }}</td>
                                        <td>{{ number_format($plc->amount-$plc->amount_paid, 2, '.',',') }}</td>
                                        <td>{{ $plc->created_at }}</td>
                                        <td>
                                            <a href="{{route('prod-labour-costs.edit', encrypt($plc->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('prod-labour-costs.destroy' , encrypt($plc->id))}}" style="display:inline;">
                                                 @csrf
                                                 @method('DELETE')
                                                 <a href="#" onclick="confirmDelete('{{$index}}')">
                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                </a> 
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-plcs')}}" method="POST">  @csrf <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div> 
                    </div>

                    <div class="tab-pane fade" id="tab_1-2" role="tabpanel">
                        <div class="table-responsive">
                            <table id="example2" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>PLC No.</th>
                                        <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0; $tpaid = 0; ?>
                                    @foreach($labourcosts as $index => $plc)
                                    <?php $total += $plc->amount; $tpaid += $plc->amount_paid; ?>
                                    <tr>
                                        <td>{{ date('d/m/Y H:i:s', strtotime($plc->date)) }}</td>
                                        <td>{{ sprintf('%04d', $plc->plc_no) }}</td>
                                        <td>{{ number_format($plc->amount, 2, '.',',') }}</td>
                                        <td style="text-align: center;">{{ number_format($plc->amount_paid, 2, '.',',') }}</td>
                                        <td style="text-align: center;">{{ number_format($plc->amount-$plc->amount_paid, 2, '.',',') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: center;">{{ number_format($total, 2, '.',',') }}</th>
                                        <th style="text-align: center;">{{ number_format($tpaid, 2, '.',',') }}</th>
                                        <th style="text-align: center;">{{ number_format($total-$tpaid, 2, '.',',') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            var table = $('#example2').DataTable({
                'scrollX': true,
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');


            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
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
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'ids[]')
                            .val(rowId)
                        );
                    });
                }
            });
        });
    </script>
@endsection

