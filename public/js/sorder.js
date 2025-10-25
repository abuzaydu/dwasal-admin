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
        
        $scope.saleOrderId = function(sale_order_id){
            $scope.orderid = sale_order_id;
            $scope.saleorder = { };
            $scope.customers = [ ];
            $scope.saleorderitems = [ ];
            $scope.newsaleorder = { };
            $scope.defaultCustomer;
            $scope.getData = function(){
                // console.log("Sale order id = "+$scope.orderid);
                $http({
                    method: 'GET',
                    url: 'api/so-items/'+$scope.orderid
                }).then(function (response) {
                    $scope.saleorder = response.data.saleorder;
                    $scope.saleorderitems = response.data.items;
                    $scope.customers = response.data.customers;
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
                console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            // Function to replicate setInterval using $timeout service.
            $scope.intervalFunction = function(){
                $timeout(function() {
                  $scope.getData();
                  $scope.intervalFunction();
                }, 15000)
            };

            $scope.inputValue = '';
            $scope.getInputVal = function(obj) {
                return $scope.inputValue.replace('', obj).replace(/,/g, '');
            };

            $scope.addOrderItem = function(item) {
                if (item.in_stock == 0.00) {
                    Swal.fire({
                        type: 'warning',
                        title: 'EMPTY STOCK...',
                        text: 'The stock of '+item.name+' is currently ZERO. Please Purchase new Stock.'
                    });
                }else{
                    if (item.retail_price == null) {
                        Swal.fire({
                            type: 'warning',
                            title: 'NO SELLING PRICE...',
                            text: 'Selling price for  '+item.name+' is not set. Please update price for this product.'
                        });
                    }else{
                        $http({
                            method: 'POST',
                            url: 'api/so-items', 
                            data: { sale_order_id: $scope.orderid, product_id: item.id, retail_price: item.retail_price }
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
                            }else{
                                $scope.getData();
                            }
                            console.log(response);
                        }, function (error) {
                            console.log(error);
                        });
                    }
                }
            }

            $scope.updateSaleOrder = function(newsaleorder) {
                // alert(newsaleorder.sold_in);
                $http({
                    method: 'PUT',
                    url: 'api/so-items/' + newsaleorder.id,
                    data: { quantity: newsaleorder.quantity, product_unit_id: newsaleorder.product_unit_id, quantity_packed: newsaleorder.quantity_packed, retail_price: newsaleorder.retail_price, price: newsaleorder.retail_price * newsaleorder.quantity, discount: newsaleorder.discount, disc_percent: newsaleorder.disc_percent, total_discount: newsaleorder.total_discount, sold_in: newsaleorder.sold_in, with_vat:newsaleorder.with_vat }
                }).then(function (response) {
                    if (response.data.status == 'LOW') {
                        Swal.fire({
                            type: 'info',
                            title: 'LOW STOCK...',
                            text: response.data.msg
                        });
                    }else if(response.data.status == 'WRONG') {
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }else if(response.data.status == 'VAT UPDATED' || response.data.status == 'UNIT UPDATED' || response.data.status == 'DISCOUNT UPDATED') {
                        $scope.getData();
                    }

                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.updateSaleOrderDiscount = function(list) {
                // alert('Tester');
                var total=0;
                angular.forEach(list , function(newsaleorder){
                    total+= parseFloat(newsaleorder.retail_price * newsaleorder.quantity);
                });
                var dpercent = $scope.total_discount/total;

                // alert('You have added a Discount of '+ Math.round(dpercent*100)+ '% to each item sold.');
                angular.forEach(list, function(newsaleorder){ 
                    $http({
                        method: 'PUT',
                        url: 'api/so-items/' + newsaleorder.id,
                        data: { quantity: newsaleorder.quantity, price: newsaleorder.retail_price * newsaleorder.quantity, discount: newsaleorder.retail_price*dpercent, disc_percent: dpercent, total_discount: newsaleorder.price*dpercent, sold_in: newsaleorder.sold_in }
                    }).then(function (response) {
                        $scope.getData();
                    });
                });
            }     

            $scope.removeSaleOrder = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/so-items/'+id
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newsaleorder) {
                    total+= parseFloat(newsaleorder.retail_price * newsaleorder.quantity);
                });
                return total;
            }

            $scope.sumDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newsaleorder) {
                    t_discount+= parseFloat(newsaleorder.discount * newsaleorder.quantity);
                });
                return t_discount;
            }

            $scope.sumVAT = function(list){
                var t_vat=0;
                angular.forEach(list, function(newsaleorder) {
                    t_vat+= parseFloat(newsaleorder.vat_amount);
                });
                return t_vat;
            }

            $scope.updateSaleOrderInfo = function(saleorder) {
                $http({
                    method: 'POST',
                    url: 'update-so',
                    data: { 
                        id: saleorder.id,
                        customer_id: saleorder.customer_id,
                        sale_type: saleorder.sale_type,
                        due_date: saleorder.due_date,
                        comments: saleorder.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });       
            }
        };
    }]);
})();