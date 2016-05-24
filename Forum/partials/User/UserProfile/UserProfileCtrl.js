
angular.module(appName).controller('UserProfileCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $stateParams, Extention) {

    $scope.isOnline = false;
    $scope.profile = null;
    $scope.userID = $stateParams.id;
    Extention.post("getProfile", { TargetUserID:$scope.userID , UserID: $rootScope.user.UserID }).then(function (res) {
        $scope.profile = res;
        console.log($scope.profile);
    });

    $rootScope.$on("socketDataChanged", function(){
        $scope.checkNowOnline();
    });

    $scope.followPerson = function () {
        Extention.postAsync("followPerson", { TargetUserID: $scope.userID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.profile.PersonFollow = 1;
            }
        });
    }

    $scope.unFollowPerson = function () {
        Extention.postAsync("unFollowPerson", { TargetUserID: $scope.userID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.profile.PersonFollow = 0;
            }
        });
    }

    $scope.checkNowOnline = function () {
        var ous =  $scope.socketData.OnlineUsers;
        
        for (var i = 0 ; i < ous.length ; i++){
            if($scope.userID == ous[i].ID ){
                $scope.isOnline = true;
                return;
            }
        }
        $scope.isOnline = false;
    }
    $scope.checkNowOnline();

    activeElement('#SProfile');
});