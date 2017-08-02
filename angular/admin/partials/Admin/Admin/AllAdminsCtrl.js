
angular.module(appName).controller('AllAdminsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.action = 'افزودن';
    $scope.pagingParams = {};
	$scope.pagingController = {};
	$scope.user = {};
    $scope.adminType = { selected: null };
    $scope.forumType = { selected: null };
	$scope.adminTypes = [];
    $scope.forumTypes = [];
    $scope.Person = {};
    $scope.selectedAdmin = {};

	Extention.post('getAllAdminTypes', { }).then(function (res) {
	    if (res && res.Status == 'success') {
	        $scope.adminTypes = res.Data;
	    }
	});

	Extention.post('getAllForumTypes', {}).then(function (res) {
	    if (res && res.Status == 'success') {
	        $scope.forumTypes = res.Data;
	    }
	});

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.updateAdmin = function() {
	    if ($scope.adminType.selected && $scope.Person.selected) {
	        if ($scope.adminType.selected.Permission == 'Administrator')
	            $scope.selectedAdmin["ForumID"] = ($scope.forumType.selected) ? $scope.forumType.selected.ID : null;
	        else
	            $scope.selectedAdmin["ForumID"] = null;
	        $scope.selectedAdmin["PermissionID"] = $scope.adminType.selected.ID;
	        $scope.selectedAdmin["UserID"] = $scope.Person.selected.ID;
	        console.log($scope.selectedAdmin);
	        Extention.post('updateAdmin', $scope.selectedAdmin).then(function (res) {
	            if (res && res.Status == 'success') {
                    $scope.search();
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
	    $scope.forumType.selected = null;
        if (admin.ForumID != null) {
            for (var i = 0; i < $scope.forumTypes.length; i++) {
                if ($scope.forumTypes[i].ID == admin.ForumID) {
                    $scope.forumType.selected = $scope.forumTypes[i];
                    break;
                }
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