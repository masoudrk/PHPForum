
angular.module(appName).controller('AllAdminsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};
	$scope.adminType = { selected : {} }
	$scope.adminTypes = [];
	$scope.selectedAdmin = {};

	Extention.post('getAllAdminTypes', { }).then(function (res) {
	    if (res && res.Status == 'success') {
	        $scope.adminTypes = res.Data;
	    }
	});

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.updateAdmin = function() {
	    if ($scope.adminType.selected && $scope.adminUserID) {
	        $scope.selectedAdmin["PermissionID"] = $scope.adminType.selected.ID;
	        $scope.selectedAdmin["UserID"] = $scope.adminUserID;
	        console.log($scope.selectedAdmin);
	        Extention.post('updateAdmin', $scope.selectedAdmin).then(function (res) {
	            if (res && res.Status == 'success') {
	                Extention.popSuccess("مدیر با موفقیت اضافه شد!");
	                $scope.pagingController.update();
	            }
	        });
	    }
	}

	$scope.deleteAdmin = function (uid) {
	    Extention.post('deleteAdmin', { AdminID: uid }).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("کاربر با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
				Extention.popError("مشکل در حذف کاربر ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.editAdmin = function(admin) {
	    for (var i = 0; i < $scope.adminTypes.length; i++) {
	        if ($scope.adminTypes[i].ID == admin.PID) {
	            $scope.adminType.selected = $scope.adminTypes[i];
                break;
	        }
	    }
	    $scope.adminUserID = Number(admin.UserID);
	}
	activeElement('#SAdmins');
});