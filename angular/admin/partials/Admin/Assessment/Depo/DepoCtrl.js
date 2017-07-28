
angular.module(appName).controller('DepoCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention) {

    $scope.action = 'افزودن';
    $scope.pagingParams = {};
	$scope.pagingController = {};
    $scope.organ = { selected: {} };
    $scope.organs = [];
    $scope.depo = {};

	Extention.post('Assessment/getAllOrgans', { }).then(function (res) {
	    if (res && res.Status == 'success') {
	        $scope.organs = res.Data;
	    }
	});

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.updateDepo = function() {
	    if ($scope.organ.selected && $scope.depo.Name) {

            $scope.depo["OrganPositionID"] = $scope.organ.selected.ID;
	        Extention.post('Assessment/addOrUpdateDepo', $scope.depo).then(function (res) {
	            if (res && res.Status == 'success') {
                    $scope.search();
	                if($scope.action == 'افزودن')
                    Extention.popSuccess("دپو با موفقیت اضافه شد!");
	                else if ($scope.action == 'ویرایش') {
	                    Extention.popSuccess("دپو با موفقیت ویرایش شد!");
	                    $scope.action = 'افزودن';
	                }
                    $scope.depo = {};
                    $scope.organ.selected = null;
	                $scope.pagingController.update();
	            }
	        });
	    }else {
	    	Extention.popError('اطلاعات را درست وارد کنید');
		}
	}

	$scope.deleteDepo = function (uid) {
	    Extention.post('Assessment/deleteDepo', { ID: uid }).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("دپو با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
				Extention.popError("مشکل در حذف دپو ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.editDepo = function(depo) {
	    for (var i = 0; i < $scope.organs.length; i++) {
	        if ($scope.organs[i].ID == depo.OrganPositionID) {
	            $scope.organ.selected = $scope.organs[i];
                break;
	        }
	    }
	    $scope.depo = depo;
	    $scope.action = 'ویرایش';
	}

	activeElement('#SAssessment' , '#SDepo');
});