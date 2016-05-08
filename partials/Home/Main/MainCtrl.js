angular.module('myApp').controller('MainCtrl', function($scope, $templateCache,$uibModal, $state, $rootScope, $routeParams, $uibModal, Extention) {
    
    $scope.openRoleModal = function() {
        $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.ok = function () {
                    $uibModalInstance.close($scope.selected.item);
                };

                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

    $scope.mainOptions = {
        sectionsColor: ['#1BBC9B', '#4BBFC3', '#7BAABE', 'whitesmoke', '#ccddff'],
        anchors: ['firstPage', 'secondPage', '3rdPage', '4thpage', 'lastPage'],
        menu: '#menu'
    };

    $scope.moog = function (merg) { console.log(merg); };

    $scope.slides = [
        {
            title: 'Simple',
            description: 'Easy to use. Configurable and customizable.',
            src: 'images/1.png'
        },
        {
            anchors:'testSlide',
            title: 'Cool',
            description: 'It just looks cool. Impress everybody with a simple and modern web design!',
            src: 'images/2.png'
        },
        {
            title: 'Compatible',
            description: 'Working in modern and old browsers too!',
            src: 'images/3.png'
        },
        {
            title: 'Compatible',
            description: 'Working in modern and old browsers too!',
            src: 'images/3.png'
        }
    ];

    //$scope.addSlide = function () {
    //    $scope.slides.push({
    //        title: 'New Slide',
    //        description: 'I made a new slide!'
    //    });
    //};

});