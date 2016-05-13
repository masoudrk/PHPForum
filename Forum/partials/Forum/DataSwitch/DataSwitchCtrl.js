angular.module(appName).controller('DataSwitchCtrl', function ($scope, $element, $rootScope, $routeParams, $state, $timeout, $timeout, Extention) {

    $scope.pagingParams = { ForumName : 'DataSwitch' };
    $scope.question = {};
    
    
    
	Extention.post('getForumMainData',{ForumName:'DataSwitch'})
		.then(function (res) {

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
                "axisLabel": "Time (ms)"
            },
            "yAxis": {
                "axisLabel": "Voltage (v)",
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
            sin2.push({x: i, y: Math.sin(i/10)});
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

	activeElement('#SForum','#SDataSwitch');
	fixFooter();
});