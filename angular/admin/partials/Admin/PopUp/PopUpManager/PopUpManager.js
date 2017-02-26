
angular.module(appName).controller('PopUpManagerCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $stateParams, $uibModal, Extention) {

    $scope.pagingController={};
    $scope.deletePopUp = function (Data) {
        Extention.post('deletePopUp', { PopUpID: Data.ID}).then(function (res) {
            if (res && res.Status == 'success') {
                Extention.popSuccess("پیام با موفقیت حذف شد!");
                $scope.pagingController.update();
            } else {
                Extention.popError("مشکل در حذف پیام ، لطفا دوباره امتحان کنید.");
            }
        });
    }
    $scope.openPopUpModal  = function (Data) {
        $uibModal.open({
            animation: true,
            templateUrl: 'popUp.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.popup = Data;
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

    $scope.openPopUpUpdate  = function (Data) {
        $uibModal.open({
            animation: true,
            templateUrl: 'popUpUpdate.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.mindate =Date.now();
                $scope.ExpireDate=new Date(Date.parse(Data.ExpireDate.replace('-','/','g'))).getTime();
                console.log($scope.mindate);
                $scope.popup = Data;
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.upgrade = function () {
                    if(!$scope.toFull.unix){
                        Extention.popError('تاریخ اتمام پاپ آپ را انتخاب کنید');return;
                    }
                    var ExpireDate = new Date($scope.toFull.unix);
                    Extention.post('updatePopUp',{ExpireDate : ExpireDate , PopUpID:$scope.popup.ID}).then(function (res) {
                        if(res && res.Status=='success'){
                            Extention.popSuccess("پاپ آپ با موفقیت تمدید شد!");
                            $scope.cancel();
                        }else{
                            Extention.popError("مشکل در تمدید پاپ آپ ، لطفا دوباره امتحان کنید.");
                        }
                    });
                }
            },
            size: 'md'
        });
    }
    activeElement('#SPopUp' ,'#SPopUps');
});