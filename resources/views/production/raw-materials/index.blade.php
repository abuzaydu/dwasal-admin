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
            document.getElementById('delete-form-'+id).submit();
            
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
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.raw_materials')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.new_raw_material')}}</div>
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
                                            <th>{{trans('navmenu.material_name')}}</th>
                                            <th>{{trans('navmenu.basic_uom')}}</th>
                                            <th>{{trans('navmenu.in_stock')}}</th>
                                            <th>Unit Cost</th>
                                            <th>Material For</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $index => $material)
                                        <?php $prod = App\Models\Product::find($material->product_id); ?>
                                        <tr>
                                            <td>{{$material->id}}</td>
                                            <td><a href="{{route('raw-materials.show', encrypt($material->id))}}">{{$material->name}}</a></td>
                                            <td style="text-align: center;">{{$material->basic_uom}}</td>
                                            <td style="text-align: center;">{{$material->pivot->in_store+0}}</td>
                                            <td style="text-align: center;">{{$material->pivot->unit_cost+0}}</td>
                                            <td>
                                                @if(!is_null($prod))
                                                {{$prod->name}}
                                                @else
                                                All
                                                @endif
                                            </td>
                                            <td>{{$material->pivot->created_at}}</td>
                                            <td>
                                                <a href="{{route('raw-materials.edit', encrypt($material->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('raw-materials.destroy' , encrypt($material->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#"  onclick="confirmDelete('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i>
                                                    </a> 
                                                </form>     
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- <form id="frm-example" action="{{url('delete-multiple-materials')}}" method="POST">
                                @csrf
                                     <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                                </form> -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div class="row">
                                <form class="form" method="POST" action="{{route('raw-materials.store')}}">
                                    @csrf
                                    <div class="row ">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="form-label">{{trans('navmenu.material_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                                
                                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-3">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                                                <select class="form-select form-select-sm mb-3" name="basic_uom" required style="width: 100%;">
                                                    @foreach($units as $key => $unit)
                                                    <option>{{$unit->unit_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="form-label">{{trans('navmenu.current_stock')}}</label>
                                                <input id="qty" type="number" min="0" name="qty" step="any" placeholder="{{trans('navmenu.hnt_current_stock')}}" class="form-control form-control-sm mb-3">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                                <input id="unit_price" type="number" min="0" step="any" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-3">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Raw Material For </label>
                                            <select class="form-select form-select-sm mb-1" name="product_id" id="product_id">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                                <textarea name="description" rows="1" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.hnt_product_desc')}}"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group float-end">
                                                <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                                <button type="reset" class="btn btn-warning">{{trans('navmenu.btn_reset')}}</button>
                                            </div>
                                        </div>
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


