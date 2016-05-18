
angular.module(appName).controller('AllUsersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

	$scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};

	$scope.search = function () {
		$scope.pagingController.update();
	}
	$scope.changeUserState = function (uid , s) {
		Extention.post('changeUserAccepted',{State : s,UserID:uid}).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("وضعیت کاربر با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
				Extention.popError("مشکل در تغییر وضعیت کاربر ، لطفا دوباره تلاش کنید.");
			}
		});
	}
	$scope.removeUser = function (uid) {
		Extention.post('deleteUser',{UserID:uid}).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("کاربر با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
				Extention.popError("مشکل در حذف کاربر ، لطفا دوباره امتحان کنید.");
			}
		});
	}
	
	fixFooter();
});