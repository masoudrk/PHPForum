var appName = 'forumApp';
var serviceBaseURL = '../api/user/';

var app = angular.module(appName, ['ngRoute', 'treasure-overlay-spinner', 'ngCookies', 'ui.router', 'angular-confirm',
    'oc.lazyLoad', 'ngAnimate', 'toaster', 'ui.bootstrap', 'ui.router.title', 'ui.select', 'ngPersian',
    'ngFileUpload','anim-in-out','am-charts','ng-fx']);

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
            url: "/Dashboard",
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
        }).state("forum_home", {
            url: "/Home",
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
        .state("main_forum", {
            url: "/MainForum/:id",
            templateUrl: "partials/Forum/Main/MainForum.html",
            controller: 'MainForumCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/Main/MainForumCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن';
                }
            }
        })
        .state("forum", {
            url: "/Forum/:id",
            templateUrl: "partials/Forum/Forum/Forum.html",
            controller: 'ForumCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/Forum/Forum/ForumCtrl.js']);
                }],
                $title: function () {
                    return 'انجمن';
                }
            }
        })
        .state("new_question", {
            url: "/NewQuestion/:id",
            templateUrl: "partials/MyActions/NewQuestion/NewQuestion.html",
            controller: 'NewQuestionCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/MyActions/NewQuestion/NewQuestionCtrl.js']);
                }],
                $title: function () {
                    return 'سوال جدید';
                }
            }
        })
        .state("questions", {
            url: "/MyQuestions",
            templateUrl: "partials/MyActions/Questions/Questions.html",
            controller: 'QuestionsCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/MyActions/Questions/QuestionsCtrl.js']);
                }],
                $title: function () {
                    return 'سوالات شما';
                }
            }
        })
        .state("my_answers", {
            url: "/MyAnswers",
            templateUrl: "partials/MyActions/Answers/Answers.html",
            controller: 'AnswersCtrl',
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load(['partials/MyActions/Answers/AnswersCtrl.js']);
                }],
                $title: function () {
                    return 'جواب های شما';
                }
            }
        })
        .state("question", {
            url: "/Question/:id",
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
        }).state("UserProfile", {
            url: "/UserProfile/:id",
            templateUrl: "partials/User/UserProfile/UserProfile.html",
            controller: 'UserProfileCtrl',
            resolve: {
                deps: [
                    '$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/User/UserProfile/UserProfileCtrl.js']);
                    }
                ],
                $title: function () {
                    return 'پروفایل من';
                }
            }
        })
        .state("messages", {
            url: "/Messages/:id",
            templateUrl: "partials/Message/Message.html",
            controller: 'MessageCtrl',
            resolve: {
                deps: [
                    '$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Message/MessageCtrl.js']);
                    }
                ],
                $title: function () {
                    return 'مدیریت پیام ها';
                }
            }
        })
        .state("profile", {
            url: "/Profile/:action",
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
        .state("rating", {
            url: "/Rating/:id",
            templateUrl: "partials/Evaluation//Rating/Rating.html",
            controller: 'RatingCtrl',
            resolve: {
                deps: [
                    '$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Evaluation/Rating/RatingCtrl.js']);
                    }
                ],
                $title: function () {
                    return 'نظرسنجی';
                }
            }
        })
        .state("quiz", {
            url: "/Quiz/:id",
            templateUrl: "partials/Evaluation/Quiz/Quiz.html",
            controller: 'QuizCtrl',
            resolve: {
                deps: [
                    '$ocLazyLoad', function ($ocLazyLoad) {
                        return $ocLazyLoad.load(['partials/Evaluation/Quiz/QuizCtrl.js']);
                    }
                ],
                $title: function () {
                    return 'آزمون';
                }
            }
        });
    $urlRouterProvider.otherwise(function ($injector, $location) {
        var $state = $injector.get('$state');
        $state.go('home');
    }); 
}
]);


var activeElement = function (parent , name) {
    if(name){
        var elem = $(name);
        elem.addClass('active').siblings().removeClass('active');
    }
    var elemP = $(parent);
    elemP.addClass('active').siblings().removeClass('active');
}


app.run(function ($rootScope, $templateCache, $state, $location, $cookies, $cookieStore, Extention, OnlineSocket) {
    
    $rootScope.breadcrumbs = [];
    $rootScope.breadcrumbs.push({title : 'خانه' , url : '#/home'});

    $rootScope.spinner ={};

    $rootScope.$on("$stateChangeSuccess", function (event, toState, toParams, fromState, fromParams) {
        Extention.setBusy(false);

    });

    $rootScope.$on('$stateChangeError',
        function(event, toState, toParams, fromState, fromParams, error){
        Extention.setBusy(false);
    });

    $rootScope.$on('$stateNotFound',
        function(event, unfoundState, fromState, fromParams){
        Extention.setBusy(false);
    });

    $rootScope.$on("$stateChangeStart", function (event, next, current) {

        Extention.setBusy(true);

        if($rootScope.globalSearchActive)
            $rootScope.globalSearchActive = false;

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

        obj.addRoute = function (title , url) {

            $rootScope.breadcrumbs.push({title :title  , url:url});
            if($rootScope.breadcrumbs.length > 2)
                $rootScope.breadcrumbs.splice(0,1);
        }

        obj.subString = function (text, length) {
            if (text && text.length > length) {
                return text.substr(0, length) + "...";
            }
            return text;
        }
        
        obj.setBusy = function (en) {
            if (en) {
                if (obj.workers === 0)
                    $rootScope.spinner.active = true;
                //$rootScope.progressbar.start();
                obj.workers++;
            } else {
                obj.workers--;
                if (obj.workers === 0)
                    $timeout(obj.disableLoading, 250);
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
        obj.popWarning = function (msg, delay) {
            if (!delay)
                delay = 7000;
            toaster.pop('warning', "", msg, delay, 'trustedHtml');
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

                if(obj.debugMode ){
                    console.log(results.data);

                    if(results.status != 200)
                        obj.popModal(results);
                }
                obj.setBusy(false);

                if(results.data.AuthState && results.data.AuthState == 'UN_AUTH'){
                    console.log('State : UN_AUTHORIZED_USER');
                    window.location = '../';
                }

                return results.data;
            }, function (err) {

                if(obj.debugMode){
                    console.log(err.data);
                    obj.popModal(err.data);
                }

                if(err.data.AuthState && err.data.AuthState == 'UN_AUTH'){
                    console.log('State : UN_AUTHORIZED_USER');
                    window.location = '../';
                }

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

app.factory("OnlineSocket", ['$http', '$timeout', '$rootScope', 'Extention',
    function ($http, $timeout,$rootScope, Extention) { // This service connects to our REST API

        var obj = {};
        obj.getData = function () {
            $timeout(function () {
                obj.fetch();
            },50000);
        };

        obj.fetch = function () {
            Extention.postAsync('getSocketData').then(function (res) {
                $rootScope.socketData = res;
                $timeout(function () {
                    $rootScope.$broadcast('socketDataChanged');
                });
                obj.getData();
            });
        }

        obj.fetch();

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
    return function (inputDate) {
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

app.filter('concat', function () {
    return function (input, con) {
        return input + con;
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

app.filter('capitalize', function() {
    return function(input, all) {
        var reg = (all) ? /([^\W_]+[^\s-]*) */g : /([^\W_]+[^\s-]*)/;
        return (!!input) ? input.replace(reg, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) : '';
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

app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});