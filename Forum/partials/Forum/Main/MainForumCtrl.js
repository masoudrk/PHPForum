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

	});

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
                if(i > 90 && i <= 100)
                    t = 190-i;
                else if(i > 80 && i <= 90)
                    t = i;
                else if(i > 70 && i <= 80)
                    t= 15;
                else if(i > 60 && i <= 70)
                    t= 80-i;
                else if(i > 50 && i <= 60)
                    t= i;
                else if(i > 30 && i <= 50)
                    t= 12;
                else if(i > 10 && i <= 30)
                    t= 8;
                else
                    t= 2;
                sin2.push({x: i, y: t});
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
});