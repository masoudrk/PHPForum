angular.module(appName).controller('MainForumCtrl', 
    function ($scope, $element, $rootScope, $stateParams, $state, $timeout, Extention) {

    $scope.activity = {
        low : 'solid 2px #e74c3c',
        medium : 'solid 2px #f1c40f',
        high : 'solid 2px #2ecc71'
    };

    if(!$stateParams.id || $stateParams.id ==''){
        $state.go('forum_home');
    }

    $scope.pagingControllerLastQuestions = {};
    $scope.pagingControllerBestQuestions = {};
    $scope.pagingControllerAnswered = {};
    $scope.pagingControllerFollowingQuestions = {};


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
        }
        $scope.activeTab = id;
    }

    $timeout(function () {
        $scope.getTab(0);
    });


    $scope.pieChartOptions = {
        "type": "pie",

        "valueField": "Value",
        "fontSize": 12,
        "marginLeft": 0,
        "marginRight": 0,
        "marginBottom": -80,
        "marginTop": -80,
        "theme": "light",
        "autoMargins": false,
        "dataProvider": $scope.chartData,
        colors:["#00BBCC", "#e74c3c"],
        labelFunction:function (graphDataItem){
            var data = graphDataItem.dataContext.Name;

            return persianJs( data + '' ).englishNumber().toString() ;
        },
        balloonFunction : function (graphDataItem){
            var data = graphDataItem.dataContext.Value;

            return "<b style=\"font-size: 13px\">" +
                persianJs( data + '' ).englishNumber().toString() + "<br/>"+graphDataItem.dataContext.Name+"</b>";
        }
    };

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

    $scope.pagingParams = { MainSubjectName : $stateParams.id };
    $scope.question = {};

	Extention.post('getMainForumData',{MainSubjectName: $stateParams.id})
		.then(function (res) {
		    $scope.forumData = res;

            $rootScope.breadcrumbs = [];
            $rootScope.breadcrumbs.push({title : 'خانه' , url : '#/home' ,icon : 'fa-home' });
            $rootScope.breadcrumbs.push({title : res.MainSubject.Title });

		});

	$scope.followSubject = function (id) {
	    Extention.postAsync("followSubject", { SubjectID: id }).then(function (res) {
	        console.log(res);
	        if (res.Status == 'success') {
	            for (var i = 0; i < $scope.forumData.SubjectChilds.length ; i++) {
	                if ($scope.forumData.SubjectChilds[i].ID == id)
	                {
	                    $scope.forumData.SubjectChilds[i].PersonFollow = 1;
	                    $scope.forumData.SubjectChilds[i].FollowCount = Number($scope.forumData.SubjectChilds[i].FollowCount) + 1;
	                    return;
	                }
	            }
	        }
	    });
	}

	$scope.unFollowSubject = function (id) {
	    Extention.postAsync("unFollowSubject", { SubjectID: id }).then(function (res) {
	        if (res.Status == 'success') {
	            for (var i = 0; i < $scope.forumData.SubjectChilds.length ; i++) {
	                if ($scope.forumData.SubjectChilds[i].ID == id) {
	                    $scope.forumData.SubjectChilds[i].PersonFollow = 0;
	                    $scope.forumData.SubjectChilds[i].FollowCount = Number($scope.forumData.SubjectChilds[i].FollowCount) - 1;
	                    return;
	                }
	            }
	        }
	    });
	}

	$scope.followMainSubject = function (id) {
	    Extention.postAsync("followMainSubject", { MainSubjectID: id }).then(function (res) {
	        console.log(res);
	        if (res.Status == 'success') {
	            $scope.forumData.MainSubject.PersonFollow = 1;
	        }
	    });
	}

	$scope.unFollowMainSubject = function (id) {
	    Extention.postAsync("unFollowMainSubject", { MainSubjectID: id }).then(function (res) {
	        if (res.Status == 'success') {
	            $scope.forumData.MainSubject.PersonFollow = 0;
	        }
	    });
	}



	activeElement('#SForum','#S' + $stateParams.id);
});