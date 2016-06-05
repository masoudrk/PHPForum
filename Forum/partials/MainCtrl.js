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
    $scope.activity = {
        low : '#e74c3c',
        medium : '#f1c40f',
        high : '#2ecc71'
    };
    
    $scope.bgColorArray= ["bg-aqua-active","bg-purple-active","bg-red-active","bg-navy-active","bg-orange-active",
        "bg-blue-active","bg-green-active","bg-olive-active","bg-lime-active",
        "bg-fuchsia-active","bg-teal-active","bg-yellow-active","bg-maroon-active","bg-light-blue-active","bg-black-active",
        "bg-green","bg-navy","bg-teal","bg-olive","bg-lime","bg-orange","bg-fuchsia","bg-purple","bg-maroon","bg-red",
        "bg-yellow","bg-aqua","bg-blue","bg-light-blue","bg-black"];

    $scope.pagingParams = {
        searchValue : '',
        searchType : '0'
    };

    $scope.notificationsUpdating = true;
    $scope.messagesUpdating = true;

    Extention.post("getUserNotifications").then(function (res) {
        $scope.notifications = res.Data;
        $scope.notificationsUpdating = false;
    });

    Extention.post("getUserMessages").then(function (res) {
        $scope.messages = res;
        $scope.messagesUpdating = false;
    });

    Extention.post("getUserMessages").then(function (res) {

        $scope.messages = res;
    });


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

    $scope.updateMessages = function (event) {
        if (event)
            event.stopPropagation();

        if (!$scope.messagesUpdating) {
            $scope.messagesUpdating = true;
            Extention.postAsync("getUserMessages").then(function (res) {
                $scope.messages = res;
                $scope.messagesUpdating = false;
            });
        } else {
            Extention.popInfo('لطفا کمی صبر کنید...');
        }
    }


    $scope.updateNotifications = function (event) {
        if(event)
            event.stopPropagation();

        if( !$scope.notificationsUpdating ){
            $scope.notificationsUpdating = true;
            Extention.postAsync("getUserNotifications").then(function (res) {
                $scope.notifications = res.Data;
                $scope.notificationsUpdating = false;
            });
        }else{
            Extention.popInfo('لطفا کمی صبر کنید...');
        }

    }

    $scope.markLastNotifications = function (event) {
        event.stopPropagation();

        if( !$scope.notificationsUpdating ){
            $scope.notificationsUpdating = true;
            Extention.postAsync("markLastNotifications").then(function (res) {
                $scope.notifications = res.Data;
                $scope.notificationsUpdating = false;
            });
        }else{
            Extention.popInfo('لطفا کمی صبر کنید...');
        }
    }


});