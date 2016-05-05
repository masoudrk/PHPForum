angular.module('myApp').controller('AdminCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $mdSidenav, $log, Extention) {

    $scope.checked = true;

    $scope.toggleRight = function () {
        $scope.checked = !$scope.checked;
    }
});