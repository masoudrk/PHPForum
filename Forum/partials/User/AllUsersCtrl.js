
angular.module(appName).controller('AllUsersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	$scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};

	$scope.search = function () {
		$scope.pagingController.update();
	}


	$scope.changeUserState = function (s) {
		Extention.post('changeUserAccepted',{state : s}).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("وضعیت کاربر با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
				Extention.popError("مشکل در تغییر وضعیت کاربر ، لطفا دوباره تلاش کنید.");
			}
		});
	}
	
});