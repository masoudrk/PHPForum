
angular.module(appName).controller('UserProfileCtrl', function ($scope, $rootScope, $routeParams, $state, $location, $stateParams, Extention) {

    $scope.isOnline = false;
    $scope.profile = {};
    $scope.userID = $stateParams.id;

    Extention.post("getProfile", { TargetUserID: $scope.userID }).then(function (res) {
        if (res.Status == 'success') {
            $scope.profile = res.Data;
            $rootScope.breadcrumbs = [];
            $rootScope.breadcrumbs.push({ title: 'خانه', url: 'home.php', icon: 'fa-home' });
            $rootScope.breadcrumbs.push({ title: ' پروفایل ' + '\'' + res.Data.FullName + '\'' });
        } else {
            $scope.profile = null;
        }
    });

    $rootScope.$on("socketDataChanged", function(){
        $scope.checkNowOnline();
    });

    $scope.followPerson = function () {
        Extention.postAsync("followPerson", { TargetUserID: $scope.userID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.profile.PersonFollow = 1;
            }
        });
    }

    $scope.unFollowPerson = function () {
        Extention.postAsync("unFollowPerson", { TargetUserID: $scope.userID, UserID: $rootScope.user.UserID }).then(function (res) {
            if (res.Status == 'success') {
                $scope.profile.PersonFollow = 0;
            }
        });
    }

    $scope.checkNowOnline = function () {
        if($scope.socketData != undefined){
            var ous =  $scope.socketData.OnlineUsers;
            for (var i = 0 ; i < ous.length ; i++){
                if($scope.userID == ous[i].ID ){
                    $scope.isOnline = true;
                    return;
                }
            }
            $scope.isOnline = false;
        }
    }
    $scope.checkNowOnline();


    $scope.incrementChartOptions =[{
        id:"g1",
        type : "smoothedLine",
        lineColor: "#00BBCC",
        valueField: "IQuestionCount",
        fillColors: "#00BBCC",
        fillAlphas:0.2,
        bullet: "round",
        bulletColor: "#FFFFFF",
        bulletBorderAlpha : 1,
        bulletBorderThickness : 2,
        bulletSize : 7,
        useLineColorForBulletBorder : true,
        lineThickness : 2,
        balloon:{
            drop:false
        },
        balloonFunction : function (graphDataItem, graph){
            var value = graphDataItem.values.value;
            var date = moment(graphDataItem.category);
            var d =  date.format('jYYYY/jMM/jDD');

            return "<b style=\"font-size: 13px\">" +
                persianJs( value + " سوال <br>" +'<span class="text-muted">'+
                    d + '</span>').englishNumber().toString() + "</b>";
        }
    },
        {
            id:"g2",
            type : "smoothedLine",
            lineColor: "#e74c3c",
            valueField: "IAnswerCount",
            fillColors: "#e74c3c",
            fillAlphas:0.2,
            bullet: "round",
            bulletColor: "#FFFFFF",
            bulletBorderAlpha : 1,
            bulletBorderThickness : 2,
            bulletSize : 7,
            useLineColorForBulletBorder : true,
            lineThickness : 2,
            balloon:{
                drop:false
            },
            balloonFunction : function (graphDataItem, graph){
                var value = graphDataItem.values.value;
                var date = moment(graphDataItem.category);
                var d =  date.format('jYYYY/jMM/jDD');

                return "<b style=\"font-size: 13px\">" +
                    persianJs( value + " جواب <br>" +'<span class="text-muted">'+
                        d + '</span>').englishNumber().toString() + "</b>";
            }
        }
    ];

    $scope.dailyChartOptions =[{
        id:"g1",
        type : "smoothedLine",
        lineColor: "#00BBCC",
        valueField: "QuestionCount",
        fillColors: "#00BBCC",
        bullet: "round",
        bulletColor: "#FFFFFF",
        bulletBorderAlpha : 1,
        bulletBorderThickness : 2,
        bulletSize : 7,
        useLineColorForBulletBorder : true,
        lineThickness : 2,
        balloon:{
            drop:false
        },
        balloonFunction : function (graphDataItem, graph){
            var value = graphDataItem.values.value;
            var date = moment(graphDataItem.category);
            var d =  date.format('jYYYY/jMM/jDD');

            return "<b style=\"font-size: 13px\">" +
                persianJs( value + " سوال <br>" +'<span class="text-muted">'+
                    d + '</span>').englishNumber().toString() + "</b>";
        }
    },
        {
            id:"g2",
            type : "smoothedLine",
            lineColor: "#e74c3c",
            valueField: "AnswerCount",
            fillColors: "#e74c3c",
            bullet: "round",
            bulletColor: "#FFFFFF",
            bulletBorderAlpha : 1,
            bulletBorderThickness : 2,
            bulletSize : 7,
            useLineColorForBulletBorder : true,
            lineThickness : 2,
            balloon:{
                drop:false
            },
            balloonFunction : function (graphDataItem, graph){
                var value = graphDataItem.values.value;
                var date = moment(graphDataItem.category);
                var d =  date.format('jYYYY/jMM/jDD');

                return "<b style=\"font-size: 13px\">" +
                    persianJs( value + " جواب <br>" +'<span class="text-muted">'+
                        d + '</span>').englishNumber().toString() + "</b>";
            }
        }
    ];

    activeElement('#SProfile');
});