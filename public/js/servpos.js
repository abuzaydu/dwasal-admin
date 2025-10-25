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

    app.controller("SearchItemCtrl", [ '$scope', '$http', function($scope, $http) {
        
        $scope.saleTempId = function(sale_temp_id){

            $scope.tempid = sale_temp_id;
            $scope.servitems = [ ];
            $http({
                method: 'GET',
                url: 'api/servitem'
            }).then(function (response) {
                $scope.servitems = response.data;
                console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            $scope.saletemp = { };
            $scope.customers = [ ];
            $scope.currencies = [ ];
            $scope.servsaletempitems = [ ];
            $scope.newservsaletemp = { };
            $scope.dpercent = 0;
            
            $scope.getData = function(){
                console.log("Sale temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/servsaletemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.saletemp = response.data.saletemp;
                    $scope.servsaletempitems = response.data.items;
                    $scope.customers = response.data.customers;
                    $scope.currencies = response.data.currencies;
                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            };
            $scope.getData();

            $scope.addSaleTemp = function(id) {
                
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
                        $scope.getData();
                    }
                });
            }

            $scope.updateSaleTemp = function(newservsaletemp) {

                $http({
                    method: 'PUT',
                    url: 'api/servsaletemp/' + newservsaletemp.id, 
                    data: { no_of_repeatition: newservsaletemp.no_of_repeatition, servprice: newservsaletemp.price, disc_percent: newservsaletemp.disc_percent, total_discount: newservsaletemp.total_discount, with_vat: newservsaletemp.with_vat }
                }).then(function (response) {
                    $scope.getData();
                });
            }    

            $scope.updateSaleTempDiscount = function(sale_discount) {
                var total=0;
                angular.forEach($scope.servsaletempitems , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });

                $scope.dpercent = (sale_discount/total)*100;

                angular.forEach($scope.servsaletempitems, function(newservsaletemp){
                    $http({
                        method: 'PUT',
                        url: 'api/servsaletemp/' + newservsaletemp.id,
                        data: { no_of_repeatition: newservsaletemp.no_of_repeatition, servprice: newservsaletemp.price, disc_percent: $scope.dpercent, total_discount: newservsaletemp.total_discount, with_vat: newservsaletemp.with_vat }
                    }).then(function (response) {
                    });
                });

                $scope.getData();
            }

            $scope.removeSaleTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/servsaletemp/'+id
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });
                return total;
            }

            $scope.sumDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newservsaletemp){
                    t_discount += newservsaletemp.total_discount;
                });
                return t_discount;
            }

            $scope.sumVAT = function(list){
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