var app = angular.module('myApp', ['ngRoute', 'ngAnimate', 'toaster', 'ui.bootstrap', 'ui.router', 'oc.lazyLoad', 'angular-confirm', 'ADM-dateTimePicker', 'ngFileUpload', 'ui.select', '720kb.tooltips', 'ngCkeditor', 'treasure-overlay-spinner', 'cfp.hotkeys', 'vcRecaptcha', 'ui.router.title', 'ngMaterial', 'ngMessages', 'material.svgAssetsCache']);
//, 'angular-imagefit'
app.config([
    '$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider', 'tooltipsConfProvider', 'ADMdtpProvider', 'vcRecaptchaServiceProvider',
    function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, tooltipsConfProvider, ADMdtp, vcRecaptchaServiceProvider) {

        vcRecaptchaServiceProvider.setSiteKey('6LdFLB4TAAAAAH1sOhBD0ew9SQEgq6XCDytD0Slv');

        tooltipsConfProvider.configure({
            'smart': true,
            'size': 'small',
            'speed': 'fast'
        });

        ADMdtp.setOptions({
            calType: 'jalali',
            format: 'YYYY/MM/DD hh:mm',
            default: 'today'
        });

        $ocLazyLoadProvider.config({
            debug: false,
            events: true
        });
        
        $stateProvider
            // Home persian states
            .state("home", {
                url: "/",
                templateUrl: "partials/Home/HomeRoot.html",
                controller: 'DefaultCtrl',
                abstract: true,
                resolve: {
                    deps: [
                        '$ocLazyLoad', function ($ocLazyLoad) {
                            return $ocLazyLoad.load([
                                'partials/Home/DefaultCtrl.js',
                            ]);
                        }
                    ],
                    $title: function () { return 'getSiteName'; },
                    $isAsyncTitle: function () { return true; }
                }
            })
            .state("home.home", {
                url: "home",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Home/Main/Main.html",
                        controller: 'MainCtrl'
                    }
                },
                resolve: {
                    deps: [
                        '$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Home/Main/MainCtrl.js'
                        ]);
                }
                    ]
                }
            })
            .state("home.about", {
                url: "about",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Home/About/About.html",
                        controller: 'AboutCtrl'
                    }
                },
                resolve: {
                    deps: [
                        '$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load([
                                'partials/Home/About/AboutCtrl.js'
                            ]);
                }
                    ]
                }
            })
            .state("home.contact_us", {
                url: "contact_us",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Home/ContactUs/ContactUs.html",
                        controller: 'ContactUsCtrl'
                    }
                },
                resolve: {
                    deps: [
                        '$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load([
                                'partials/Home/ContactUs/ContactUsCtrl.js'
                            ]);
                }
                    ]
                }
            })
            .state("home.post", {
                url: "post/:id",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Home/Post/Post.html",
                        controller: 'PostSingleCtrl'
                    }
                },
                resolve: {
                    deps: [
                        '$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Home/Post/PostCtrl.js']);
                }
                    ]
                }
            })
            .state("home.cat", {
                url: "cat/:id",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Home/Category/Category.html",
                        controller: 'CategoryCtrl'
                    }
                },
                resolve: {
                    deps: [
                        '$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Home/Category/CategoryCtrl.js',
                            'app/directives/Post/post.js',
                                'partials/Home/Category/CategoryService.js'
                            ]);
                }
                    ]
                }
            })
            
            // User persian states
            .state("user_root", {
                url: "/user",
                templateUrl: "partials/User/UserRoot.html",
                controller: 'UserCtrl',
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/User/UserCtrl.js']);
                    }]
                }
            })
            .state("user_root.dashboard", {
                url: "/dashboard",
                views: {
                    "viewContent": {
                        templateUrl: "partials/User/UserDashboard.html",
                        controller: 'UserCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/User/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/User/UserCtrl.js']);
                    }]
                }
            })
            .state("user_root.profile", {
                url: "/profile",
                views: {
                    "viewContent": {
                        templateUrl: "partials/User/Profile/Profile.html",
                        controller: 'ProfileCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/User/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/User/Profile/ProfileCtrl.js']);
                    }]
                }
            })

            // Admin states
            .state("admin_root", {
                url: "/admin",
                templateUrl: "partials/Admin/AdminRoot.html",
                controller: 'AdminCtrl',
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Admin/AdminCtrl.js']);
                    }]
                }
            })
            .state("admin_root.dashboard", {
                url: "/dashboard",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Dashboard/Dashboard.html",
                        controller: 'AdminCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/AdminCtrl.js']);
                    }],
                    $title: function () { return 'داشبورد'; }
                }
            })
            .state("admin_root.new_post", {
                url: "/new_post/:id",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Post/NewPost.html",
                        controller: 'NewPostCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Post/NewPostCtrl.js',
                            'partials/Admin/Modals/Gallery/GalleryModalCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت پست'; }
                }
            })
            .state("admin_root.updater", {
                url: "/updater",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Updater/Updater.html",
                        controller: 'UpdaterCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Updater/UpdaterCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'آپدیت سایت'; }
                }
            })
            .state("admin_root.all_posts", {
                url: "/all_posts",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Post/AllPosts.html",
                        controller: 'AllPostsCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Post/AllPostsCtrl.js']);
                    }],
                    $title: function () { return 'تمام پست ها'; }
                }
            })
            .state("admin_root.new_page", {
                url: "/new_page/:id",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Page/NewPage.html",
                        controller: 'NewPageCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Page/NewPageCtrl.js',
                            'partials/Admin/Modals/Gallery/GalleryModalCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت صفحه'; }
                }
            })
            .state("admin_root.all_pages", {
                url: "/all_pages",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Page/AllPages.html",
                        controller: 'AllPagesCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Page/AllPagesCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'تمام صفحات'; }
                }
            })
            .state("admin_root.gallery", {
                url: "/gallery",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Gallery/Gallery.html",
                        controller: 'GalleryCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Gallery/GalleryCtrl.js',
                            //'js/ng-file-upload.min.js',
                            'js/angular-clipboard.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'گالری سایت'; }
                }
            })
            .state("admin_root.subjects", {
                url: "/subjects",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Subject/Subject.html",
                        controller: 'SubjectCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Subject/SubjectCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت موضوعات'; }
                }
            })
            .state("admin_root.comment", {
                url: "/comments",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Comment/Comment.html",
                        controller: 'CommentCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Comment/CommentCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت نظرات'; }
                }
            })
            .state("admin_root.contact_us", {
                url: "/contacts",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/ContactUs/ContactUs.html",
                        controller: 'ContactUs'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/ContactUs/ContactUsCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت پیام ها'; }
                }
            })
            .state("admin_root.users", {
                url: "/users",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Users/Users.html",
                        controller: 'UsersCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Users/UsersCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت کاربران'; }
                }
            })
            .state("admin_root.setting", {
                url: "/setting",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Setting/Setting.html",
                        controller: 'SettingCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Setting/SettingCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'تنظیمات سایت'; }
                }
            })
            .state("admin_root.slider", {
                url: "/slider",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Slider/Slider.html",
                        controller: 'SliderCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Slider/SliderCtrl.js',
                            'partials/Admin/Modals/Gallery/GalleryModalCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'مدیریت اسلایدر'; }
                }
            })
            .state("admin_root.profile", {
                url: "/profile",
                views: {
                    "viewContent": {
                        templateUrl: "partials/Admin/Profile/Profile.html",
                        controller: 'ProfileCtrl'
                    },
                    "viewSidebar": {
                        templateUrl: "partials/Admin/Sidebar.html"
                    }
                },
                resolve: {
                    deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load([
                            'partials/Admin/Profile/ProfileCtrl.js',
                            'app/directives/MultiSelectDropDown/checkbox-drop-down.js']);
                    }],
                    $title: function () { return 'پروفایل'; }
                }
            });

        $urlRouterProvider.otherwise(function ($injector, $location) {
            var $state = $injector.get('$state');
            //$state.go('home.home');
            $state.go('admin_root.dashboard');
        });
    }
]);

app.factory("Extention", ['$http', '$timeout', '$rootScope', '$state', '$stateParams', 'toaster', '$uibModal',
    function ($http, $timeout, $rootScope, $state, $stateParams, toaster, $uibModal) { // This service connects to our REST API

        var serviceBase = 'api/v1/';

        var obj = {};
        obj.workers = 0;
        obj.serviceBase = serviceBase;
        obj.debugMode = true;

        obj.noImageClass = 'fa fa-2x fa-user';

        obj.setBusy = function (en) {
            if (en) {
                if (obj.workers === 0)
                    $rootScope.spinner.active = true;
                //$rootScope.progressbar.start();
                obj.workers++;
            } else {
                obj.workers--;
                if (obj.workers === 0)
                    $timeout(obj.disableLoading, 500);
                //$rootScope.progressbar.complete();
            }
        };

        obj.toast = function (data) {
            toaster.pop(data.status, "", data.message, 10000, 'trustedHtml');
        }

        obj.pop = function (status, msg, delay) {
            if (!delay)
                delay = 7000;
            toaster.pop(status, "", msg, delay, 'trustedHtml');
        }

        obj.popError = function (msg, delay) {
            if (!delay)
                delay = 7000;
            toaster.pop('error', "", msg, delay, 'trustedHtml');
        }
        obj.popSuccess = function (msg, delay) {
            if (!delay)
                delay = 7000;
            toaster.pop('success', "", msg, delay, 'trustedHtml');
        }
        obj.popInfo = function (msg, delay) {
            if (!delay)
                delay = 7000;
            toaster.pop('info', "", msg, delay, 'trustedHtml');
        }

        obj.get = function (q) {
            obj.setBusy(true);
            return $http.get(serviceBase + q).then(function (results) {
                obj.setBusy(false);
                return results.data;
            }, function (err) {
                obj.setBusy(false);
                return err;
            });
        };

        obj.getExternal = function (q) {
            obj.setBusy(true);
            return $http.get(q).then(function (results) {
                obj.setBusy(false);
                return results.data;
            }, function (err) {
                obj.setBusy(false);
                return err;
            });
        };

        obj.post = function (q, object) {
            obj.setBusy(true);
            return $http.post(serviceBase + q, object).then(function (results) {
                obj.setBusy(false);
                return results.data;
            }, function (err) {
                obj.setBusy(false);
                return err;
            });
        };

        obj.postAsync = function (q, object) {
            return $http.post(serviceBase + q, object).then(function (results) {
                return results.data;
            }, function (err) {
                return err;
            });
        };

        obj.disableLoading = function () {
            $rootScope.spinner.active = false;
        }

        obj.authUser = function (user) {
            $rootScope.authenticated = true;
            $rootScope.user = {};
            $rootScope.user.UserID = user.UserID;
            $rootScope.user.lastName = user.LastName;
            $rootScope.user.firstName = user.FirstName;
        }

        obj.unAuthUser = function () {
            $rootScope.authenticated = false;
            $rootScope.isAdmin = false;
            $rootScope.user = {};
        }

        obj.isAdmin = function () {
            return $rootScope.isAdmin;
        }

        obj.getAuth = function () {
            return { authenticated: $rootScope.authenticated, isAdmin: $rootScope.isAdmin };
        }

        obj.openSignupPanel = function (lang) {
            var template;
            if (lang == 'en')
                template = 'partials/HomeEN/SignupTemplate.html';
            else
                template = 'partials/Home/SignupTemplate.html';

            $uibModal.open({
                animation: true,
                templateUrl: template,
                controller: 'authCtrl',
                size: 'md'
            });
        }

        obj.openSigninPanel = function (lang) {
            var template;
            if (lang == 'en')
                template = 'partials/HomeEN/LoginTemplate.html';
            else
                template = 'partials/Home/LoginTemplate.html';

            $uibModal.open({
                animation: true,
                templateUrl: template,
                controller: 'authCtrl',
                size: 'md'
            });
        }

        obj.scrollTo = function (y) {

            var startY = currentYPosition();
            var stopY = y;//elmYPosition(eID);
            var distance = stopY > startY ? stopY - startY : startY - stopY;
            if (distance < 100) {
                scrollTo(0, stopY); return;
            }
            var speed = Math.round(distance / 10);
            if (speed >= 5) speed = 5;
            var step = Math.round(distance / 25);
            var leapY = stopY > startY ? startY + step : startY - step;
            var timer = 0;
            if (stopY > startY) {
                for (var i = startY; i < stopY; i += step) {
                    setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                    leapY += step; if (leapY > stopY) leapY = stopY; timer++;
                } return;
            }
            for (var i = startY; i > stopY; i -= step) {
                setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                leapY -= step; if (leapY < stopY) leapY = stopY; timer++;
            }

            function currentYPosition() {
                // Firefox, Chrome, Opera, Safari
                if (self.pageYOffset) return self.pageYOffset;
                // Internet Explorer 6 - standards mode
                if (document.documentElement && document.documentElement.scrollTop)
                    return document.documentElement.scrollTop;
                // Internet Explorer 6, 7 and 8
                if (document.body.scrollTop) return document.body.scrollTop;
                return 0;
            }
        };
        obj.scrollToElement = function (element, offsetY) {
            if (!offsetY)
                offsetY = 0;
            var startY = currentYPosition();
            var stopY = elmYPosition(element) + offsetY;
            var distance = stopY > startY ? stopY - startY : startY - stopY;
            if (distance < 100) {
                scrollTo(0, stopY); return;
            }
            var speed = Math.round(distance / 10);
            if (speed >= 5) speed = 5;
            var step = Math.round(distance / 25);
            var leapY = stopY > startY ? startY + step : startY - step;
            var timer = 0;
            if (stopY > startY) {
                for (var i = startY; i < stopY; i += step) {
                    setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                    leapY += step; if (leapY > stopY) leapY = stopY; timer++;
                } return;
            }
            for (var i = startY; i > stopY; i -= step) {
                setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                leapY -= step; if (leapY < stopY) leapY = stopY; timer++;
            }

            function currentYPosition() {
                // Firefox, Chrome, Opera, Safari
                if (self.pageYOffset) return self.pageYOffset;
                // Internet Explorer 6 - standards mode
                if (document.documentElement && document.documentElement.scrollTop)
                    return document.documentElement.scrollTop;
                // Internet Explorer 6, 7 and 8
                if (document.body.scrollTop) return document.body.scrollTop;
                return 0;
            }

            function elmYPosition(eID) {
                var elm = document.getElementById(eID);
                var y = elm.offsetTop;
                var node = elm;
                while (node.offsetParent && node.offsetParent != document.body) {
                    node = node.offsetParent;
                    y += node.offsetTop;
                } return y;
            }

        };

        return obj;
    }]);

app.run(function ($rootScope, $templateCache, $state, $location, Extention) {

    //$templateCache.removeAll();

    $rootScope.spinner = { active: false };

    $rootScope.$on("$stateChangeSuccess", function() {
        Extention.setBusy(false);
        //$templateCache.remove('partials/Home/Main/Main.html');
       // $templateCache.remove('partials/Home/Post/Post.html');
    });

    $rootScope.$on("$stateChangeStart", function (event, next, current) {
        Extention.setBusy(true);
        //$rootScope.authenticated = false;
        //$rootScope.recaptchaKey = false;

        //if (typeof (next.views.viewContent) !== 'undefined') {

        //    $templateCache.remove(next.views.viewContent.templateUrl);
        //}

        Extention.post('getSiteTitleIcon').then(function (res) {
            $rootScope.titleIcon = res.SiteTitleIcon;
        });

        Extention.get('session').then(function (results) {
            

            //if (next.url == '/admin') {
            //    $state.go("admin_root.dashboard");
            //    return;
            //}

            //if (results && results.Status == 'success') {
            //    $rootScope.authenticated = true;
            //    $rootScope.user = {};
            //    $rootScope.user.lastName = results.LastName;
            //    $rootScope.user.firstName = results.FirstName;
                
            //    if (results.IsAdmin)
            //        $rootScope.isAdmin = true;
            //}

            //if (next.name.indexOf("en") > -1) {
            //    $rootScope.lang = 'en';
            //} else {
            //    $rootScope.lang = 'fa';
            //}
            
            //if (next.name.indexOf("admin_root") > -1) {
            //    if (results && !results.IsAdmin)
            //        $state.go("home.home");

            //}else if (next.name.indexOf("user_root") > -1) {
            //    if (results && !results.UserID)
            //        $state.go("home.home");
            //}
        });
    });
});

app.filter('jalaliDate', function () {
    return function (inputDate, format) {
        var date = moment(inputDate);
        return date.fromNow() + " " + date.format(format);
    }
});

app.filter('moment', function () {
    return function (inputDate, format) {
        return moment(inputDate).format(format);
    }
});

app.filter('subString', function () {
    return function (text, length) {
        if (text&&text.length > length) {
            return text.substr(0, length) + "...";
        }
        return text;
    }
});

app.filter('split', function() {
    return function (input, splitChar, feildName) {
        if (!input)
            return "";
        var str = "";
        for (var i = 0; i < input.length; i++) {
            if (i === input.length-1)
                str += (input[i][feildName]);
            else
                str += (input[i][feildName] + splitChar);
        }
        return str;
    }
});

app.directive('slideable', function() {
    return {
        restrict: 'C',
        compile: function(element, attr) {
            // wrap tag
            var contents = element.html();
            element.html('<div class="slideable_content" style="margin:0 !important; padding:0 !important" >' + contents + '</div>');

            return function postLink(scope, element, attrs) {
                // default properties

                attrs.duration = (!attrs.duration) ? '1s' : attrs.duration;
                attrs.easing = (!attrs.easing) ? 'ease-in-out' : attrs.easing;
                element.css({
                    'overflow': 'hidden',
                    'height': '0px',
                    'transitionProperty': 'height',
                    'transitionDuration': attrs.duration,
                    'transitionTimingFunction': attrs.easing
                });
            };
        }
    };
});

app.directive('slideToggle', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var target = document.querySelector(attrs.slideToggle);
            attrs.expanded = false;
            element.bind('click', function () {
                var content = target.querySelector('.slideable_content');
                if (!attrs.expanded) {
                    content.style.border = '1px solid rgba(0,0,0,0)';
                    var y = content.clientHeight;
                    content.style.border = 0;
                    target.style.height = y + 'px';
                } else {
                    target.style.height = '0px';
                }
                attrs.expanded = !attrs.expanded;
            });
        }
    }
});

app.directive('compile', [
    '$compile', function($compile) {
        return function(scope, element, attrs) {
            scope.$watch(
                function(scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function(value) {
                    // when the 'compile' expression changes
                    // assign it into the current DOM
                    element.html(value);

                    // compile the new DOM and link it to the current
                    // scope.
                    // NOTE: we only compile .childNodes so that
                    // we don't get into infinite loop compiling ourselves
                    $compile(element.contents())(scope);
                }
            );
        };
    }
]);

angular.module("ui.router.title", ["ui.router"])
	.run(["$rootScope", "$timeout", "$state", "Extention", function ($rootScope, $timeout, $state, Extention) {

	    $rootScope.$on("$stateChangeSuccess", function () {
	        var title = $state.$current.locals.globals.$title;
	        var isAsync = $state.$current.locals.globals.$isAsyncTitle;
	        if (isAsync) {
	            Extention.post(title).then(function (res) {
	                $timeout(function () {
	                    $rootScope.$title = res.SiteName;
	                });

	                $rootScope.$breadcrumbs = [];
	                var state = $state.$current;
	                while (state) {
	                    if (state.resolve && state.resolve.$title) {
	                        $rootScope.$breadcrumbs.unshift({
	                            title: getTitleValue(state.locals.globals.$title),
	                            state: state.self.name,
	                            stateParams: state.locals.globals.$stateParams
	                        });
	                    }
	                    state = state.parent;
	                }
	            });
	        } else {
	            var t = getTitleValue(title);
	            $timeout(function () {
	                $rootScope.$title = t;
	            });

	            $rootScope.$breadcrumbs = [];
	            var state = $state.$current;
	            while (state) {
	                if (state.resolve && state.resolve.$title) {
	                    $rootScope.$breadcrumbs.unshift({
	                        title: getTitleValue(state.locals.globals.$title),
	                        state: state.self.name,
	                        stateParams: state.locals.globals.$stateParams
	                    });
	                }
	                state = state.parent;
	            }
	        }
	    });

	    function getTitleValue(title) {
	        return angular.isFunction(title) ? title() : title;
	    }

	}]);