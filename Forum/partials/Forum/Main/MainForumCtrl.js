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

    $scope.pagingParams = { MainSubjectName : $stateParams.id };
    $scope.question = {};
    $scope.data = [
        {
            "key" : "سوال ها" ,
            "values" : []
        },

        {
            "key" : "جواب ها" ,
            "values" : []
        }
    ]

	Extention.post('getMainForumData',{MainSubjectName: $stateParams.id})
		.then(function (res) {
        $scope.forumData = res;
        //$scope.data[0].values=[[ 1025409600000 , 23.041422681023] , [ 1028088000000 , 19.854291255832] , [
            // 1030766400000 , 21.02286281168] ];
            //res.ChartAData;
        //$scope.data[1].values =[ [ 1025409600000 , 7.9356392949025] , [ 1028088000000 , 7.4514668527298] , [
            // 1030766400000 , 7.9085410566608]];
            // res.ChartQData;

        $scope.data = [
            {
                "key" : "Africa" ,
                "values" : [ [ 1463347751 , 1] , [ 1463347128 , 8] , [ 1463433820 , 20] ]
            }
        ];
	});

    $scope.options = {
        chart: {
            type: 'stackedAreaChart',
            height: 300,
            margin : {
                top: 20,
                right: 20,
                bottom: 30,
                left: 40
            },
            x: function(d){
                return d[0];
            },
            y: function(d){
                return d[1];
            },
            useVoronoi: false,
            clipEdge: false,
            showLegend:false,
            duration: 100,
            useInteractiveGuideline: true,
            xAxis: {
                showMaxMin: false,
                tickFormat: function(d) {
                    return d3.time.format('%x')(new Date(d))
                }
            },
            yAxis: {
                tickFormat: function(d){
                    return d3.format(',.2f')(d);
                }
            },
            zoom: {
                enabled: true,
                scaleExtent: [1, 10],
                useFixedDomain: false,
                useNiceScale: false,
                horizontalOff: false,
                verticalOff: true,
                unzoomEventType: 'dblclick.zoom'
            }
        }
    };

	activeElement('#SForum','#S' + $stateParams.id);
});