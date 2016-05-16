angular.module(appName).controller('MainCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $timeout, $log) {

    $scope.bgColorArray= ["bg-aqua-active","bg-purple-active","bg-red-active","bg-navy-active","bg-orange-active",
        "bg-blue-active","bg-green-active","bg-olive-active","bg-lime-active",
        "bg-fuchsia-active","bg-teal-active","bg-yellow-active","bg-maroon-active","bg-light-blue-active","bg-black-active",
        "bg-green","bg-navy","bg-teal","bg-olive","bg-lime","bg-orange","bg-fuchsia","bg-purple","bg-maroon","bg-red",
        "bg-yellow","bg-aqua","bg-blue","bg-light-blue","bg-black"];

    $scope.pagingParams = {
        searchValue : '',
        searchType : '0'
    };

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

    $scope.fixPage = function () {
        $timeout(function () {
            fixFooter();
        })
    }

    $scope.searchTypeChanges = function (type) {
        $scope.pagingParams.searchType = type;
        $scope.search();
    }

    $scope.backFromSearch = function () {
        $rootScope.globalSearchActive = false;
    }
    $scope.getRandomColorClass = function(id){
        var i = id % $scope.bgColorArray.length;
        return $scope.bgColorArray[i];
    }

});