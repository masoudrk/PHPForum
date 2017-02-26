angular.module(appName).controller('ForumCtrl',
    function ($scope, $element, $rootScope, $stateParams, $state, $timeout, Extention) {

    if(!$stateParams.id || $stateParams.id ==''){
        $state.go('forum_home');
    }
    //
    // $scope.activity = {
    //     low : 'solid 2px #e74c3c',
    //     medium : 'solid 2px #f1c40f',
    //     high : 'solid 2px #2ecc71'
    // };

    $scope.pagingParams = { SubjectID : $stateParams.id };
    $scope.question = {};

	Extention.post('getSubForumData',{SubjectID: $stateParams.id})
		.then(function (res) {

        $scope.forumData = res;
        // Extention.addRoute(res.Subject.Title,$state.href($state.current.name,$stateParams));
        $rootScope.breadcrumbs = [];
        $rootScope.breadcrumbs.push({title : 'خانه' , url : 'home.php' ,icon : 'fa-home' });
        $rootScope.breadcrumbs.push({title : res.Subject.MainTitle ,
                url : $state.href('main_forum', {id:res.Subject.MainSubjectName}) });
        $rootScope.breadcrumbs.push({title : res.Subject.Title });

	});

    $scope.pagingControllerLastQuestions = {};
    $scope.pagingControllerBestQuestions = {};
    $scope.pagingControllerAnswered = {};
    $scope.pagingControllerFollowingQuestions = {};
    $scope.pcAdminPost = {};
    $scope.pcAdminPosts = {};
    // $scope.pagingControllerBestAnswered = {};

    $scope.getTab = function (id) {
        switch (id){
            case 0:
                $scope.pagingControllerLastQuestions.update();
                break;
            case 1:
                $scope.pagingControllerBestQuestions.update();
                break;
            case 2:
                $scope.pagingControllerAnswered.update();
                break;
            case 3:
                $scope.pagingControllerFollowingQuestions.update();
                break;
            case 4:
                $scope.pcAdminPost.update();
                break;
        }
        $scope.activeTab = id;
    }

    $scope.getDownTab = function (id) {
        switch (id) {
            case 0:
                $scope.pcAdminPosts.update();
                break;
            // case 1:
            //     $scope.pagingControllerBestAnswered.update();
            //     break;
        }
        $scope.activeDownTab = id;
    }

    $timeout(function () {
        $scope.getTab(0);
        $scope.getDownTab(0);
    });

    $scope.incrementChartOptions = [
    {
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

	activeElement('#SForum','#S' + $stateParams.id);
});