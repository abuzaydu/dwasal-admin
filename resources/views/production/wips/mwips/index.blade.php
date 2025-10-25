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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                <form class="dashform form-horizontal " action="{{url('f-prod-mwips')}}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <div class="float-sm-end">
                      <div class="input-group">
                          <button type="button" class="btn btn-white btn-sm" id="reportrange">
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
                                    <div class="tab-icon"><i class='fa fa-wrentch font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">Material WIPs</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link"  href="{{route('prod-mwips.create')}}" >
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">New Material WIPs</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link"  href="{{ url('prod-wip-materials')}}" >
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">WIP Materials</div>
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
                                            <th>Item Name</th>
                                            <th>Opening QTY</th>
                                            <th>Produced QTY</th>
                                            <th>Finished</th>
                                            <th>mwip Loss/Wastage</th>
                                            <th>Closing QTY</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mwips as $index => $mwip)
                                        <tr>
                                            <td style="text-align: center;">{{$index +1}}</td>
                                            <td style="text-align: center;">{{date('d-m-Y', strtotime($mwip->date))}}</td>
                                            <td>{{$mwip->title}}</td>
                                            <td style="text-align: center;">{{$mwip->opening_qty}}</td>
                                            <td style="text-align: center;">{{$mwip->produced}}</td>
                                            <td style="text-align: center;">{{$mwip->used}}</td>
                                            <td style="text-align: center;">{{$mwip->dam_qty}}</td>
                                            <td style="text-align: center;">{{$mwip->closing_qty}}</td>
                                            <td>
                                                <a href="{{route('prod-mwips.edit', encrypt($mwip->id))}}">
                                                    <span class="fa fa-edit"></span>
                                                </a> |
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('prod-mwips.destroy' , encrypt($mwip->id))}}" style="display:inline;">
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
                                <form id="frm-example" action="{{url('delete-multiple-mwips')}}" method="POST">
                                    @csrf
                                    <button id="submitButton" class="btn btn-danger ">{{trans('navmenu.delete_selected')}}</button>
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


