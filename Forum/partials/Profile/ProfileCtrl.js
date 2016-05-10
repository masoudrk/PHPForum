
angular.module(appName).controller('ProfileCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.activeTab = 2;

    Extention.post('getUserProfile').then(function (res) {
        $scope.curUser = res;
    });

	fixFooter();
});