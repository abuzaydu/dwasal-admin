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

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        
        $scope.pmPurchaseId = function(pm_purchase_temp_id){
            
            $scope.items = [ ];
            $scope.tempid = pm_purchase_temp_id;

            $http({
                method: 'GET',
                url : 'api/pmitem'
            }).then(function(response){

                $scope.items = response.data.packing_materials;
            });

            $scope.pmitemtemp = [ ];
            $scope.newpmitemtemp = { };
            $scope.pmtemp = { };
            $scope.suppliers = [ ];
            $scope.currencies = [ ];
            $scope.defaultSupplier;
            $http({
                method: 'GET',
                url: 'api/pmitemtemp'
            }).then(function(response){
                $scope.pmitemtemp = response.data.itemtemps;
                $scope.suppliers = response.data.suppliers;
                $scope.pmtemp = response.data.pmtemp;
                $scope.currencies = response.data.currencies;

                console.log($scope.pmitemtemp);
            });

            function getData(){
                $http({
                    method: 'GET',
                    url: 'api/pmitemtemp'
                }).then(function(response){
                    $scope.pmitemtemp = response.data.itemtemps;
                    $scope.pmtemp = response.data.pmtemp;
                    $scope.suppliers = response.data.suppliers;
                    $scope.currencies = response.data.currencies;

                });
            }
     
            $scope.addStockTemp = function(item, newpmitemtemp , tempid) {
                $http({
                    url: 'api/pmitemtemp',
                    method: 'POST',
                    data :  {packing_material_id: item.id, qty: 0 , pm_purchase_temp_id: tempid}
                }).then(function(response){
                    getData();
                }, function(error){
                    console.log(error);
                });
            }

            $scope.updateStockTemp = function(newpmitemtemp) {
                $http({
                    method: 'PUT',
                    url : 'api/pmitemtemp/' + newpmitemtemp.id,
                    data : { qty: newpmitemtemp.qty, unit_cost: newpmitemtemp.unit_cost, total: newpmitemtemp.total}
                }).then(function(response){
                    
                    console.log(response.data);
                    if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }
                    getData();
                });
            }   

            $scope.removeStockTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/pmitemtemp/' + id
                }).then(function(response){
                    getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newpmitemtemp){
                    total+= parseFloat(newpmitemtemp.total);
                });

                return total;
            }

            $scope.updatePmTempInfo = function(pmtemp) {

                $http({
                    method: 'PUT',
                    url: 'pm-temp/' + pmtemp.id,
                    data: { 
                        supplier_id: pmtemp.supplier_id,
                        date_set: pmtemp.date_set,
                        date: pmtemp.date,
                        purchase_type: pmtemp.purchase_type,
                        pay_type: pmtemp.pay_type,
                        currency: pmtemp.currency,
                        grn_no :pmtemp.grn_no,
                        order_no : pmtemp.order_no,
                        invoice_no : pmtemp.invoice_no, 
                        delivery_note_no : pmtemp.delivery_note_no,
                        ex_rate_mode: pmtemp.ex_rate_mode,
                        local_ex_rate: pmtemp.local_ex_rate,
                        foreign_ex_rate: pmtemp.foreign_ex_rate,
                        due_date: pmtemp.due_date,
                        comments: pmtemp.comments
                    }
                }).then(function (response) {
                    console.log(response.data);
                    getData();
                }, function(error) {
                    console.log(error);
                });       
            }

        }

        
    }]);
    
})();