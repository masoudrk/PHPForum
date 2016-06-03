
angular.module(appName).controller('AllUsersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.pagingParams = { userType: null };
	$scope.pagingController = {};
	$scope.user = {};
    $scope.dropDwonTitle = 'نمایش اعضا';

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

	$scope.changeTypeFilter = function(type) {
	    $scope.pagingParams.userType = type;
	    switch (type) {
	        case null:
	            $scope.dropDwonTitle = 'همه ی اعضا';
	            break;
            case 1:
                $scope.dropDwonTitle = 'اعضای تایید شده';
                break;
            case 0:
                $scope.dropDwonTitle = 'اعضا در انتظار تایید';
                break;
            case -1:
                $scope.dropDwonTitle = 'اعضای تایید نشده';
                break;
	        default:
	            $scope.dropDwonTitle = 'نمایش اعضا';
                break;
	    }
	    $scope.search();
	}
	activeElement('#SUsers');
});