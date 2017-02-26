
angular.module(appName).controller('EducationCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {
    $scope.pagingParams = {};
	$scope.pagingController = {};

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.insertEducation= function() {
	    if ($scope.educationName) {
	        Extention.post('insertEducation', { Name: $scope.educationName }).then(function (res) {
	            if (res && res.Status == 'success') {
	                Extention.popSuccess("مدرک اضافه شد");
	                $scope.pagingController.update();
	            } else {
	                Extention.popError("مشکل در وارد کردن مدرک ، لطفا دوباره تلاش کنید.");
	            }
	        });
	    }
	}

	$scope.removeEducation = function (uid) {
	    Extention.post('deleteEducation', { ID: uid}).then(function (res) {
			if(res && res.Status=='success'){
			    Extention.popSuccess("مدرک با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در حذف مدرک ، لطفا دوباره امتحان کنید.");
			}
		});
	}
	activeElement('#SMeta', '#SEducation');
	fixFooter();

});