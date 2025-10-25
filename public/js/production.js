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
		$scope.mros = [ ];
		$scope.pms = [ ];
		$scope.rms = [ ];
        $scope.stages = [ ];
		$scope.products = [ ];
		$scope.product_made = [ ]; 
        $scope.falsetrue = false;

        $scope.getItems = function() {
            $http({
                method: 'GET',
                url: 'api/prod-items'
            }).then(function(response){
                $scope.stages = response.data.stages;
                $scope.mros = response.data.mros;
                $scope.pms = response.data.pms;
                $scope.rms = response.data.rms;
                $scope.products = response.data.products;
                $scope.product_made =response.data.product_made; 
            });
        }
        $scope.rmusedtemps = [ ];
        $scope.getRM = function() {
            $http({
            method: 'GET',
            url : 'api/prod-rmusedtemp'
            }).then(function(response){
                    $scope.rmusedtemps = response.data;
            });
        }

        $scope.dlctemps = [ ];
        $scope.getDLC = function(){
            $http({
            method: 'GET',
            url : 'api/prod-dlctemp'
            }).then(function(response){
                    $scope.dlctemps = response.data;
            });
        }

        $scope.mrousedtemps = [ ];
        $scope.getMro = function(){
            $http({
            method: 'GET',
            url : 'api/prod-mrousedtemp'
            }).then(function(response){
                    $scope.mrousedtemps = response.data;
            });
        }

        $scope.pmusedtemps = [ ];
        $scope.getPM = function(){
            $http({
            method: 'GET',
            url : 'api/prod-pmusedtemp'
            }).then(function(response){
                   $scope.pmusedtemps = response.data;
                if ($scope.pmusedtemps.length > 0) {
                    $scope.falsetrue = true;
                }else{
                    $scope.falsetrue = false;
                }
            });
        }

        $scope.getData = function(){
            $scope.getItems();
            $scope.getRM();
            $scope.getMro();
            $scope.getDLC();
            $scope.getPM();
        }

        $scope.getData();

// *********** RAW MATERIALS *******************//
               
        $scope.addRM = function(){
            $http({
                method: 'POST',
                url : 'api/prod-rmusedtemp',
                data: { rm_id: $scope.rm_id }
            }).then(function(response){
                $scope.rm_id = '';
                $scope.getData();
            });
        }

        $scope.updateRMTemp = function(newrmusedtemp) {
            $http({
                method: 'PUT',
                url : 'api/prod-rmusedtemp/' + newrmusedtemp.id,
                data: { quantity: newrmusedtemp.quantity, unit_cost: newrmusedtemp.unit_cost, total: newrmusedtemp.total}
            }).then(function(response){
                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: response.data.msg
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                     }
                }
                $scope.getData();
            });
        }   

        $scope.removeRMTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/prod-rmusedtemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

        $scope.sumRM = function(list) {
            var total=0;
            angular.forEach(list , function(rmusedtemps){
                total+= parseFloat(rmusedtemps.total);
            });
            return total;
        }

// *********** PACKING MATERIALS ************//
		$scope.addPM = function(){
        	$http({
                method: 'POST',
                url : 'api/prod-pmusedtemp',
                data: { pm_id: $scope.pm_id }
            }).then(function(response){
                $scope.pm_id;
            	$scope.getData();
            });
        }

         $scope.updatePMTemp = function(newpmusedtemp) {
            $http({
                method: 'PUT',
                url: 'api/prod-pmusedtemp/'+ newpmusedtemp.id,
                data: {quantity: newpmusedtemp.quantity, unit_packed: newpmusedtemp.unit_packed, product_packed: newpmusedtemp.product_packed, unit_cost: newpmusedtemp.unit_cost, total: newpmusedtemp.total}
            }).then(function(response){

                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: 'Packing Material stock is less than quantity your utilizing. Please update stock'
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: data.msg
                        });
                     }
                }
                // $scope.getData();
            });
        }   

        $scope.removePMTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/prod-pmusedtemp/' + id
            }).then(function(response) {
                $scope.getData();
            });
        }

        $scope.sumPM = function(list) {
            var total=0;
            angular.forEach(list , function(pmusedtemps){
                total+= parseFloat(pmusedtemps.total);
            });
            return total;
        }

//***************** MROS **********************//

        $scope.addMro = function(mro){
        	$http({
                method: 'POST',
                url : 'api/prod-mrousedtemp',
                data: {mro_id: $scope.mro_id }
            }).then(function(response){
                $scope.mro_id = '';
                $scope.getData();
            });
        }

        $scope.updateMroTemp = function(newmrousedtemp) {
            $http({
                method : 'PUT',
                url : 'api/prod-mrousedtemp/' + newmrousedtemp.id,
                data : { quantity: newmrousedtemp.quantity, unit_cost: newmrousedtemp.unit_cost, total: newmrousedtemp.total} 
            }).then(function(response){
                if(response.data.status == 'WRONG'){
                    Swal.fire({
                        type: 'info',
                        title: 'WRONG QTY...',
                        text: response.data.msg
                    });
                }
                // $scope.getData();
            });
        }   

        $scope.removeMroTemp = function(id) {
            $http({
                method: 'DELETE',
                url: 'api/prod-mrousedtemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

         $scope.sumMro = function(list) {
            var total=0;
            angular.forEach(list , function(mrousedtemps){
                total+= parseFloat(mrousedtemps.total);
            });
            return total;
        }

    //***************** PRODUCTS ******************//

        $scope.addDLC = function(){
            $http({
                method: 'POST',
                url : 'api/prod-dlctemp',
                data: {production_stage_id: $scope.stage_id}
            }).then(function(response){
                $scope.stage_id = '';
                $scope.getData();
            });
        }

        $scope.updateDLCTemp = function(newdlctemp) {
            $http({
                method : 'PUT',
                url : 'api/prod-dlctemp/' + newdlctemp.id,
                data : { quantity: newdlctemp.quantity, unit_cost: newdlctemp.unit_cost, total: newdlctemp.total} 
            }).then(function(response){
                if(response.data.status == 'WRONG'){
                    Swal.fire({
                        type: 'info',
                        title: 'WRONG QTY...',
                        text: response.data.msg
                    });
                }
                // $scope.getData();
            });
        }   

        $scope.removeDLCTemp = function(id) {
            $http({
                method: 'DELETE',
                url: 'api/prod-dlctemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

         $scope.sumDLC = function(list) {
            var total=0;
            angular.forEach(list , function(dlctemps){
                total+= parseFloat(dlctemps.total);
            });
            return total;
        }

    //***************** PRODUCTS ******************//

        function getProductMade(){
            $http({
                method: 'GET',
                url : 'api/product-made'
            }).then(function(response){
                $scope.product_made = response.data.product_made;
                if ($scope.pmusedtemps.length > 0) {
                    $scope.falsetrue = true;
                }else{
                    $scope.falsetrue = false;
                }
            }, function(error){

            });
        }

    	$scope.AddProducts = function(product_made){
    		$http({
    			method : 'POST',
    			url: 'api/prod-items/create',
    			data: {product_packed : product_made }
    		}).then(function(response){
                if(response.data.status == 'warning' ){
                    Swal.fire({
                        type: 'warning',
                        title: 'DUPLICATES PRODUCT',
                        text: response.data.msg
                    });
                }else{
                    $scope.getData();
                }
    			
    		});
    	}

        $scope.updateProducts = function(product){
            $http({
                method : 'PUT',
                url: 'api/prod-items/' + product.id,
                data: { qty : product.qty ,
                    cost_per_unit : product.cost_per_unit,
                    selling_price : product.selling_price,
                    profit_margin: product.profit_margin,
                    unit_packed: product.unit_packed
               }
            }).then(function(response){
                $scope.getData();
            });
        }

        $scope.removeProduct = function(id) {
             $http({
                method : 'DELETE',
                url: 'api/prod-items/' + id
            }).then(function(response){
                    $scope.getData();
            } , function(error){

            });
        }

        function recalculate() {
             $http({
                method : 'GET',
                url: 'api/prod-items/recalculate'
            }).then(function(response){
            });

        }
        
        $scope.sumVolProduced = function(list){
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.qty*product_made.unit_packed);
            });
            return total;
        }

        $scope.sumUnitPacked = function(list){
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.unit_packed);
            });
            return total;
        }   

        $scope.sumQty = function(list) {
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.qty);
            });
            return total;
        }

        $scope.updateTemp = function() {
            $scope.getData();
        }
	}]);
})();