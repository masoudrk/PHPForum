
angular.module(appName).controller('AboutCtrl', function ($scope, $rootScope, $stateParams, $state, $timeout, Extention) {

    $scope.bgColorArray= ["bg-aqua-active","bg-orange","bg-purple-active","bg-navy-active","bg-orange-active",
        "bg-blue-active","bg-green-active","bg-light-blue","bg-lime-active",
        "bg-fuchsia-active","bg-yellow-active","bg-red-active","bg-maroon-active","bg-light-blue-active","bg-black-active",
        "bg-green","bg-navy","bg-teal","bg-olive","bg-lime","bg-fuchsia","bg-purple","bg-maroon","bg-teal-active","bg-red",
        "bg-yellow","bg-aqua","bg-blue","bg-olive-active"];

    $scope.getRandomColorClass = function(id){
        var i = id % $scope.bgColorArray.length;
        return $scope.bgColorArray[i];
    }
    
    Extention.post('getAllAdminsForAbout').then(function (res) {
        $scope.data = res;
    });

    activeElement('#About');
});