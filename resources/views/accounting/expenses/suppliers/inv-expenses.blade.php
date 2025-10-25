@extends('layouts.acc')
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
            window.location.href="{{url('delete-trans/')}}/"+id;
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

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa  fa-truck"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{trans('navmenu.supplier')}}</span>
                        <span class="info-box-number">{{App\Supplier::find($trans->supplier_id)->name}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{trans('navmenu.total_amount')}}</span>
                        <span class="info-box-number">{{number_format($trans->amount)}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-file"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{trans('navmenu.invoice_no')}}</span>
                        <span class="info-box-number">{{ sprintf('%04d', $trans->invoice_no)}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="#" class="btn btn-danger" onclick="confirmDelete('<?php echo encrypt($trans->id) ?>')"><i class="fa fa-trash"></i> Delete</a>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- =========================================================== -->
    <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
          
            <div class="box box-primary">
                <div class="box-body">
                    <!-- Custom Tabs (Pulled to the right) -->
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1-1">
                                <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <th>#</th>
                                        <th>{{trans('navmenu.expense_type')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th>VAT</th>
                                        @endif
                                        @if($settings->estimate_withholding_tax)
                                        <th>{{trans('navmenu.wht_rate')}}</th>
                                        <th>{{trans('navmenu.wht_amount')}}</th>
                                        @endif
                                        <th>{{trans('navmenu.expense_date')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $index => $cost)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$cost->cost_type}}</td>
                                            <td style="text-align: center;">{{number_format($cost->cost_amount)}}</td>
                                            @if($settings->is_vat_registered)
                                            <td>{{number_format($cost->exp_vat)}}</td>
                                            @endif
                                            @if($settings->estimate_withholding_tax)
                                            <td style="text-align: center;">{{number_format($cost->wht_rate)}} </td>
                                            <td style="text-align: center;">{{number_format($cost->wht_amount)}} </td>
                                            @endif
                                            <td>{{$cost->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
@endsection 