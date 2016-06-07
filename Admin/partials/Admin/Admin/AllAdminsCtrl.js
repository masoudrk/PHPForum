
angular.module(appName).controller('AllAdminsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.action = 'افزودن';
    $scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};
	$scope.adminType = { selected : {} }
	$scope.adminTypes = [];
    $scope.Person = {};
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
	    if ($scope.adminType.selected && $scope.Person.selected) {
	        $scope.selectedAdmin["PermissionID"] = $scope.adminType.selected.ID;
	        $scope.selectedAdmin["UserID"] = $scope.Person.selected.ID;
	        console.log($scope.selectedAdmin);
	        Extention.post('updateAdmin', $scope.selectedAdmin).then(function (res) {
	            if (res && res.Status == 'success') {
	                if($scope.action == 'افزودن')
	                    Extention.popSuccess("مدیر با موفقیت اضافه شد!");
	                else if ($scope.action == 'ویرایش') {
	                    Extention.popSuccess("مدیر با موفقیت ویرایش شد!");
	                    $scope.action = 'افزودن';
	                }
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
	    $scope.Person.selected = { ID: admin.UserID, FullName: admin.FullName };
	    $scope.action = 'ویرایش';
	}

	$scope.getPersons = function (filter) {
	    Extention.postAsync('getUsersByName', { filter: filter }).then(function (res) {
	        if (res && res.Status == 'success') {
	            $scope.users = res.Data;
	        } else {
	            Extention.popError("مشکل در اتصال.");
	        }
	    });
	}

	activeElement('#SAdmins');
});