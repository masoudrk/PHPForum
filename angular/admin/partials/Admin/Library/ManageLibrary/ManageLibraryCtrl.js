
angular.module(appName).controller('ManageLibraryCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, Extention, $uibModal) {

    $scope.pagingParams = { };
	$scope.pagingController = {};

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


    $scope.changeFileState = function (uid, s) {
        Extention.post('changeLibraryFileAccepted', { State: s, LibraryID: uid.ID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: uid.UserID }).then(function (res) {
            if(res && res.Status == 'success'){
                Extention.popSuccess("وضعیت فایل با موفقیت تغییر کرد!");
                $scope.pagingController.update();
            }else{
                Extention.popError("مشکل در تغییر وضعیت فایل ، لطفا دوباره تلاش کنید.");
            }
        });
    }

    $scope.openDiscardModal = function (LibraryID, UserID) {
        Extention.post('getCommonMessages', { filter: 'رد فایل' }).then(function (res) {
            if (res && res.Status == 'success') {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'DiscardAnswerModal.html',
                    controller: function ($scope, $uibModalInstance) {
                        $scope.common = res.Data;
                        $scope.LibraryID = LibraryID;
                        $scope.UserID = UserID;
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };

                        $scope.send = function (message) {
                            Extention.post('changeLibraryFileAccepted', { State: -1, LibraryID: $scope.LibraryID, AdminPermissionLevel: session.AdminPermissionLevel, UserID: $scope.UserID, Message: message }).then(function (res) {
                                if (res && res.Status == 'success') {
                                    Extention.popSuccess("وضعیت فایل با موفقیت تغییر کرد!");
                                    $uibModalInstance.dismiss('done');
                                } else {
                                	console.log(res);
                                    Extention.popError("مشکل در تغییر وضعیت فایل ، لطفا دوباره تلاش کنید.");
                                }
                            });
                        };
                    },
                    size: 'md'
                });
                modalInstance.result.then(function (stat) {
                    if(stat == 'done')
                        $scope.pagingController.update();
                }, function (stat) {
                    if(stat == 'done')
                        $scope.pagingController.update();
                });
            } else {
                return;
            }

        });
    }
    $scope.openRoleModal = function (File) {
		console.log(File);
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.File = File;
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

	activeElement('#SLibrary', '#SFilaeManage');
});