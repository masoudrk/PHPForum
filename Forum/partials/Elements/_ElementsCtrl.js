angular.module(appName).controller('ElementsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $log) {

    $scope.fixPersian = function () {
        fixFooter();
    }
	fixFooter();
});