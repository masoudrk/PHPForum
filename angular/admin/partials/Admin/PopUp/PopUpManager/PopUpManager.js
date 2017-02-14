
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
    activeElement('#SContact');
});