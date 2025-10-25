(function(){
    var app = angular.module('smartpos', [ ]);


    app.directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
              ngModel.$parsers.push(function(value) {
                return '' + value;
              });
              ngModel.$formatters.push(function(value) {
                return parseFloat(value, 10);
              });
            }
        };
    });
    
    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', '$timeout', function($scope, $http, $filter, $timeout) {
        
        $scope.saleTempId = function(sale_temp_id){

            $scope.items = [ ];
            $scope.tempid = sale_temp_id;

            $scope.saletemp = { };
            $scope.customers = [ ];
            $scope.currencies = [ ];
            $scope.saletempitems = [ ];
            $scope.newsaletemp = { };
            $scope.defaultCustomer;
            $scope.dpercent = 0;
            $scope.sale_mode = 'Retail Price'; 
            $scope.discapprovals = 0;
            $scope.getData = function(){
                console.log("Sale temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/saletemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.salemodes = response.data.salemodes;
                    $scope.saletemp = response.data.saletemp;
                    $scope.sale_mode = response.data.sale_mode;
                    $scope.saletempitems = response.data.items;
                    $scope.customers = response.data.customers;
                    $scope.currencies = response.data.currencies;
                    $scope.discapprovals = response.data.discapprovals;
                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            };

            $scope.getData();
            
            $scope.usebarcode = false;
            $http({
                method: 'GET',
                url: 'api/usebarcode'
            }).then(function (response) {
                $scope.usebarcode = response.data.usebarcode;
                if ($scope.usebarcode) {
                    // Kick off the interval
                    $scope.intervalFunction();
                }
                // console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            // Function to replicate setInterval using $timeout service.
            $scope.intervalFunction = function(){
                // $timeout(function() {
                //   $scope.getData();
                //   $scope.intervalFunction();
                // }, 15000)
            };

            $scope.inputValue = '';
            $scope.getInputVal = function(obj) {
                return $scope.inputValue.replace('', obj).replace(/,/g, '');
            };


            $scope.updateSaleMode = function(sale_mode){
                $scope.sale_mode = sale_mode;
                console.log($scope.sale_mode);
                $http({
                    method: 'POST',
                    url: 'api/update-sale-mode', 
                    data: { sold_in: sale_mode, sale_temp_id: $scope.tempid }
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addSaleTemp = function(item) {
                if (item.retail_price == null) {
                    Swal.fire({
                        type: 'warning',
                        title: 'NO SELLING PRICE...',
                        text: 'Selling price for  '+item.name+' is not set. Please update price for this product.'
                    });
                }else{
                    $http({
                        method: 'POST',
                        url: 'api/saletemp', 
                        data: { sale_temp_id: $scope.tempid, product_id: item.id, basic_uom: item.basic_uom, in_stock: item.in_stock, unit_cost: item.unit_cost, sold_in: $scope.sale_mode, retail_price: item.retail_price, wholesale_price: item.wholesale_price }
                    }).then(function (response) {
                        if(response.data.status == 'DUPL') {
                            Swal.fire({
                                type: 'info',
                                title: 'DUPLICATES...',
                                text: response.data.msg
                            });
                        }else if(response.data.status == 'WP') {
                            Swal.fire({
                                type: 'info',
                                title: 'WRONG PRICES...',
                                text: response.data.msg
                            });        
                        }else if(response.data.status == 'LOW') {
                            Swal.fire({
                                type: 'warning',
                                title: 'OUT OF STOCK...',
                                text: 'The stock of '+item.name+' is currently ZERO. Please Purchase new Stock.'
                            });                            
                        }else{
                            $scope.getData();
                        }
                        console.log(response);
                    }, function (error) {
                        console.log(error);
                    });
                }
            }

            $scope.updateSaleTemp = function(newsaletemp) {
                // alert(newsaletemp.sold_in);
                $http({
                    method: 'PUT',
                    url: 'api/saletemp/' + newsaletemp.id,
                    data: { quantity_sold: newsaletemp.quantity_sold, product_unit_id: newsaletemp.product_unit_id, buying_price: newsaletemp.unit_cost * newsaletemp.quantity_sold, retail_price: newsaletemp.retail_price, price: newsaletemp.retail_price * newsaletemp.quantity_sold, disc_percent: newsaletemp.disc_percent, discount: newsaletemp.discount, total_discount: newsaletemp.total_discount, sold_in: newsaletemp.sold_in, with_vat:newsaletemp.with_vat }
                }).then(function (response) {
                    if (response.data.status == 'LOW') {
                        Swal.fire({
                            type: 'info',
                            title: 'LOW STOCK...',
                            text: response.data.msg
                        });
                        $scope.getData();
                    }else if(response.data.status == 'SHARED') {
                        (async () => {
                            /* inputOptions can be an object or Promise */
                            const inputOptions = new Promise((resolve) => {
                              setTimeout(() => {
                                resolve({
                                  'Old': 'Sell Old Stock First',
                                  'New': 'Sell New Stock First',
                                })
                              }, 1000)
                            })

                            const { value: usestock } = await Swal.fire({
                              title: 'Different Stocks',
                              text: response.data.msg,
                              input: 'radio',
                              inputOptions: inputOptions,
                              inputValidator: (value) => {
                                if (!value) {
                                  return 'You need to choose something!'
                                }
                              }
                            })

                            if (usestock) {
                                $http({
                                    method: 'PUT',
                                    url: 'api/saletemp/' + newsaletemp.id,
                                    data: { used_stock: usestock }
                                }).then(function (response){
                                    $scope.getData();
                                });
                            }

                        })()
                    }else if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                        $scope.getData();
                    }else if(response.data.status == 'UNIT CHANGE' || response.data.status == 'VAT UPDATED') {
                        $scope.getData();
                    }
                     // $scope.getData();
                    // console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.updateSaleTempDiscount = function(sale_discount) {
                var total=0;
                angular.forEach($scope.saletempitems , function(newsaletemp){
                    total+= parseFloat(newsaletemp.retail_price * newsaletemp.quantity_sold);
                });

                $scope.dpercent = (sale_discount/total)*100;

                // alert('You have added a Discount of '+ Math.round(dpercent)+ '% to each item sold.');
                angular.forEach($scope.saletempitems, function(newsaletemp){                 
                    $http({
                        method: 'PUT',
                        url: 'api/saletemp/' + newsaletemp.id,
                        data: { quantity_sold: newsaletemp.quantity_sold, product_unit_id: newsaletemp.product_unit_id, buying_price: newsaletemp.unit_cost * newsaletemp.quantity_sold, price: newsaletemp.retail_price * newsaletemp.quantity_sold, disc_percent: $scope.dpercent, discount: newsaletemp.discount, total_discount: newsaletemp.total_discount, sold_in: newsaletemp.sold_in, with_vat:newsaletemp.with_vat }
                    }).then(function (response) {
                    });
                });

                $scope.getData();
            }     

            $scope.removeSaleTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/saletemp/'+id
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newsaletemp) {
                    total += parseFloat(newsaletemp.retail_price * newsaletemp.quantity_sold);
                });
                return total;
            }

            $scope.sumDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newsaletemp) {
                    t_discount += parseFloat(newsaletemp.total_discount);
                });
                return t_discount;
            }

            $scope.sumVAT = function(list){
                var t_vat=0;
                angular.forEach(list, function(newsaletemp) {
                    t_vat+= parseFloat(newsaletemp.vat_amount);
                });
                return t_vat;
            }


            //Service Items
            // $scope.servitems = [ ];
            // $http({
            //     method: 'GET',
            //     url: 'api/servitem'
            // }).then(function (response) {
            //     $scope.servitems = response.data;
            //     console.log(response.data);
            // }, function (error) {
            //     console.log(error);
            //     alert('This is embarassing. An error has occurred. Please check the log for details');
            // });

            $scope.servsaletempitems = [ ];
            $scope.newservsaletemp = { };
            $scope.servdpercent = 0;
            
            $scope.getServData = function(){
                console.log("Sale temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/servsaletemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.saletemp = response.data.saletemp;
                    $scope.servsaletempitems = response.data.items;
                    $scope.customers = response.data.customers;
                    $scope.currencies = response.data.currencies;
                    console.log(response);
                }, function (error) {
                    console.log(error);
                });
            };
            $scope.getServData();

            $scope.addServSaleTemp = function(id) {
                
                $http({
                    method: 'POST',
                    url: 'api/servsaletemp',
                    data: { sale_temp_id: $scope.tempid, service_id: id }
                }).then(function (response) {
                    if(response.data.status == 'DUPL') {
                        Swal.fire({
                            type: 'info',
                            title: 'DUPLICATES...',
                            text: response.data.msg
                        });
                    }else{
                        $scope.getServData();
                    }
                });
            }

            $scope.updateSaleServTemp = function(newservsaletemp) {

                $http({
                    method: 'PUT',
                    url: 'api/servsaletemp/' + newservsaletemp.id, 
                    data: { no_of_repeatition: newservsaletemp.no_of_repeatition, servprice: newservsaletemp.price, disc_percent: newservsaletemp.disc_percent, total_discount: newservsaletemp.total_discount, with_vat: newservsaletemp.with_vat }
                }).then(function (response) {
                    $scope.getServData();
                });
            }    

            $scope.updateServSaleTempDiscount = function(service_discount) {
                var total=0;
                angular.forEach($scope.servsaletempitems , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });
                $scope.servdpercent = (service_discount/total)*100;
               
                angular.forEach($scope.servsaletempitems, function(newservsaletemp){
                    $http({
                        method: 'PUT',
                        url: 'api/servsaletemp/' + newservsaletemp.id,
                        data: { no_of_repeatition: newservsaletemp.no_of_repeatition, servprice: newservsaletemp.price, disc_percent: $scope.servdpercent, total_discount: newservsaletemp.total_discount, with_vat: newservsaletemp.with_vat }
                    }).then(function (response) {
                        $scope.getServData();
                    });
                });
            }

            $scope.removeSaleServTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/servsaletemp/'+id
                }).then(function(response) {
                    $scope.getData();
                });
            }
            
            $scope.sumServ = function(list) {
                var total=0;
                angular.forEach(list , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });
                return total;
            }

            $scope.sumServDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newservsaletemp){
                    t_discount+= newservsaletemp.total_discount;
                });
                return t_discount;
            }

            $scope.sumServVAT = function(list){
                var t_vat=0;
                angular.forEach(list, function(newservsaletemp){
                    t_vat+= parseFloat(newservsaletemp.vat_amount);
                });
                return t_vat;
            } 

            $scope.updateSaleTempInfo = function(saletemp) {
                $http({
                    method: 'PUT',
                    url: 'pos/' + saletemp.id,
                    data: { 
                        customer_id: saletemp.customer_id,
                        date_set: saletemp.date_set,
                        sale_date: saletemp.sale_date,
                        sale_type: saletemp.sale_type,
                        pay_type: saletemp.pay_type,
                        currency: saletemp.currency,
                        ex_rate_mode: saletemp.ex_rate_mode,
                        local_ex_rate: saletemp.local_ex_rate,
                        foreign_ex_rate: saletemp.foreign_ex_rate,
                        due_date: saletemp.due_date,
                        comments: saletemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                    $scope.getServData();
                });       
            }

            $scope.updateSaleCustomerID = function(customer_id) {
                $http({
                    method: 'PUT',
                    url: 'pos/' + $scope.saletemp.id,
                    data: { 
                        customer_id: customer_id,
                        date_set: $scope.saletemp.date_set,
                        sale_date: $scope.saletemp.sale_date,
                        sale_type: $scope.saletemp.sale_type,
                        pay_type: $scope.saletemp.pay_type,
                        currency: $scope.saletemp.currency,
                        ex_rate_mode: $scope.saletemp.ex_rate_mode,
                        local_ex_rate: $scope.saletemp.local_ex_rate,
                        foreign_ex_rate: $scope.saletemp.foreign_ex_rate,
                        due_date: $scope.saletemp.due_date,
                        comments: $scope.saletemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });       
            }
        };
    }]);
})();