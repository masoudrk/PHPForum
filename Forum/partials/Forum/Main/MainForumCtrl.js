angular.module(appName).controller('MainForumCtrl', 
    function ($scope, $element, $rootScope, $stateParams, $state, $timeout, $timeout, Extention) {

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

	Extention.post('getMainForumData',{MainSubjectName: $stateParams.id})
		.then(function (res) {
        $scope.forumData = res;
	});
    
    $scope.loadFinishPageData = function () {
        $timeout(function(){fixPersianNumbers()});
    }
    

    $scope.options = {
        "chart": {
            "type": "lineChart",
            "height": 200,
            "margin": {
                "top": 20,
                "right": 20,
                "bottom": 40,
                "left": 55
            },
            "showLegend": false,
            "useInteractiveGuideline": true,
            "dispatch": {},
            "xAxis": {
                "axisLabel": "روز"
            },
            "yAxis": {
                "axisLabel": "سوالات",
                "axisLabelDistance": -10,
                tickFormat: function(d){
                    return d3.format('.02f')(d);
                }
            },
            "caption": {
                enable: false
            },
            "legend": {
                enable: false
            }
        }
    };

    $scope.data = sinAndCos();

    /*Random Data Generator */
    function sinAndCos() {
        var sin = [],sin2 = [],
            cos = [];

        //Data is represented as an array of {x,y} pairs.
        for (var i = 0; i < 100; i++) {
            var  t = 0;
            if(i>70)
                t=i/2;
            else if(i  > 50)
                t= 5;
            else
                t= i;
            sin2.push({x: i, y: (t < 0)?-t:t});
        }

        //Line chart data should be sent as an array of series objects.
        return [
            {
                values: sin2,
                key: 'Another sine wave',
                color: '#7777ff',
                area: true      //area - set to true if you want this line to turn into a filled area chart.
            }
        ];
    };

	activeElement('#SForum','#S' + $stateParams.id);
	fixFooter();
});