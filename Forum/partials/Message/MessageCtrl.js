
angular.module(appName).controller('MessageCtrl', function ($scope, $rootScope, $stateParams, $state,
                                                            $location, $timeout,$uibModal , Extention,Upload) {

    if($stateParams.id){
        Extention.post('getUserMessageByID',{ MessageID:$stateParams.id})
            .then(function (res) {
            $scope.showMessage(res);
        })
    }

    $scope.showMessage = function (message) {

        if(message.MessageViewed==0){
            Extention.postAsync('markAsReadMessage',{MessageID : message.ID}).then(function (res) {
                for(var i = 0 ; i < $scope.message.all.length ; i++ ){
                    if($scope.message.all[i].ID == res.Data){
                        $scope.message.all[i].MessageViewed = 1;
                        break;
                    }
                }

                var notifyMessages = $scope.$parent.messages.All;
                for(var i = 0 ; i < notifyMessages.length ; i++ ){
                    if(notifyMessages[i].ID == res.Data){
                        notifyMessages.splice(i,1);
                        var notifyMessages = $scope.$parent.messages.Total -=1;
                        break;
                    }
                }
            });
        }

        $uibModal.open({
            animation: true,
            templateUrl: 'messageModal.html',
            controller: function ($scope , $uibModalInstance , Message) {
                $scope.message =  Message;

                $scope.getAdmin = function () {
                    $state.go('UserProfile',{id : message.SenderUserID});
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