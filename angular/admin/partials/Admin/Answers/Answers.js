
angular.module(appName).controller('AnswersCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $stateParams, $uibModal) {
    $scope.adminType = $stateParams.id;
    $scope.pagingParams = { SubjectName: $stateParams.id, answerType: null};
	$scope.pagingController = {};
    $scope.dropDwonTitle = 'نمایش جواب ها';

    $scope.Position = {};
    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        $scope.allPositions = msg;
    });

	$scope.search = function () {
		$scope.pagingController.update();
	}
	$scope.changeAnswerState = function (uid, s) {
	    Extention.post('changeAnswerAccepted', { State: s, AnswerID: uid.ID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: uid.AuthorID, AuthorID: uid.QuestionAuthorID, QuestionID: uid.QuestionID }).then(function (res) {
			if(res && res.Status == 'success'){
			    Extention.popSuccess("وضعیت جواب با موفقیت تغییر کرد!");
				$scope.pagingController.update();
			}else{
			    Extention.popError("مشکل در تغییر وضعیت جواب ، لطفا دوباره تلاش کنید.");
			}
		});
	}
	$scope.removeAnswer = function (uid, AuthorID) {
	    Extention.post('deleteAnswer', { AnswerID: uid, AdminPermissionLevel: session.AdminPermissionLevel, UserID: AuthorID }).then(function (res) {
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

	$scope.openDiscardModal = function (answerID, authorID) {
	    Extention.post('getCommonMessages', { filter: 'رد جواب' }).then(function (res) {
	        if (res && res.Status == 'success') {
	            var modalInstance = $uibModal.open({
	                animation: true,
	                templateUrl: 'DiscardAnswerModal.html',
	                controller: function ($scope, $uibModalInstance) {
	                    $scope.common = res.Data;
	                    $scope.answerID = answerID;
	                    $scope.authorID = authorID;
	                    $scope.cancel = function () {
	                        $uibModalInstance.dismiss('cancel');
	                    };

	                    $scope.send = function (message) {
	                        Extention.post('changeAnswerAccepted', { State: -1, AnswerID: $scope.answerID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: $scope.authorID, Message: message }).then(function (res) {
	                            if (res && res.Status == 'success') {
	                                Extention.popSuccess("وضعیت جواب با موفقیت تغییر کرد!");
	                                $uibModalInstance.dismiss('cancel');
	                            } else {
	                                Extention.popError("مشکل در تغییر وضعیت جواب ، لطفا دوباره تلاش کنید.");
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

	$scope.openRoleModal = function (answer) {
	    var modalInstance = $uibModal.open({
	        animation: true,
	        templateUrl: 'myModalContent.html',
	        controller: function ($scope, $uibModalInstance) {
	            $scope.Answer = answer;
	            $scope.AnswerText = $scope.Answer.AnswerText;
				$scope.QuestionText= $scope.Answer.QuestionText;
	            $scope.cancel = function () {
	                $uibModalInstance.dismiss('cancel');
	            };
	            $scope.editAnswer = function () {
	                if ($scope.AnswerText) {
	                    Extention.post('editAnswer', { AnswerID: $scope.Answer.ID, AnswerText: $scope.AnswerText }).then(function (res) {
	                        if (res && res.Status == 'success') {
	                            Extention.popSuccess("جواب با موفقیت ویرایش شد!");
	                            $scope.editMode = false;
	                        } else {
	                            Extention.popError("مشکل در ویرایش جواب ، لطفا دوباره امتحان کنید.");
	                        }
	                    });
	                }
	            }
	        },
	        size: 'md'
	    });
	    modalInstance.result.then(function () {
	    }, function () {
	        $scope.pagingController.update();
	    });
	}
	$scope.changePosition = function () {
	    $scope.pagingParams.OrganizationID = ($scope.Position.selected) ? $scope.Position.selected.ID : null;
	    $scope.search();
	}

	activeElement('#SAnswers', '#S' + $stateParams.id);
});