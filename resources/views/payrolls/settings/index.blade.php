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
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2 col-sm-12 text-md-end">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="fa fa-plus-square"></i>New Setting</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;">New Setting</h6>
                            <!-- <h6 class="mb-0 text-uppercase" id="list-title">Setting List</h6> -->
                        </div>
                        <div class="ms-auto">
                        </div>
                    </div>

                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('payroll-settings.store') }}">
                            @csrf
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Setting Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Setting name.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Description <span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="description" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Description.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Rate (%) <span style="color: red;">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="percent_rate" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide Percentage.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom03" class="form-label">Min Monthly Income</label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="min_income">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom03" class="form-label">Max Monthly Income</label>
                                <input type="number" class="form-control form-control-sm mb-1" id="validationCustom02" step="any" name="max_income">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Save</button>
                                <a onclick="showHideForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div id="item-list">
                        <table  id="psettings" class="table myDataTable table-hover table-striped table-bordered display nowrap" style="width:100%; white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th style="text-align: center;">Rate (%)</th>
                                    <th>Min Income (Monthly)</th>
                                    <th>Max Income (Monthly)</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($psettings as $key => $psetting)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $psetting->name }}</td>
                                    <td style="text-align: center;">{{ $psetting->percent_rate+0 }}</td>
                                    <td>{{ number_format($psetting->min_income, 2, '.', ',') }}</td>
                                    <td>{{ number_format($psetting->max_income, 2, '.', ',') }}</td>
                                    <td>{{ $psetting->description}}</td>
                                    <td> 
                                        <a href="{{ route('payroll-settings.edit', encrypt($psetting->id)) }}"><i class='fa fa-pencil mr-1'></i>Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('payroll-settings.destroy', encrypt($psetting->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='fa fa-trash mr-1'></i>Delete</a>
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
@endsection