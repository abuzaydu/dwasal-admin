(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        $scope.items = [ ];

        $http({
            method: 'GET',
            url : 'api/mro-items'
        }).then(function(response){
             $scope.items = response.data;
        });

  
        $scope.mrousedtemp = [ ];
        $scope.newmrousedtemp = { };
        $http({
            method : 'GET',
            url : 'api/mrousedtemp'
        }).then(function(response){
            $scope.mrousedtemp = response.data;
        });

        function getData(){
           $http({
                method : 'GET',
                url : 'api/mrousedtemp'
            }).then(function(response){
                $scope.mrousedtemp = response.data;
            }); 
        }

        $scope.addUsedMroTemp = function(item, newmrousedtemp) {  
            $http({
                method: 'POST',
                url : 'api/mrousedtemp',
                data : { mro_id: item.id, quantity: 0, unit_cost: 0 }
            }).then(function(response) {
                getData();
            });
        }

        $scope.updateMroTemp = function(newmrousedtemp) {
            $http({
                method : 'PUT',
                url : 'api/mrousedtemp/' + newmrousedtemp.id,
                data : { quantity: newmrousedtemp.quantity, unit_cost: newmrousedtemp.unit_cost, total: newmrousedtemp.total} 
            }).then(function(response){
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

        $scope.removeMroTemp = function(id) {
            $http({
                method: 'DELETE',
                url: 'api/mrousedtemp/' + id
            }).then(function(response){
                getData();
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newmrousedtemp){
                total+= parseFloat(newmrousedtemp.total);
            });
            return total;
        }

    }]);
})();