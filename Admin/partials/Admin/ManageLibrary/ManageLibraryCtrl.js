
angular.module(appName).controller('ManageLibraryCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $uibModal) {

    $scope.pagingParams = { };
	$scope.pagingController = {};
    $scope.dropDwonTitle = 'نمایش جواب ها';

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.removeFile = function (file) {
	    Extention.post('deleteFile', { ID: file.ID, FileID: file.FileID, AbsolutePath: file.AbsolutePath }).then(function (res) {
	        console.log(res);
	        if (res && res.Status == 'success') {
			    Extention.popSuccess("فایل با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در حذف فایل ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.changeTypeFilter = function(type) {
	    $scope.pagingParams.answerType = type;
	    switch (type) {
	        case null:
	            $scope.dropDwonTitle = 'همه ی جواب ها';
	            break;
            case 1:
                $scope.dropDwonTitle = 'جواب ها تایید شده';
                break;
            case 0:
                $scope.dropDwonTitle = 'جواب ها در انتظار تایید';
                break;
            case -1:
                $scope.dropDwonTitle = 'جواب ها تایید نشده';
                break;
	        default:
	            $scope.dropDwonTitle = 'نمایش جواب ها';
                break;
	    }
	    $scope.search();
	}


	activeElement('#SLibrary', '#SFilaeManage');
});