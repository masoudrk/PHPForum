angular.module(appName).controller('DashboardCtrl', function ($scope, $element, $rootScope, Extention, $state, $timeout) {


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

    $scope.radarChartGraphs = [{

        valueField: "QTotal",

        bullet: "round",
        balloonFunction : function (graphDataItem, graph){
            var value = graphDataItem.values.value;

            return "<span style=\"font-size: 13px\">" +
                persianJs( " سوال" + value ).englishNumber().toString() + "</span>";
        }
    },{

        valueField: "ATotal",

        bullet: "round",
        balloonFunction : function (graphDataItem, graph){
            var value = graphDataItem.values.value;

            return "<span style=\"font-size: 13px\">" +
                persianJs( " جواب" + value ).englishNumber().toString() + "</span>";
        }
    }];


    Extention.post('getDashboardData').then(function (res) {
        $scope.dashboardData = res;
    });

    activeElement('#SDashboard');
});