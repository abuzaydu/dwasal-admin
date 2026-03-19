var app = angular.module('ExpenseApp', []);

    app.directive('stringToNumber', function () {
        return {
            require: 'ngModel',
            link: function (scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function (value) {
                    return parseFloat(value) || 0;
                });
                ngModel.$formatters.push(function (value) {
                    return parseFloat(value) || 0;
                });
            }
        };
    });

    app.controller('ExpenseCtrl', function ($scope, $http) {

        $scope.expense     = {};
        $scope.expItems    = [];
        $scope.expenseTypes = [];

        $scope.initExpense = function (id) {
            $scope.expense.id = id;
            $scope.loadExpenseItems(id);
            $scope.loadExpenseTypes();
        };

        $scope.loadExpenseTypes = function () {
            $http.get('/fetch-expense-types').then(function (res) {
                $scope.expenseTypes = res.data;
            });
        };

        $scope.loadExpenseItems = function (expenseId) {
            $http.get('/fetch-expense-items', { params: { expense_id: expenseId } })
                .then(function (res) {

                    $scope.expItems = res.data.map(function (item) {
                        item.quantity    = parseFloat(item.quantity)    || 0;
                        item.unit_price  = parseFloat(item.unit_price)  || 0;
                        item.total_price = parseFloat(item.total_price) || 0;
                        return item;
                    });
                });
        };
    
        $scope.addExpenseItem = function (expType) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $http({
                method: 'POST',
                url: '/add-expense-item',
                data: { vms_expense_id: $scope.expense.id, expense_type_id: expType.id, _token: token },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest: function (obj) {
                    var str = [];
                    for (var p in obj) str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
                    return str.join('&');
                }
            }).then(function (res) {
                $scope.expItems.push(res.data);
            });
        };

        $scope.updateExpenseItem = function (item) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $http({
                method: 'POST',
                url: '/update-expense-item',
                data: {
                    id:          item.id,
                    quantity:    parseFloat(item.quantity)    || 0,
                    unit_price:  parseFloat(item.unit_price)  || 0,
                    total_price: parseFloat(item.total_price) || 0,
                    _token: token
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest: function (obj) {
                    var str = [];
                    for (var p in obj) str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
                    return str.join('&');
                }
            });
        };

        $scope.removeExpenseItem = function (id) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $http({
                method: 'POST',
                url: '/remove-expense-item',
                data: { id: id, _token: token },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                transformRequest: function (obj) {
                    var str = [];
                    for (var p in obj) str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
                    return str.join('&');
                }
            }).then(function () {
                $scope.expItems = $scope.expItems.filter(function (i) { return i.id !== id; });
            });
        };

        $scope.sumQty = function (items) {
            var total = 0;
            angular.forEach(items, function (i) { total += parseFloat(i.quantity) || 0; });
            return total;
        };

        $scope.sumTotal = function (items) {
            var total = 0;
            angular.forEach(items, function (i) { total += parseFloat(i.total_price) || 0; });
            return total;
        };

        $scope.selectedItems = function (items) {
            return items ? items.length : 0;
        };


        $scope.calcTotal = function (item) {
            var qty   = parseFloat(item.quantity)   || 0;
            var price = parseFloat(item.unit_price) || 0;
            item.total_price = qty * price;
            $scope.updateExpenseItem(item);
        };

    });