app.controller('authCtrl', function ($scope, $rootScope,$state, $routeParams, $uibModalInstance, $location, $http, Extention) {
    //initially set those objects to null to avoid undefined error

    $scope.login = {};
    $scope.signup = {};
    $scope.doLogin = function (user) {
        Extention.post('login', {
            customer: user
        }).then(function (results) {

            if (results.Status == "success") {
                Extention.authUser(results);
                $uibModalInstance.close(results);

                if (results.IsAdmin) {
                    Extention.toast({ status: "success", message: "ادمین گرامی به سایت خوش آمدید!" });
                    $state.go("admin_root.dashboard");
                }
                else  {
                    Extention.toast({ status: "success", message: "کاربر گرامی به سایت خوش آمدید!" });
                    $state.go("user_root.dashboard");
                }

            } else {
                Extention.toast({ status: "error", message: "خطا ، اطلاعات وارد شده نادرست است." });
            }

        });
    };
    $scope.signup = {email:'',password:'',name:'',phone:'',address:''};
    $scope.signUp = function (customer) {
        if (!$scope.checkEmail(customer.email)) {
            Extention.popError('ایمیل وارد شده معتبر نیست!');
            return;
        }
        if (!customer.username || customer.username.length < 5) {
            Extention.popError('نام کاربری بایستی حداقل 5 کارکتر باشد!');
            return;
        }
        if (!customer.password || customer.password.length < 5) {
            Extention.popError('رمز وارد شده بایستی حداقل 5 کاراکتر باشد!');
            return;
        }
        if (customer.password != customer.password2) {
            Extention.popError('رمز وارد شده با تایید آن یکسان نیست!');
            return;
        }
        Extention.post('signUp', {
            customer: customer,
            recaptchaResponse: $scope.myRecaptchaResponse
        }).then(function (results) {
            if (results && results.Status == "success") {
                $uibModalInstance.close(results);
                Extention.authUser(results);
                Extention.toast({ status: "success", message: "ثبت نام با موفقیت انجام شد!" });
            } else {
                if (results.Status == "error-exists")
                    Extention.toast({ status: "error", message: "کاربری با این مشخصات ثبت نام کرده است!" });
                else if (results.Status == "error-captcha")
                    Extention.toast({ status: "error", message: "هویت شما شناخته نشد ، لطفا دوباره امتحان کنید." });
                else
                    Extention.toast({ status: "error", message: "خطا ، لطفا دوباره تلاش کنید." });
            }
        });
    };

    $scope.checkEmail = function (value) {
        if ($scope.EMAIL_REGEXP.test(value))
            return true;
        else
            return false;
    };

    $scope.EMAIL_REGEXP = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    
    $scope.ok = function () {
        $uibModalInstance.close($scope.selected.item);
    };

    $scope.cancel = function () {
        
        $uibModalInstance.dismiss('cancel');
    };
});