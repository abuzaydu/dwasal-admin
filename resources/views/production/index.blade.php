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
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <form class="dashform form-horizontal " action="{{url('f-prod-costs')}}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    
                    <div class="form-group float-end">
                      <div class="input-group">
                          <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-NEW" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.production_records')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link"  href="{{route('prod-costs.create')}}" >
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.production_costs')}}</div>
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
                                            <th>S/NO</th>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th>{{trans('navmenu.batch_no')}}</th>
                                            <th>{{trans('navmenu.production_volume')}}</th>
                                            <th>{{trans('navmenu.total_cost')}}</th>
                                            <th>{{trans('navmenu.status')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($prod_records as $index => $prod_record)
                                        <tr>
                                            <td></td>
                                            <td style="text-align: center;">{{$index +1}}</td>
                                            <td>{{date('d-m-Y', strtotime($prod_record->date))}}</td>
                                            <td style="text-align: center;">{{$prod_record->prod_batch}}</td>
                                            <td style="text-align: center;">{{$prod_record->total_prod_qty}}</td>
                                            <td style="text-align: center;">{{number_format($prod_record->total_cost)}}</td>
                                            <td>@if($prod_record->is_transferred){{trans('navmenu.transfered')}} @else {{trans('navmenu.waiting_transfer')}} @endif</td>

                                            <td>
                                                <a href="{{route('prod-costs.show', encrypt($prod_record->id))}}">
                                                    <span class="fa fa-eye" title="{{trans('navmenu.show')}}"></span>
                                                </a>
                                                @if(!$prod_record->is_transferred) 
                                                 |  
                                                <a href="{{url('prod-transfer-to/'.encrypt($prod_record->id))}}">
                                                    <span class="fa fa-send" title="{{trans('navmenu.stock_transfer')}}"></span>
                                                </a> |
                                                <a href="{{ route('prod-costs.edit', encrypt($prod_record->id))}}"><i class="fa fa-edit"></i></a> | 
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('prod-costs.destroy' , encrypt($prod_record->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" onclick="confirmDelete('{{$index}}')">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a> 
                                                </form>
                                                @endif   
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div class="row">
                                
                            </div> 
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


