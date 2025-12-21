@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('pro-invoices') }}">Proforma Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="print_invoice p-1" id="print-invoice">
                        <div class="row g-1 p-2" style="border: 1px solid black;">
                            <div class="col-md-12">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align: left; padding-left: 15px;">
                                            @if(!is_null($company->logo_url))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150" style="border: 1px solid white;">
                                            </figure>
                                            @endif
                                        </td>
                                        <td>
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td colspan="2" style="text-align: right;">
                                                        <strong style="font-size: 14px;">{{$company->name}}</strong><br>
                                                        @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif
                                                                
                                                        <p class="invoice-address">
                                                            {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}@if(!is_null($shop->country)), {{$shop->country}}@endif <br>@if(!is_null($shop->tel) || !is_null($shop->mobile)) Tel: @if(!is_null($shop->tel))<b>{{$shop->tel}}</b> |@endif <b>{{$shop->mobile}}</b> @if(!is_null($shop->whatsapp))WhatsApp : <b>{{$shop->whatsapp}}</b>@endif<br> @endif Email: <b>{{$shop->email}}</b>@if(!is_null($shop->website)), Website: <b>{{$shop->website}}</b>@endif
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;  border-radius: 30px; text-align: center;">
                                                <h6 class="mb-0 text-uppercase" style="color: #fff;">Proforma Invoice</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 customer mt-0 mb-0">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="padding-left: 30px; width: 70%;">
                                            <b>PFI To :</b>
                                            <table class="customer-info" style="margin-left: 15px;">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 35%;"><span class="text-uppercase" style="font-size: 14px; font-weight: 400;">Client Name :</span></td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Address:</td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">
                                                        Contact Person </td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Mobile:</td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"> <b></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Email :</td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">TIN :<b>.........................</b></td>
                                                
                                                        <td style="width: 65%;">VRN :<b>...........................</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="text-align: left; width: 30%;">
                                            <table>
                                                <tr style="border: 1px solid gray; border-radius: 20px;">
                                                    <td colspan="2" style="font-size: 16px; text-align: center;">PFI No  : <b>{{ sprintf('%04d',$invoice->invoice_no)}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">
                                                        Date :
                                                    </td>
                                                    <td style="width: 60%; border-bottom: 1px dotted black;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">
                                                        Due Date :
                                                    </td>
                                                    <td style="width: 60%; border-bottom: 1px dotted black;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">
                                                        RFQ No:
                                                    </td>
                                                    <td style="width: 60%; border-bottom: 1px dotted black;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">
                                                        TIN:
                                                    </td>
                                                    <td style="width: 60%; border-bottom: 1px dotted black;">{{$shop->tin}}</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right;">
                                                        VRN:
                                                    </td>
                                                    <td style="width: 60%; border-bottom: 1px dotted black;">{{$shop->vrn}}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 mt-0">
                                <table class="" style="width: 100%; border-radius: 15px;">
                                    <thead>
                                        <tr style="background: <?php echo $settings->invoice_color; ?>; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center; border-left: 1px solid #fff; text-align: right;">Code</th>
                                            <th style="">Description</th>
                                            <th style="text-align: center; border-left: 1px solid #fff;">Qty</th>
                                            @if($items->count() > 0)
                                            <th style="text-align: center; border-left: 1px solid #fff;">UOM</th>
                                            @endif
                                            <th style="text-align: center; border-left: 1px solid #fff;">Rate</th>
                                            <th style="text-align: right; border-left: 1px solid #fff;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $tqty = 0; ?>
                                        @if($items->count() < 0)
                                        @foreach($items as $key => $item)
                                        <?php
                                            $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                            $quantity = $item->quantity/$punit->qty_equal_to_basic;
                                            $price_per_unit = $item->price_per_unit*$punit->qty_equal_to_basic;
                                            $unit_discount = $item->discount*$punit->qty_equal_to_basic;
                                            $tqty += $item->quantity;
                                            
                                            $slug = str_replace($item->name, '', $item->slug);
                                        ?>
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                            <td style="text-align: center;">{{$key+1}}</td>
                                            <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->product_code)){{$item->product_code}}@endif</td>
                                            <td class="desc" style="">{{$item->name}} @if($slug != '')- {{$slug}}@endif</td>
                                            <td class="qty" style="border-left: 1px solid gray; text-align: center;">{{$quantity+0}}</td>
                                            <td style="border-left: 1px solid gray; text-align: center;">{{$punit->unit_name}}</td>
                                            <td class="unit" style="border-left: 1px solid gray; text-align: center;">{{number_format($item->cost_per_unit, 2, '.', ',') }}</td>
                                            <td class="total" style=" border-left: 1px solid gray; text-align: right;">{{number_format($item->amount, 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        @for($i=0; $i<5; $i++)
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                            <td style="text-align: center;">{{$i+1}}</td>
                                            <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;"></td>
                                            <td class="desc" style=""></td>
                                            <td class="qty" style="border-left: 1px solid gray; text-align: center;"></td>
                                            <td style="border-left: 1px solid gray; text-align: center;"></td>
                                            <td class="unit" style="border-left: 1px solid gray; text-align: center;"></td>
                                            <td class="total" style=" border-left: 1px solid gray; text-align: right;"></td>
                                        </tr>
                                        @endfor
                                        @if($servitems->count() >0)
                                        @foreach($servitems as $key => $item)
                                        <?php $tqty += $item->repeatition; ?>
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                            <td style="text-align: center;">{{$key+1}}</td>
                                            <td style="text-align: right; border-left: 1px solid gray;">{{$item->code}}</td>
                                            <td class="desc"><b>{{$item->name}}</b><br><small>{{$item->description}}</small></td>
                                            <td class="qty" style="border-left: 1px solid gray; text-align: center;">{{$item->repeatition}}</td>
                                            @if($items->count() > 0)
                                            <td style="border-left: 1px solid gray;text-align: center;">Unit(s)</td>
                                            @endif
                                            <td class="unit" style="border-left: 1px solid gray; text-align: center;">{{number_format($item->cost_per_unit, 2, '.', ',') }}</td>
                                            <td class="total" style="border-left: 1px solid gray; text-align: right;">{{number_format($item->amount, 2, '.', ',') }}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        <tr class="blank_row" style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                            <td colspan="3" style="" class="desc"></td>
                                            <td class="qty" style="border-left: 1px solid gray; text-align: center;"></td>
                                            @if($items->count() > 0)
                                            <td style="border-left: 1px solid gray; text-align: center;"></td>
                                            @endif
                                            <td style="border-left: 1px solid gray; text-align: center;" class="unit"></td>
                                            <td class="total" style=" border-left: 1px solid gray; text-align: right;"></td>
                                        </tr>
                                        <tr class="" style="border-bottom: 1px solid gray; border-left: 1px solid <?php echo $settings->invoice_color; ?>; border-right: 1px solid <?php echo $settings->invoice_color; ?>;">
                                            <td colspan="3" style="" class="desc">Total</td>
                                            <td class="qty" style="border-left: 1px solid gray; text-align: center;"></td>
                                            @if($items->count() > 0)
                                            <td style="border-left: 1px solid gray; text-align: center;"></td>
                                            @endif
                                            <td style="border-left: 1px solid gray; text-align: center;" class="unit"></td>
                                            <td class="total" style="border-left: 1px solid gray; text-align: right;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mt-0">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr style="border-top: 1px solid gray;">
                                            <td style="width: 60%">
                                                @if($settings->show_bd)
                                                <table style="width: 100%; font-size: 8px; padding: 0;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;   border-radius: 0px; text-align: left;">
                                                                <h6 class="mb-0 text-uppercase" style="color: <?php echo $settings->invoice_title_color; ?>;">Bank Details</h6>
                                                            </td>
                                                            <!-- <td>Payment Options :</td> -->
                                                        </tr>
                                                        @if($baccounts->count() > 0)
                                                        <tr>
                                                            <td class="row">
                                                                @foreach($baccounts as $bankdetail)
                                                                <div class="col-sm-10" style="border: 1px solid #e3e4e8; margin-bottom: 2px;">
                                                                    Bank Name : <b>{{$bankdetail->bank_name}}</b><br>
                                                                    Account Name: <b>{{$bankdetail->account_name}}</b><br>
                                                                    <?php $accnumbers = App\Models\Account::where('shop_id', $shop->id)->where('bank_name', $bankdetail->bank_name)->where('account_name', $bankdetail->account_name)->select('currency', 'account_number')->get(); ?>
                                                                    @foreach($accnumbers as $account)
                                                                    Account No : @if(!is_null($account->currency)) {{$account->currency}} : @endif <b>{{$account->account_number}}</b><br>
                                                                    @endforeach
                                                                    Branch name/Code : <b>{{$bankdetail->branch_name}}</b><br>
                                                                    Swift : <b>{{$bankdetail->swift_code}}</b>
                                                                </div>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                        @else
                                                        <tr>
                                                            <td><span style="color: orange;">Your bank details not updated. Please update your bank details <a href="{{ route('pro-invoices.edit', encrypt($invoice->id)) }}">Here</a></span></td>
                                                        </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                                @endif
                                            </td>
                                            <td style="width: 40%; border: 1px solid gray;  border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                <table class="mt-0" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="unit" style=" text-align: left;">{{trans('navmenu.total')}} (Excl.Tax):</td>
                                                            <td class="total" style=" text-align: right;"><b></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="unit" style=" text-align: left;">{{trans('navmenu.discount')}}:</td>
                                                            <td class="total" style=" text-align: right;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="unit" style=" text-align: left;">TAX {{$settings->tax_rate}}%:</td>
                                                            <td class="total" style=" text-align: right;"></td>
                                                        </tr>
                                                        <tr>
                                                            <th class="unit" style="text-align: left; width: 50%;"><b>{{trans('navmenu.total')}} (Inc.Tax) ({{$defcurr->code}}):</b></th>
                                                            <th style="border-bottom: 2px dotted black; width: 50%;"><b></b></th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr style="border-top: 1px solid <?php echo $settings->invoice_color; ?>; border-bottom: 2px solid <?php echo $settings->invoice_color; ?>; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                            <td style="width: 65%;">
                                                <div class="notice col-md-12">
                                                    <div><b>DECLARATION</b>:</div>
                                                    <div>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</div>
                                                </div>
                                                @if(!is_null($invoice->notes))
                                                <div class="notice col-md-12 pt-3">
                                                    <div>NOTE:</div>
                                                    <div>{!! $invoice->notes !!}</div>
                                                </div>
                                                @endif
                                            </td>
                                            <td style="width: 35%; border-left: 1px solid gray;">
                                                <div class="text-center">
                                                    <span style="font-size: 14px; font-weight: bold;">For {{$company->name}}</span><br>
                                                    @if(!is_null($shop->stamp))
                                                    <figure>
                                                        <img class="invoice-logo" src="{{asset('storage/stamps/'.$shop->stamp)}}" alt="" width="80">
                                                    </figure>
                                                    @endif
                                                    <br>
                                                    <b>-----------------------------------------</b><br>
                                                    <b>Authorized Signatory</b>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if(!is_null($invoice->terms_and_conditions))
                            <div class="col-md-12">
                                <label class="form-label">Terms & Conditions</label>
                                {!! $invoice->terms_and_conditions !!}
                            </div>
                            @endif
                            @if($settings->show_end_note && !is_null($settings->invoice_end_note))
                            <div class="col-md-12 mx-auto text-center">
                                <table style="width: 100%;">
                                    <tr style="background: <?php echo $settings->invoice_color; ?>; border-top: 1px solid <?php echo $settings->invoice_color; ?>; border-bottom: 2px solid <?php echo $settings->invoice_color; ?>; border-bottom-left-radius: 45px; border-bottom-right-radius: 45px;">
                                        <td style="text-align: center; font-size: 16px !important; font-style: italic; color: <?php echo $settings->invoice_title_color; ?>;"><b>{{$settings->invoice_end_note}}.</b></td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-4">
                        @if($settings->enable_sale_approval)
                            @if($invoice->status == 'Approved' || $invoice->status == 'Full Invoiced')
                            <div class="col-md-5 p-1">
                                <button onclick="javascript:savePdf()" type="button" class="btn btn-outline-primary btn-sm" style="width: 100%;">
                                    <i class="fa fa-download"></i> {{trans('navmenu.download')}} / <i class="fa fa-printer"></i>{{trans('navmenu.print')}}
                                </button>
                            </div>
                            @if($invoice->status != 'Full Invoiced')
                            <div class="col-md-4 p-1">
                                <form action="{{ url('create-invoice') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$invoice->id}}">
                                    <button type="submit" class="btn btn-outline-success btn-sm" style="width: 100%"><i class="fa fa-pencil"></i>Create Invoice</button>
                                </form>
                            </div>
                            @endif
                            @else
                            <div class="col-md-4 p-1">
                                <a class="btn btn-secondary btn-sm" href="{{ route('pro-invoices.edit', encrypt($invoice->id))}}" style="width: 100%"><i class="fa fa-edit"></i>Update</a>
                            </div>
                            <a class="btn btn-primary btn-sm" href="{{ url('create-dnote-pfi/' . encrypt($invoice->id)) }}"><i class="fa fa-file"></i> Delivery Note</a>
                            @if(Auth::user()->can('approve-pro-invoice') && $invoice->status == 'Awaiting for Approval')
                            <div class="col-md-4 p-1">
                                <a class="btn btn-primary btn-sm" href="{{ url('approve-pro-invoice/'.encrypt($invoice->id))}}" style="width: 100%"><i class="fa fa-edit"></i>Approve Proforma Invoice</a>
                            </div>
                            <div class="col-md-4 p-1">
                                <a class="btn btn-danger btn-sm" href="{{ url('reject-pro-invoice/'.encrypt($invoice->id))}}" style="width: 100%"><i class="fa fa-edit"></i>Reject Proforma Invoice</a>
                            </div>
                            @endif
                            @endif
                        @else
                        <div class="col-md-12 p-1">
                            <button onclick="javascript:savePdf()" type="button" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-download"></i> {{trans('navmenu.download')}} / <i class="fa fa-printer"></i>{{trans('navmenu.print')}}
                            </button>
                            <a class="btn btn-secondary btn-sm" href="{{ route('pro-invoices.edit', encrypt($invoice->id))}}"><i class="fa fa-edit"></i>Update</a>
                            @if($invoice->status == 'Pending')
                            <a class="btn btn-primary btn-sm" href="{{ url('create-dnote-pfi/' . encrypt($invoice->id)) }}"><i class="fa fa-file"></i> Delivery Note</a>
                            <form action="{{ url('create-invoice') }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="id" value="{{$invoice->id}}">
                                <button type="submit" class="btn btn-outline-success btn-sm"><i class="fa fa-pencil"></i>Create Invoice</button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {

            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;


            //File name for printed ducument
            document.title = "<?php echo 'Proforma Invoice_'.sprintf('%06d', $invoice->inv_no).'_'.$invoice->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
            const element = document.getElementById("print-invoice");
            var filename = "<?php echo 'Proforma Invoice_'.sprintf('%06d', $invoice->invoice_no).'_'.$invoice->created_at; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).toPdf().save();

          // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }
    </script>