
                    
                    <div id="payroll">
                        <div class="text-center">
                            @if(!is_null($settings->logo_url))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="widtd: 60px; height: 60px">
                            </figure>
                            @endif
                            <h6 class="mb-0 text-uppercase text-center">{{$settings->company_name}}</h6><br>
                            <h6 class="mb-0 text-uppercase">Payroll {{$payroll->month}}</b></h6>
                        </div>
                        <hr/>
                        <h6 class="mb-0 text-uppercase">Payroll Information </h6>
                        <hr/>
                        <table class="table mb-0 table-striped payroll">
                            <tbody>
                                <tr>
                                    <td>Payroll ID:</td>
                                    <td><b>{{$payroll->payid}}</b></td>
                                <!-- </tr>
                                <tr> -->
                                    <td>Date & Time Created:</td>
                                    <td><b>{{date('Y/m/d h:i:s a', strtotime($payroll->created_at))}}</b></td>
                                </tr>
                                <tr>
                                    <td>Employee Name:</td>
                                    <td><b>{{$employee->fname}} {{$employee->lname}}</b></td>
                                <!-- </tr>
                                <tr> -->
                                    <td>Position:</td>
                                    <td><b>{{$position->name}}</b></td>
                                </tr>
                                <tr>
                                    <td>Basic Pay :</td>
                                    <td><b>{{number_format($monthly, 2,'.', ',')}}</b></td>
                                <!-- </tr>
                                <tr> -->
                                    <td>Days Worked:</td>
                                    <td><b>{{$payroll->days_work}}</b></td>
                                </tr>
                                <tr>
                                    <td>Overtime (Hours) :</td>
                                    <td><b>{{$payroll->overtime_hrs}}</b></td>
                                <!-- </tr>
                                <tr> -->
                                    <td>Lates (minutes):</td>
                                    <td><b>{{$payroll->late}}</b></td>
                                </tr>
                                <tr>
                                    <td>Absences (Days) :</td>
                                    <td><b>{{$payroll->absences}}</b></td>
                                <!-- </tr>
                                <tr> -->
                                    <td>Bonuses:</td>
                                    <td><b>{{number_format($payroll->bonuses, 2, '.', ',')}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <div class="row">
                            <div class="col-6">
                                <h6 class="mb-0">Earnings</h6>
                                <hr/>
                                <table class="table mb-0 table-striped">
                                    <tbody>
                                        <tr>
                                            <td>Montdly Pay:</td>
                                            <td><b>{{ number_format($monthly, 2, '.', ',')}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Overtime Pay:</td>
                                            <td><b>{{ number_format($overtime, 2, '.', ',')}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Bonuses:</td>
                                            <td><b>{{ number_format($payroll->bonuses, 2, '.', ',') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b class="text-uppercase">Gross Income</b> :</td>
                                            <td><b>{{ number_format($gross_income, 2, '.', ',')}}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-6">
                                <h6 class="mb-0">Deductions witd monthly contributions</h6>
                                <hr/>
                                <table class="table mb-0 table-striped">
                                    <tbody>
                                        @if(!is_null($sscheme))
                                        <tr>
                                            <td>{{$sscheme->name}} ({{$sscheme->percent_rate}} %):</td>
                                            <td><b>{{ number_format($ssf, 2, '.', ',') }}</b></td>
                                        </tr>
                                        @endif
                                        @if(!is_null($hischeme))
                                        <tr>
                                            <td>{{$hischeme->name}} ({{$hischeme->percent_rate}} %):</td>
                                            <td><b>{{ number_format($his, 2, '.', ',') }}</b></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Lates :</td>
                                            <td><b>{{ number_format($late_overall, 2, '.', ',') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Absences :</td>
                                            <td><b>{{ number_format($absent_overall, 2, '.', ',') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>PAYE :</td>
                                            <td><b>{{ number_format($payevalue, 2, '.', ',') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b class="text-uppercase">Total Deductions </b>:</td>
                                            <td><b>{{ number_format($total_deduction, 2, '.', ',') }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr/>
                        <h6 class="mb-0">Overall Net Pay (Monthly Gross Income - Total deductions)</h6>
                        <hr/>
                        <table class="table mb-0 table-striped">
                            <tbody>
                                <tr>
                                    <td><b class="text-uppercase">Total Net Pay</b> :</td>
                                    <td><b>{{ number_format($net_pay, 2, '.', ',') }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>