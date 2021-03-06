angular.module('myApp').controller('MainCtrl', function ($scope, $templateCache, $state, $rootScope, $routeParams, $uibModal, Extention, $cookies) {

    Extention.postAsync('checkPopUp', {}).then(function (msg) {
        console.log(msg);
        return;
        if (msg.Status == 'success' && msg.Data != null) {
            var popUp = $cookies.get("popup");
            if (popUp) {
                return;
            } else {
                var expireDate = new Date();
                expireDate.setDate(expireDate.getDate() + 1);
                // Setting a cookie
                $cookies.put('popup', 'true', {'expires': expireDate, 'path': '/'});
                $uibModal.open({
                    animation: true,
                    templateUrl: 'popUp.html',
                    controller: function ($scope, $uibModalInstance) {
                        $scope.popup = msg.Data;
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                    },
                    size: 'md'
                });
            }
        }
    });

    $scope.user = {};
    $scope.emailError = false;
    $scope.userNameError = false;
    $scope.userNameRegex = (/[أ-ي]/);
    $scope.allPositions = [];
    $scope.Position = {};

    Extention.postAsync('getAllPositions', {}).then(function (msg) {
        $scope.allPositions = msg;
    });

    $scope.checkEmail = function (value) {
        if (value) {
            Extention.postAsync('checkEmail', {value: value}).then(function (msg) {
                if (msg.Status === 'success') {
                    $scope.emailError = !msg.Data;
                    if ($scope.emailError)
                        Extention.popError('این ایمیل قبلا ثبت شده است');
                } else {
                    Extention.popError('خطا در ارتباط');

                }
            });
        }
    };

    $scope.openRoleModal = function () {
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
    };

    $scope.savePerson = function () {
        if ($scope.user.roleAccepted && $scope.signUpForm.$valid && !$scope.user.emailError) {
            $scope.user.OrganizationID = $scope.Position.selected.ID;
            Extention.post('savePerson', $scope.user).then(function (msg) {
                console.log(msg);
                if (msg.Status === 'success') {
                    $scope.user = {};
                    Extention.popSuccess('اطلاعات شما با موفقیت ثبت شد .لطفا ایمیل خود را بررسی کنید. ', 10000);
                } else if (msg.Status === 'emailError') {
                    Extention.popError('این ایمیل قبلا ثبت شده است');
                } else {
                    Extention.popError('اطلاعات شما نا معتبر است');
                }
            });
        } else if (!$scope.signUpForm.name.$valid) {
            Extention.popError('لطفا نام خودرا فارسی وارد کنید');
        } else {
            Extention.popError('لطفا تمام اطلاعات را وارد کنید');
        }
    };

    $scope.logInUser = function () {
        getPage('Forum/home.php');
    };

    $scope.signOutUser = function () {
        Extention.post('logout', {}).then(function (msg) {
            $rootScope.userSession = {};
        });
    };

    $scope.signInFunc = function () {
        if ($scope.signInForm.$valid) {
            Extention.post('signInUser', $scope.signIn).then(function (msg) {
                //console.log(msg);
                if (msg.Status === 'success') {
                    if (msg.IsAdmin) {
                        getPage('Admin');
                    } else {
                        //getPage('Forum');
                        getPage('Forum/home.php');
                    }
                } else if (msg.Status === 'notAccepted') {
                    Extention.popError('اکانت شما هنوز  توسط ادمین تایید نشده است');
                } else {
                    console.log(msg);
                    Extention.popError('اطلاعات شما نا معتبر است.');
                }
            });
        }
    };

    $scope.mainOptions = {
        anchors: ['firstPage'/*, 'secondPage', '3rdPage', '4thpage', 'lastPage'*/],
        menu: '#menu'
    };

    $scope.openForgetPassModal = function () {
        $uibModal.open({
            animation: true,
            templateUrl: 'passModal.html',
            controller: function ($scope, $uibModalInstance) {
                $scope.sentPassToEmail = function () {
                    if ($scope.Email) {
                        Extention.post('forgetPassword', {Email: $scope.Email}).then(function (msg) {
                            if (msg.Status === 'success') {
                                Extention.popSuccess('پسورد جدید برای ایمیل شما ارسال شده است');
                            } else {
                                console.log(msg);
                                Extention.popError('اطلاعات شما نا معتبر است.');
                            }
                        });
                    }
                };

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