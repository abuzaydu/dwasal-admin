@extends('layouts.prod')
<script type="text/javascript">
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#datepicker").val('');
      }
    }

    function confirmDelete(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.value) {
           document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              'Deleted!',
              'Your Product has been deleted.',
              'success'
            )
          }
        })
    }
</script>

@section('content')
 <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-12 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>

        <div  class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.user')}}</p>
                                <h4 class="my-1">{{$employee->first_name}} {{$employee->last_name}}</h4>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bxs-box"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.total_amount')}}</p>
                                <h4 class="my-1">{{number_format($pmuse->total_cost)}}</h4>
                            </div>
                            <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa fa-money"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.date')}}</p>
                                <h4 class="my-1">{{date('d M, Y', strtotime($pmuse->date))}}</h4>
                            </div>
                            <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
             <!-- /.col -->
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.batch_no')}}</p>
                                <h4 class="my-1">{{$pmuse->prod_batch}}</h4>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="lni lni-information"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>

          <!-- =========================================================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="card radius-10">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                          <thead style="font-weight: bold; font-size: 14;">
                              <th>#</th>
                              <th>{{trans('navmenu.packing_name')}}</th>
                              <th>{{trans('navmenu.product_packed')}}</th>
                              <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                              <th style="text-align: center;">UOM</th>
                              <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                              <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                              <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                              <th>{{trans('navmenu.actions')}}</th>
                          </thead>
                          <tbody>
                              @foreach($uitems as $index => $pmitem)
                              <tr>
                                  <td>{{$index+1}}</td>
                                  <td>{{$pmitem->name}}</td>
                                  <td>{{$pmitem->product_name}}</td>
                                  <td style="text-align: center;">{{$pmitem->quantity}}</td>
                                  <td> <span style="color: gray; text-align: center;">{{$pmitem->basic_uom}}</span></td>
                                  <td style="text-align: center;">{{number_format($pmitem->unit_cost)}}</td>
                                  <td style="text-align: center;">{{number_format($pmitem->total)}}</td>
                                  <td style="text-align: center;">{{$pmitem->date}}</td>
                                  <td style="text-align: center;">
                                      <a href="{{route('pm-used-items.edit', encrypt($pmitem->id))}}">
                                          <i class="fa fa-edit" style="color: blue;"></i>
                                      </a>|
                                      <form id="delete-form-{{$index}}" style="display: inline;" method="POST" action="{{route('pm-used-items.destroy' , encrypt($pmitem->id))}}">
                                          @csrf
                                          @method('DELETE')
                                          <a href="#" onclick="confirmDelete('{{$index}}')">
                                          <i class="fa fa-trash" style="color: red;"></i></a>
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