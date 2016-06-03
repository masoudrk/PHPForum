
angular.module(appName).controller('MessageCtrl', function ($scope, $rootScope, $routeParams, $state,
                                                            $location, $timeout,$uibModal , Extention,Upload) {

    $scope.showMessage = function (message) {

        $uibModal.open({
            animation: true,
            templateUrl: 'messageModal.html',
            controller: function ($scope , $uibModalInstance , Message) {
                $scope.message =  Message;

                $scope.getAdmin = function () {
                    $state.go('UserProfile',{id : message.AdminUserID});
                    $uibModalInstance.dismiss('cancel');
                }
                $scope.cancel = function () { $uibModalInstance.dismiss('cancel'); }
            },
            size: 'md',
            resolve: {
                Message: function () {
                    return message;
                }
            }
        });
    }

    activeElement('#SMessage');
});