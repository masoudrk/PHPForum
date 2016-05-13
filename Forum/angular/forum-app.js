var appName = 'forumApp';
var serviceBaseURL = '../api/user/';

var app = angular.module(appName, ['ngRoute', 'treasure-overlay-spinner', 'ngCookies', 'ui.router', 'angular-confirm',
    'oc.lazyLoad', 'ngAnimate', 'toaster', 'ui.bootstrap', 'ui.router.title', 'ui.select', 'nvd3']);

app.config([
    '$stateProvider', '$urlRouterProvider', '$ocLazyLoadProvider',
function ($stateProvider, $urlRouterProvider, $ocLazyLoadProvider) {
    // Add nested user links to the "foo" menu.
    $ocLazyLoadProvider.config({
        debug: true,
        events: true
    });

    $stateProvider 
        .state('home', {
            url: "/",
            templateUrl: "partials/Forum/_Home/Home.html",
            controller: 'HomeCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/_Home/HomeCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن';
                }
            }
        })
        .state("dashboard", {
            url: "/dashboard",
            templateUrl: "partials/Dashboard/Dashboard.html",
            controller: 'DashboardCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Dashboard/DashboardCtrl.js']);
                }],
                $title: function () {
                    return 'داشبورد';
                }
            }
        }).state("profile", {
            url: "/profile",
            templateUrl: "partials/Profile/Profile.html",
            controller: 'ProfileCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Profile/ProfileCtrl.js']);
                }],
                $title: function () {
                    return 'پروفایل';
                }
            }
        })
        .state("forum_home", {
            url: "/home",
            templateUrl: "partials/Forum/_Home/Home.html",
            controller: 'HomeCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/_Home/HomeCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن خانه';
                }
            }
        })
        .state("forum_data_switch", {
            url: "/data_switch",
            templateUrl: "partials/Forum/DataSwitch/DataSwitch.html",
            controller: 'DataSwitchCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/DataSwitch/DataSwitchCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن دیتا سوئیچ';
                }
            }
        })
        .state("forum_transition", {
            url: "/transition",
            templateUrl: "partials/Forum/Transition/Transition.html",
            controller: 'TransitionCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/Transition/TransitionCtrl.js']);
                }],
                $title: function () {
                    return 'پروفایل';
                }
            }
        })
        .state("forum_transport_management", {
            url: "/transport_management",
            templateUrl: "partials/Forum/TransportManagement/TransportManagement.html",
            controller: 'TransportManagementCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/TransportManagement/TransportManagementCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن نظارت بر سیستم انتقال';
                }
            }
        })
        .state("forum_radio", {
            url: "/radio",
            templateUrl: "partials/Forum/Radio/Radio.html",
            controller: 'RadioCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/Radio/RadioCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن رادیو';
                }
            }
        })
        .state("new_question", {
            url: "/new_question/:id",
            templateUrl: "partials/Question/NewQuestion/NewQuestion.html",
            controller: 'NewQuestionCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Question/NewQuestion/NewQuestionCtrl.js']);
                }],
                $title: function () {
                    return 'سوال جدید';
                }
            }
        })
        .state("questions", {
            url: "/questions",
            templateUrl: "partials/Question/Questions/Questions.html",
            controller: 'QuestionsCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Question/Questions/QuestionsCtrl.js']);
                }],
                $title: function () {
                    return 'سوالات شما';
                }
            }
        }).state("question", {
        url: "/question/:id",
            templateUrl: "partials/Forum/Question/Question.html",
            controller: 'QuestionCtrl',
            resolve: {
                deps: [
                    '$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Forum/Question/QuestionCtrl.js']);
                    }
                ],
                $title: function () {
                    return 'سوال';
                }
            }
        });
    $urlRouterProvider.otherwise(function ($injector, $location) {
        var $state = $injector.get('$state');
        $state.go('home');
    });
}
]);

var persian = { 0: '۰', 1: '۱', 2: '۲', 3: '۳', 4: '۴', 5: '۵', 6: '۶', 7: '۷', 8: '۸', 9: '۹' };
var traverse = function (el) {
    if (el.nodeType == 3) {
        var list = el.data.match(/[0-9]/g);
        if (list != null && list.length != 0) {
            for (var i = 0; i < list.length; i++)
                el.data = el.data.replace(list[i], persian[list[i]]);
        }
    }
    for (var i = 0; i < el.childNodes.length; i++) {
        traverse(el.childNodes[i]);
    }
}

var fixPersianNumbers = function () {
    traverse(document.body);
}

var fixFooter = function () {
    var o = $.AdminLTE.options.controlSidebarOptions;
    var sidebar = $(o.selector);
    $.AdminLTE.controlSidebar._fixForContent(sidebar);
    traverse(document.body);
}

var activeElement = function (parent , name) {
    if(name){
        var elem = $(name);
        elem.addClass('active').siblings().removeClass('active');
    }
    var elemP = $(parent);
    elemP.addClass('active').siblings().removeClass('active');
}

app.run(function ($rootScope, $templateCache, $state, $location, $cookies, $cookieStore,Extention) {

    $rootScope.spinner ={};

    $rootScope.$on("$stateChangeSuccess", function () {
        Extention.setBusy(false);
    });
    $rootScope.$on('$stateChangeError',
        function(event, toState, toParams, fromState, fromParams, error){
        Extention.setBusy(false);
    });
    $rootScope.$on('$stateNotFound',
        function(event, unfoundState, fromState, fromParams){
        Extention.setBusy(false);
    })

    $rootScope.$on("$stateChangeStart", function (event, next, current) {
        Extention.setBusy(true);
    });


});

app.factory("Extention", ['$http', '$timeout', '$rootScope', '$state', '$stateParams', 'toaster', '$uibModal',
    function ($http, $timeout, $rootScope, $state, $stateParams, toaster, $uibModal) { // This service connects to our REST API

        $rootScope.logout = function () {
            obj.post('logout').then(function (res) {
                if(res&&res.Status=='success'){
                    window.location = "../";
                }else{
                    obj.popError('مشکل ، لطفا دوباره امتحان کنید.');
                }
            });
        }

        $rootScope.user = session;

        var serviceBase = serviceBaseURL;
        //$rootScope.session = session;
        $rootScope.spinner = {};
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

        obj.popModal = function (data) {
            $uibModal.open({
                animation: true,
                templateUrl: 'notifyModal.html',
                controller: function ($scope , $uibModalInstance , data) {
                    $scope.data =  data;
                },
                size: 'md',
                resolve: {
                    data: function () {
                        return data;
                    }
                }
            });
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

                if(obj.debugMode && results.status != 200){
                    obj.popModal(results);
                }
                obj.setBusy(false);
                return results.data;
            }, function (err) {
                if(obj.debugMode)
                    obj.popModal(err.data);
                obj.setBusy(false);
                return err;
            });
        };

        obj.postAsync = function (q, object) {
            return $http.post(serviceBase + q, object).then(function (results) {

                if(obj.debugMode && results.status != 200){
                    obj.popModal(results);
                }
                return results.data;
            }, function (err) {
                if(obj.debugMode)
                    obj.popModal(err.data);
                return err;
            });
        };

        obj.disableLoading = function () {
            $rootScope.spinner.active = false;
        }

        obj.authUser = function (sess) {
            if(!sess.Valid){
                obj.unAuthUser();
                return;
            }

            $rootScope.user = sess.Session;
        }

        obj.unAuthUser = function () {
            $rootScope.user = undefined;
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

app.filter('propsFilter', function() {
    return function(items, props) {
        var out = [];

        if (angular.isArray(items)) {
            var keys = Object.keys(props);

            items.forEach(function(item) {
                var itemMatches = false;

                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }

                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            // Let the output be the input untouched
            out = items;
        }

        return out;
    };
});

app.filter('jalaliDate', function () {
    return function (inputDate, format) {
        var date = moment(inputDate);
        return date.fromNow() + " " + date.format(format);
    }
});

app.filter('fromNow', function () {
    return function (inputDate, format) {
        var date = moment(inputDate);
        return date.fromNow() ;
    }
});

app.filter('jalaliDateSimple', function () {
    return function (inputDate, format) {
        var date = moment(inputDate);
        return date.format(format);
    }
});

app.filter('moment', function () {
    return function (inputDate, format) {
        return moment(inputDate).format(format);
    }
});

app.filter('subString', function () {
    return function (text, length) {
        if (text && text.length > length) {
            return text.substr(0, length) + "...";
        }
        return text;
    }
});

app.filter('split', function () {
    return function (input, splitChar, feildName) {
        if (!input)
            return "";
        var str = "";
        for (var i = 0; i < input.length; i++) {
            if (i === input.length - 1)
                str += (input[i][feildName]);
            else
                str += (input[i][feildName] + splitChar);
        }
        return str;
    }
});

app.directive('slideable', function () {
    return {
        restrict: 'C',
        compile: function (element, attr) {
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
    '$compile', function ($compile) {
        return function (scope, element, attrs) {
            scope.$watch(
                function (scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function (value) {
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