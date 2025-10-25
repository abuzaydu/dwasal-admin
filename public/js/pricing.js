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
        
        $scope.pricingTempId = function(pricing_temp_id){

            $scope.tempid = pricing_temp_id;
            $scope.pricing ={};
            $scope.materialcosts = [ ];
            $scope.newmaterial = { };
            $scope.labourcosts = [ ];
            $scope.newlabour = { };
            $scope.transportcosts = [ ];
            $scope.newtransport = { };
            $scope.indirectcosts = [ ];
            $scope.newindirect = { };
            $scope.localindirectcosts = [ ];
            $scope.newlocalindirect = { };
            $scope.packagecosts = [ ];
            $scope.newpackage = { };
            $scope.localpackagecosts = [ ];
            $scope.localnewpackage = { };
            $scope.handlingcosts = [ ];
            $scope.newhandleling = { };
            $scope.getData = function(){
                console.log("Product pricing Temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/material-costs/'+$scope.tempid
                }).then(function (response) {
                    $scope.pricing = response.data.pricing;
                    $scope.materialcosts = response.data.materialcosts;
                    $scope.labourcosts = response.data.labourcosts;
                    $scope.transportcosts = response.data.transportcosts;
                    $scope.indirectcosts = response.data.indirectcosts;
                    $scope.localindirectcosts = response.data.localindirectcosts;
                    $scope.packagecosts = response.data.packagecosts;
                    $scope.localpackagecosts = response.data.localpackagecosts;
                    $scope.handlingcosts = response.data.handlingcosts;
                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            };

            $scope.getData();

            $scope.updatePricingTemp = function(pricing) {
                $http({
                    method: 'POST',
                    url : 'api/update-pricing/' + pricing.id,
                    data: { 
                        product_id: pricing.product_id,
                        date: pricing.date,
                        currency: pricing.currency,
                        ex_rate: pricing.ex_rate,
                        min_order_value: pricing.min_order_value,
                        no_of_piece_per_set: pricing.no_of_piece_per_set,
                        shipping_import_fee: pricing.shipping_import_fee,
                        wholesale_eu_margin: pricing.wholesale_eu_margin,
                        vat: pricing.vat,
                        target_rrp: pricing.target_rrp,
                        domestic_w_margin: pricing.domestic_w_margin,
                        domestic_r_margin: pricing.domestic_r_margin,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updatePricingProduct = function(product_id) {
                $http({
                    method: 'POST',
                    url : 'api/update-pricing/' + $scope.pricing.id,
                    data: { 
                        product_id: product_id,
                        date: $scope.pricing.date,
                        currency: $scope.pricing.currency,
                        ex_rate: $scope.pricing.ex_rate,
                        min_order_value: $scope.pricing.min_order_value,
                        no_of_piece_per_set: $scope.pricing.no_of_piece_per_set,
                        shipping_import_fee: $scope.pricing.shipping_import_fee,
                        wholesale_eu_margin: $scope.pricing.wholesale_eu_margin,
                        vat: $scope.pricing.vat,
                        target_rrp: $scope.pricing.target_rrp,
                        domestic_w_margin: $scope.pricing.domestic_w_margin,
                        domestic_r_margin: $scope.pricing.domestic_r_margin,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updatePricingDate = function(date) {
                $http({
                    method: 'POST',
                    url : 'api/update-pricing/' + $scope.pricing.id,
                    data: { 
                        product_id: $scope.product_id,
                        date: date,
                        currency: $scope.pricing.currency,
                        ex_rate: $scope.pricing.ex_rate,
                        min_order_value: $scope.pricing.min_order_value,
                        no_of_piece_per_set: $scope.pricing.no_of_piece_per_set,
                        shipping_import_fee: $scope.pricing.shipping_import_fee,
                        wholesale_eu_margin: $scope.pricing.wholesale_eu_margin,
                        vat: $scope.pricing.vat,
                        target_rrp: $scope.pricing.target_rrp,
                        domestic_w_margin: $scope.pricing.domestic_w_margin,
                        domestic_r_margin: $scope.pricing.domestic_r_margin,
                    }
                }).then(function (response) {
                    $scope.getData();
                }); 
            }

            $scope.updateMaterialCosts = function(newmaterial) {
                $http({
                    method: 'PUT',
                    url: 'api/material-costs/' + newmaterial.id,
                    data: { unit_cost: newmaterial.unit_cost, no_of_piece_made: newmaterial.no_of_piece_made }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumMaterialCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newmaterial) {
                    total += parseFloat(newmaterial.cost_per_piece);
                });
                return total;
            }

            $scope.removeMaterialCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/material-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.addLabourCost = function() {
                $http({
                    method: 'POST',
                    url: 'api/labour-costs', 
                    data: { product_pricing_id: $scope.tempid }
                }).then(function (response) {
                    $scope.getData();
                });
            }

            $scope.updateLabourCosts = function(newlabour) {
                $http({
                    method: 'PUT',
                    url: 'api/labour-costs/' + newlabour.id,
                    data: { stage: newlabour.stage, daily_wage_rate: newlabour.daily_wage_rate, no_of_piece: newlabour.no_of_piece }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumLabourCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newlabour) {
                    total += parseFloat(newlabour.cost_per_piece);
                });
                return total;
            }
            
            $scope.removeLabourCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/labour-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addTransportCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/transport-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateTransportCosts = function(newtransport) {
                $http({
                    method: 'PUT',
                    url: 'api/transport-costs/' + newtransport.id,
                    data: { description: newtransport.description, transport_cost: newtransport.transport_cost, no_of_items: newtransport.no_of_items }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumTransportCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newtransport) {
                    total += parseFloat(newtransport.cost_per_unit);
                });
                return total;
            }

            $scope.removeTransportCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/transport-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.totaldirectcosts = 0;
            $scope.sumDirectCosts = function(materialcosts, labourcosts, transportcosts) {
                var mtotal=0;
                angular.forEach(materialcosts , function(newmaterial) {
                    mtotal += parseFloat(newmaterial.cost_per_piece);
                });

                var ltotal=0;
                angular.forEach(labourcosts , function(newlabour) {
                    ltotal += parseFloat(newlabour.cost_per_piece);
                });

                var ttotal=0;
                angular.forEach(transportcosts , function(newtransport) {
                    ttotal += parseFloat(newtransport.cost_per_unit);
                });


                $scope.totaldirectcosts =  mtotal+ltotal+ttotal;
                return mtotal+ltotal+ttotal;
            }

            $scope.addIndirectCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/indirect-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateIndirectCosts = function(newindirect) {
                $http({
                    method: 'PUT',
                    url: 'api/indirect-costs/' + newindirect.id,
                    data: { description: newindirect.description, percent: newindirect.percent, amount: $scope.totaldirectcosts*(newindirect.percent/100) }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumIndirectCostsPercent = function(list) {
                var tpercent = 0;
                angular.forEach(list , function(newindirect) {
                    tpercent += parseFloat(newindirect.percent);
                });
                return tpercent;
            }

            $scope.sumIndirectCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newindirect) {
                    total += parseFloat(newindirect.amount);
                });
                return total;
            }

            $scope.removeIndirectCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/indirect-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addLocalIndirectCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/local-indirect-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateLocalIndirectCosts = function(newlocalindirect) {
                $http({
                    method: 'PUT',
                    url: 'api/local-indirect-costs/' + newlocalindirect.id,
                    data: { description: newlocalindirect.description, percent: newlocalindirect.percent, amount: $scope.totaldirectcosts*(newlocalindirect.percent/100) }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumLocalIndirectCostsPercent = function(list) {
                var tpercent = 0;
                angular.forEach(list , function(newlocalindirect) {
                    tpercent += parseFloat(newlocalindirect.percent);
                });
                return tpercent;
            }

            $scope.sumLocalIndirectCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newlocalindirect) {
                    total += parseFloat(newlocalindirect.amount);
                });
                return total;
            }

            $scope.removeLocalIndirectCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/local-indirect-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addPackagingCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/packaging-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updatePackageCosts = function(newpackage) {
                $http({
                    method: 'PUT',
                    url: 'api/packaging-costs/' + newpackage.id,
                    data: { item_desc: newpackage.item_desc, package_cost: newpackage.package_cost, no_of_items: newpackage.no_of_items }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumPackageCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newpackage) {
                    total += parseFloat(newpackage.unit_cost);
                });
                return total;
            }

            $scope.removePackageCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/packaging-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addLocalPackagingCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/local-packaging-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateLocalPackageCosts = function(newlocalpackage) {
                $http({
                    method: 'PUT',
                    url: 'api/local-packaging-costs/' + newlocalpackage.id,
                    data: { item_desc: newlocalpackage.item_desc, package_cost: newlocalpackage.package_cost, no_of_items: newlocalpackage.no_of_items }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumLocalPackageCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newlocalpackage) {
                    total += parseFloat(newlocalpackage.unit_cost);
                });
                return total;
            }

            $scope.removeLocalPackageCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/local-packaging-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            $scope.addHandlingCost = function() {
                $http({
                    method: 'POST',
                    url : 'api/export-handling-costs',
                    data: {pricing_id: $scope.tempid}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateHandlingCosts = function(newhandleling) {
                $http({
                    method: 'PUT',
                    url: 'api/export-handling-costs/' + newhandleling.id,
                    data: { description: newhandleling.description, amount: newhandleling.amount }
                }).then(function (response) {
                    $scope.getData();
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.sumHandlingCosts = function(list) {
                var total=0;
                angular.forEach(list , function(newhandleling) {
                    total += parseFloat(newhandleling.amount);
                });
                return total;
            }

            $scope.removeHandlingCost = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/export-handling-costs/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }
        };
    }]);
})();