angular.module('myApp').controller('MainCtrl', function($scope, $templateCache, $state, $rootScope, $routeParams, $uibModal, Extention ) {


    $scope.user = {};
    $scope.emailError = false;
    $scope.userNameError = false;
    $scope.userNameRegex = (/^(?=.*[a-z])[0-9a-zA-Z]{3,}$/);
    $scope.allPositions = [];
    $scope.Position = {};

    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        $scope.allPositions = msg;
    });

    $scope.checkEmail = function (value) {
        if (value) {
            Extention.postAsync('checkEmail', { value: value }).then(function (msg) {
                $scope.emailError = !msg;
                if ($scope.emailError)
                    Extention.popError('این ایمیل قبلا ثبت شده است');
            });
        }
    }

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

    $scope.savePerson = function () {
        if ($scope.user.roleAccepted && $scope.signUpForm.$valid && !$scope.user.emailError) {
            $scope.user.OrganizationID = $scope.Position.selected.ID;
            Extention.post('savePerson', $scope.user).then(function (msg) {
                console.log(msg);
                if (msg.Status == 'success') {
                    Extention.popSuccess('اطلاعات شما با موفقیت ثبت شد .لطفا ایمیل خود را بررسی کنید تا از صحت ایمیل شما مطمئن شویم. بعد از تایید مدیر، شما می توانید وارد انجمن شوید',12000);
                } else if (msg.Status == 'notAccepted') {
                    Extention.popInfo('ایمیل فعال سازی جدیدی برای شما ارسال شده است');
                } else {
                    Extention.popError('اطلاعات شما نا معتبر است.');
                }
            });
        }
    }

    $scope.logInUser = function () {
        getPage('Forum');
    }

    $scope.signOutUser = function () {
        Extention.post('logout', {}).then(function (msg) {
            $rootScope.userSession = {};
        });
    }

    $scope.signInFunc = function () {
        if ($scope.signInForm.$valid) {
            Extention.post('signInUser', $scope.signIn).then(function (msg) {
                //console.log(msg);
                if (msg.Status == 'success') {
                    if (msg.IsAdmin) {
                        getPage('Admin');
                    } else {
                        getPage('Forum');
                    }
                    
                } else {
                    console.log(msg);
                    Extention.popError('اطلاعات شما نا معتبر است.');
                }
            });
        }
    }

    $scope.mainOptions = {
        anchors: ['firstPage'/*, 'secondPage', '3rdPage', '4thpage', 'lastPage'*/],
        menu: '#menu'
    };

    $scope.openForgetPassModal = function () {
        $uibModal.open({
            animation: true,
            templateUrl: 'passModal.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.sentPassToEmail = function() {
                    if ($scope.Email) {
                        Extention.post('forgetPassword', {Email :$scope.Email}).then(function (msg) {
                            if (msg.Status == 'success') {
                                Extention.popSuccess('پسورد جدید برای ایمیل شما ارسال شده است');
                            } else {
                                console.log(msg);
                                Extention.popError('اطلاعات شما نا معتبر است.');
                            }
                        });
                    }
                }

                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
            },
            size: 'md'
        });
    }

    //$scope.moog = function (merg) { console.log(merg); };
    

    //TODO slide properties : 
    //$scope.slides = [
    //    {
    //        subject: 'رادیویی',
    //        image: 'images/broadcast.png',
    //        title: 'Simple',
    //        description: 'Easy to use. Configurable and customizable.',
    //        src: 'images/1.png'
    //    },
    //    {
    //        anchors:'testSlide',
    //        title: 'Cool',
    //        description: 'It just looks cool. Impress everybody with a simple and modern web design!',
    //        src: 'images/2.png'
    //    },
    //    {
    //        title: 'Compatible',
    //        description: 'Working in modern and old browsers too!',
    //        src: 'images/3.png'
    //    },
    //    {
    //        title: 'Compatible',
    //        description: 'Working in modern and old browsers too!',
    //        src: 'images/3.png'
    //    }
    //];

    //$scope.addSlide = function () {
    //    $scope.slides.push({
    //        title: 'New Slide',
    //        description: 'I made a new slide!'
    //    });
    //};

});