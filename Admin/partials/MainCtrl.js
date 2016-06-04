angular.module(appName).controller('MainCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $log, Extention) {

    $scope.UserMessages = [];

    // $rootScope.$on("socketDataChanged", function(){
    //     if($rootScope.globalSearchActive && $scope.pagingParams.searchType==1)
    //         $scope.checkNowOnline();
    // });

    $scope.checkNowOnline = function () {
        return
        var ous =  $scope.socketData.OnlineUsers;
        for (var i = 0 ; i < ous.length ; i++){
            for (var j = 0 ;j < $rootScope.searchResult.length; j++){
                if($scope.searchResult[j].UserID == ous[i].ID ){
                    $scope.searchResult[j].isOnline = true;
                }else{
                    $scope.searchResult[j].isOnline = false;
                }
            }
        }
    }

    $scope.pagingParams = {
        searchValue : '',
        searchType : '0'
    };

    //Extention.post("getUserNotifications").then(function (res) {
    //    $scope.notifications = res;
    //});

    //Extention.post("getUserMessages").then(function (res) {

    //    $scope.messages = res;
    //});


    $scope.fullSearchData = {SearchType : '0'};

    $scope.pagingControllerSearch = {};

    $scope.search = function () {
        if(!$scope.pagingParams.searchValue){
            $rootScope.globalSearchActive = false;
            return;
        }
        $scope.pagingControllerSearch.update();
        $rootScope.globalSearchActive = true;
        $scope.pageTitle = $state.current.resolve.$title();

    }

    $scope.searchBoxChanged = function () {
        if(!$scope.pagingParams.searchValue && $rootScope.globalSearchActive){
            $rootScope.globalSearchActive = false;
            return;
        }
    }

    $scope.searchTypeChanges = function (type) {
        $scope.pagingParams.searchType = type;
        $scope.search();
    }

    $scope.backFromSearch = function () {
        $rootScope.globalSearchActive = false;
        $scope.searchResult = [];
    }
    $scope.getRandomColorClass = function(id){
        var i = id % $scope.bgColorArray.length;
        return $scope.bgColorArray[i];
    }

    $scope.notificationsUpdating = false;

    $scope.updateNotifications = function (event) {
        event.stopPropagation();

        if( !$scope.notificationsUpdating ){
            $scope.notificationsUpdating = true;
            Extention.postAsync("getUserNotifications", {UserID: $rootScope.user.UserID }).then(function (res) {
                $scope.notifications = res;
                $scope.notificationsUpdating = false;
            });
        }else{
            Extention.popInfo('لطفا کمی صبر کنید...');
        }

    }

});