@extends('layouts.pr')
    <script type="text/javascript">
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var itemlist = document.getElementById('item-list');
            var newtitle = document.getElementById('new-title');
            var listtitle = document.getElementById('list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

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
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2 col-sm-12 text-md-end">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('payroll-settings.update', encrypt($psetting->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Setting Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" value="{{$psetting->name}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Setting name.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Description <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="description" value="{{$psetting->description}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Description.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Rate (%) <span style="color: red;">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="percent_rate" value="{{$psetting->percent_rate}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Percentage.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom03" class="form-label">Min Monthly Income</label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="min_income" value="{{$psetting->min_income}}">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom03" class="form-label">Max Monthly Income</label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="max_income" value="{{$psetting->max_income}}">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Save</button>
                                <a href="{{ url('payroll-settings')}}" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection