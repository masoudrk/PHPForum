angular.module(appName).controller('ForumCtrl',
    function ($scope, $element, $rootScope, $stateParams, $state, $timeout, $timeout, Extention) {

    if(!$stateParams.id || $stateParams.id ==''){
        $state.go('forum_home');
    }
        
    $scope.activity = {
        low : 'solid 2px #e74c3c',
        medium : 'solid 2px #f1c40f',
        high : 'solid 2px #2ecc71'
    };

    $scope.pagingParams = { SubjectID : $stateParams.id };
    $scope.question = {};


    $scope.data = [
        {
            key: "جواب ها",
            values: [ ] ,
            mean: 250
        },
        {
            key: "سوال ها",
            values: [ ],
            mean: -60
        }
    ];

	Extention.post('getSubForumData',{SubjectID: $stateParams.id})
		.then(function (res) {
        $scope.forumData = res;
        $scope.data[0].values = res.ChartAData;
        $scope.data[1].values = res.ChartQData;
	});
    


        $scope.options = {
            "chart": {
                "type": "lineChart",
                "height": 350,
                "margin": {
                    "top": 40,
                    "right": 20,
                    "bottom": 60,
                    "left": 75
                },
                x: function (d) {
                    return d[0];
                },
                y: function (d) {
                    return d[1];
                },
                "useInteractiveGuideline": true,
                "dispatch": {},
                "clipEdge": true,
                "showLegend":false,
                "xAxis": {
                    "axisLabel": "روز",
                    tickFormat: function (d) {
                        if ($scope.data[0].values[d]) {
                            var label = $scope.data[1].values[d][2];
                            return label;
                        }
                        return '';
                    }
                },
                "yAxis": {
                    "axisLabel": "تعداد",
                    tickFormat: function (d) {
                        return d3.format('.02f')(d);
                    },
                }
            }
        }

	activeElement('#SForum','#S' + $stateParams.id);
});