
angular.module(appName).controller('AllUsersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	$scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};

	$scope.search = function () {
		$scope.pagingController.update();
	}
	
	fixFooter();
});