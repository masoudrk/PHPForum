angular.module(appName).controller('DashboardCtrl', function ($scope, ADMdtpConvertor, $rootScope, Extention, $state, $timeout) {

    $scope.newQuestionsDigram = {};

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

    var convertDateToISO = function (inputFullDate) {
        if (inputFullDate.calType == "jalali") {
            var t = ADMdtpConvertor.toGregorian(inputFullDate.year, inputFullDate.month, inputFullDate.day);

            return t.year + '-' + format(t.month) + '-' + format(t.day) + ' ' +
                format(inputFullDate.hour) + ':' + format(inputFullDate.minute);
        } else {
            return inputFullDate.year + '-' + format(inputFullDate.month) + '-' + format(inputFullDate.day) + ' ' +
                format(inputFullDate.hour) + ':' + format(inputFullDate.minute);
        }
    }
    var format = function (input) {
        return ((input < 10) ? '0' + input : input);
    }

    $scope.updateNewQuestionDiagram = function () {
        $timeout(function () {
            var data = {};

            if(angular.isDefined($scope.newQuestionsDigram.MainSubject))
                data.MainSubjectID = $scope.newQuestionsDigram.MainSubject.ID;

            if(angular.isDefined($scope.newQuestionsDigram.to) && $scope.newQuestionsDigram.to != "")
                data.toDate = convertDateToISO($scope.newQuestionsDigram.toFull);

            if(angular.isDefined($scope.newQuestionsDigram.from) && $scope.newQuestionsDigram.from != "")
                data.fromDate = convertDateToISO($scope.newQuestionsDigram.fromFull);

            Extention.post('getNewQuestionsGraphData',data).then(function (res) {
                $scope.dashboardData.ChartData = res;
            });

        });
    }


    Extention.post('getDashboardData').then(function (res) {
        res.MainSubjects.push({ID : -1 , Title : 'همه انجمن ها'});
        $scope.dashboardData = res;
    });

    activeElement('#SDashboard');
});