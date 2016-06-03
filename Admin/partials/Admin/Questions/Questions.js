
angular.module(appName).controller('QuestionsCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $stateParams, $uibModal) {
    $scope.adminType = $stateParams.id;
    $scope.pagingParams = { SubjectName: $stateParams.id, questionType: null};
	$scope.pagingController = {};
	$scope.dropDwonTitle = 'نمایش سوال ها';

	$scope.search = function () {
		$scope.pagingController.update();
	}

	$scope.changeQuestionState = function (uid, s, AuthorID ) {
	    Extention.post('changeQuestionAccepted', { State: s, QuestionID: uid, AdminPermissionLevel: session.AdminPermissionLevel, UserID: AuthorID }).then(function (res) {
			if(res && res.Status == 'success'){
			    Extention.popSuccess("وضعیت سوال با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در تغییر وضعیت سوال ، لطفا دوباره تلاش کنید.");
			}
		});
	}
	$scope.removeQuestion = function (uid ,AuthorID) {
	    Extention.post('deleteQuestion', { QuestionID: uid, AdminPermissionLevel: session.AdminPermissionLevel, UserID: AuthorID }).then(function (res) {
			if(res && res.Status=='success'){
			    Extention.popSuccess("سوال با موفقیت حذف شد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در حذف سوال ، لطفا دوباره امتحان کنید.");
			}
		});
	}

	$scope.changeTypeFilter = function(type) {
	    $scope.pagingParams.answerType = type;
	    switch (type) {
	        case null:
	            $scope.dropDwonTitle = 'همه ی سوال ها';
	            break;
            case 1:
                $scope.dropDwonTitle = 'سوال ها تایید شده';
                break;
            case 0:
                $scope.dropDwonTitle = 'سوال ها در انتظار تایید';
                break;
            case -1:
                $scope.dropDwonTitle = 'سوال ها تایید نشده';
                break;
	        default:
	            $scope.dropDwonTitle = 'نمایش سوال ها';
                break;
	    }
	    $scope.search();
	}

	$scope.openRoleModal = function (title , text ) {
	    $uibModal.open({
	        animation: true,
	        templateUrl: 'myModalContent.html',
	        controller: function ($scope, $uibModalInstance) {
	            $scope.Text = text;
	            $scope.Title = title;
	            $scope.cancel = function () {
	                $uibModalInstance.dismiss('cancel');
	            };
	        },
	        size: 'md'
	    });
	}

	$scope.openExchangeModal = function (question) {
	    Extention.post('getSubjects', { }).then(function (res) {
	        if (res && res.Status == 'success') {
	            var modalInstance = $uibModal.open({
	                animation: true,
	                templateUrl: 'ExchangeModal.html',
	                controller: function ($scope, $uibModalInstance) {
	                    $scope.subjects = res.Data;
	                    $scope.question = question;
	                    $scope.cancel = function () {
	                        $uibModalInstance.dismiss('cancel');
	                    };

	                    $scope.send = function (subject) {
	                        Extention.post('exchangeQuestion', { SubjectID: subject.ID, QuestionID: $scope.question.ID}).then(function (res) {
	                            if (res && res.Status == 'success') {
	                                Extention.popSuccess("سوال با موفقیت انتقال داده شد!");
	                                $uibModalInstance.dismiss('cancel');
	                            } else {
	                                Extention.popError("مشکل در انتقال سوال ، لطفا دوباره امتحان کنید.");
	                            }
	                        });
	                    };
	                },
	                size: 'md'
	            });

	            modalInstance.result.then(function () {
	            }, function () {
	                $scope.pagingController.update();
	            });

	        } else {
	            return;
	        }
	    });
	}

	//$scope.openExchangeModal = function (question) {
	//    Extention.post('getCommonMessages', { filter: 'رد سوال' }).then(function (res) {
	//        if (res && res.Status == 'success') {
	//            $uibModal.open({
	//                animation: true,
	//                templateUrl: 'ExchangeModal.html',
	//                controller: function ($scope, $uibModalInstance) {
	//                    $scope.common = res.Data;
	//                    $scope.question = question;
	//                    $scope.cancel = function () {
	//                        $uibModalInstance.dismiss('cancel');
	//                    };

	//                    $scope.send = function (message) {

	//                    };
	//                },
	//                size: 'md'
	//            });
	//        } else {
	//            return;
	//        }
	//    });
	//}

    console.log('#S' + $stateParams.id);
    activeElement('#SQuestions', '#SS' + $stateParams.id);
	fixFooter();

});