angular.module(appName).controller('MainForumCtrl', 
    function ($scope, $element, $rootScope, $stateParams, $state, $timeout, $timeout, Extention) {

    $scope.activeTab = 0;
    $scope.activity = {
        low : 'solid 2px #e74c3c',
        medium : 'solid 2px #f1c40f',
        high : 'solid 2px #2ecc71'
    };

    if(!$stateParams.id || $stateParams.id ==''){
        $state.go('forum_home');
    }

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