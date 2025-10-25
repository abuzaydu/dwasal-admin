(function(){
    var app = angular.module('nstsoft', [ ]);

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
        
        $scope.reconcileTempId = function(reconcile_id){
            $scope.tempid = reconcile_id;
            $scope.reconciliation = {};
            $scope.reconciletemp = [ ];
            $scope.newreconciletemp = { };
            $scope.evidencetemp = [ ];

            $scope.getData = function(){
                $http({
                    method: 'GET',
                    url: 'api/reconciletemp/'+$scope.tempid
                }).then(function (response) {
                    console.log(response);
                    $scope.reconciliation = response.data.reconciliation;
                    $scope.reconciletemp = response.data.temps;
                    $scope.evidencetemp = response.data.evidence;
                });
            };

            $scope.getData();

            $scope.addReconcileTemp = function(reconciliation) {
                $http({
                    method: 'POST',
                    url : 'api/reconciletemp',
                    data: {request_id: reconciliation.id}
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.updateReconcileTemp = function(newreconciletemp) {
                $http({
                    method: 'PUT',
                    url : 'api/reconciletemp/' + newreconciletemp.id,
                    data : {item_description: newreconciletemp.item_description, item_category: newreconciletemp.item_category, no_of_days: newreconciletemp.no_of_days, quantity: newreconciletemp.quantity, price: newreconciletemp.price, is_passed: newreconciletemp.is_passed}
                }).then(function(response) {
                    console.log(response);
                }, function(error){
                    console.log(error);
                });
            }   

            $scope.removeReconcileTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url : 'api/reconciletemp/' +id 
                }).then(function(response) {
                    $scope.getData();
                });
            }

            
            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list, function(newreconciletemp){
                    total+= parseFloat(newreconciletemp.total);
                });
                return total;
            }

            $scope.removeImage = function(id){
                Swal.fire({
                    title: 'Are you sure, You want to remove the Image?',
                    showDenyButton: true,
                    confirmButtonText: 'Yes Remove',
                    denyButtonText: `Don't Remove`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $http({
                            method: 'DELETE',
                            url : 'api/evidencetemp/' +id 
                        }).then(function(response) {
                            $scope.getData();
                        });
                        // Swal.fire('Removed!', '', 'success')
                    } else if (result.isDenied) {
                        Swal.fire('Image not removed', '', 'info')
                    }
                });
            }
            
        };
    }]);
})();