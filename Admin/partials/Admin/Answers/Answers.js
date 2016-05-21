
angular.module(appName).controller('AnswersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $stateParams, $uibModal) {
    $scope.adminType = $stateParams.id;
    $scope.pagingParams = { SubjectName: $stateParams.id, answerType: null};
	$scope.pagingController = {};
    $scope.dropDwonTitle = 'نمایش جواب ها';

	$scope.search = function () {
		$scope.pagingController.update();
	}
	$scope.changeAnswerState = function (uid, s) {
	    Extention.post('changeAnswerAccepted', { State: s, AnswerID: uid, AdminPermissionLevel: session.AdminPermissionLevel }).then(function (res) {
			if(res && res.Status == 'success'){
			    Extention.popSuccess("وضعیت جواب با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در تغییر وضعیت جواب ، لطفا دوباره تلاش کنید.");
			}
		});
	}
	$scope.removeAnswer = function (uid) {
	    Extention.post('deleteAnswer', { AnswerID: uid, AdminPermissionLevel: session.AdminPermissionLevel }).then(function (res) {
			if(res && res.Status=='success'){
				Extention.popSuccess("جواب با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در حذف جواب ، لطفا دوباره امتحان کنید.");
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


	$scope.openRoleModal = function (text) {
	    $uibModal.open({
	        animation: true,
	        templateUrl: 'myModalContent.html',
	        controller: function ($scope, $uibModalInstance) {
	            $scope.Text = text;
	            $scope.cancel = function () {
	                $uibModalInstance.dismiss('cancel');
	            };
	        },
	        size: 'md'
	    });
	}

    console.log('#S' + $stateParams.id);
	activeElement('#SAnswers', '#S' + $stateParams.id);
	fixFooter();

});