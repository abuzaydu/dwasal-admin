@extends('layouts.hr')
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
                title: "Sure you want to delete ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "delete",
                cancelButtonText: "cancel"
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire(
                        "Deleted",
                        'success'
                    )
                }else{
                    Swal.fire(
                        "Cancelled",
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
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-1 gap-3">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;">New Position</h6>
                            <h6 class="mb-0 text-uppercase" id="list-title">Positions List</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" id="new-btn" class="btn btn-primary" onclick="showHideForm('show')"><i class="fa fa-plus-square"></i>New Position</button>
                        </div>
                    </div>

                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('positions.store') }}">
                            @csrf
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Position Name<span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-3" id="validationCustom01" name="name" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Position name.</div>
                                </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Basic Pay (Per Hour)</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_hourly">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Basic Pay (Per Month)</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_monthly">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Transport Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="trans_allowance">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">House Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="house_allowance">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Communication Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="com_allowance">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a onclick="showHideForm('hide')" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div id="item-list">
                        <table class="table myDataTable table-hover align-middle mb-0 card-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Position Name</th>
                                    <th>Basic Pay (Per Hour)</th>
                                    <th>Basic Pay (Per Month)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($positions as $key => $position)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $position->name }}</td>
                                    <td>{{ number_format($position->basic_pay_hourly, 2, '.', ',') }}</td>
                                    <td>{{ number_format($position->basic_pay_monthly, 2, '.', ',') }}</td>
                                    <td> 
                                        <a href="{{ route('positions.edit', encrypt($position->id)) }}"><i class='bx bx-pencil mr-1'></i>Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('positions.destroy', encrypt($position->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" href="javascript:;" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='bx bx-trash mr-1'></i>Delete</a>
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