
angular.module(appName).controller('UserProfileCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $stateParams, Extention) {

    $scope.profile = null;
    Extention.post("getProfile", { TargetUserID: $stateParams.id, UserID: $rootScope.user.UserID }).then(function (res) {
        $scope.profile = res;
        console.log($scope.profile);
    });

    activeElement('#SProfile');
	fixFooter();
});