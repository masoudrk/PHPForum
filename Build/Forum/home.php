<?php
?>
<!DOCTYPE html>
<html ng-app="forumApp" ng-controller="MainCtrl as vm" style="background-color: #ECF0F5;" >
<head>
    <!--    <link rel="stylesheet" href="../css/angular-notification-icons.min.css">-->
    <link rel="stylesheet" href="../css/font-awesome-animation.min.css">

    <link rel="stylesheet" href="../css/hover-min.css">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../cms/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- Ionicons -->
    <!--<link rel="stylesheet" href="../https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
    <!-- Theme style -->
    <link rel="stylesheet" href="../css/site-styles.css">

    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="fa" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>انجمن</title>

    <link rel="icon" href="../images/title.png" />
    <link rel="icon" href="../{{titleIcon}}" />
<style>

    .no-padding {
        padding: 0 !important;
    }
</style>
</head>
<body class="hold-transition skin-blue sidebar-mini vazir-font">
<div class="wrapper">

    <div class="content-wrapper" id="cont" style="min-height: 600px">

        <div class="">
            <div class="">
                <div class="col-md-4 no-padding">
                    <a  >
                        <img class="img-responsive" src="../images/final-supervizer.jpg" id="image">

                    </a>
                </div>
                <div class="col-md-6 no-padding">
                    <a class="col-xs-12 col-sm-6 no-padding"
                       href="../Forum/#/MainForum/Transition">
                        <div class="hovereffect">
                            <img class="img-responsive" src="../images/final-fiber.jpg" id="image">
                            <div ng-click="changeView('Transition')" class="overlay link" id="hoverImage">
                                <table style="height:100%;width:100%"><tr><td style="vertical-align:middle"><h2>گروه خطوط انتقال</h2></td></tr></table>

                                <!--<a class="info" href="../#/">دنبال کردن</a>-->
                            </div>
                        </div>
                    </a><!-- ./col -->
                    <a class="col-sm-6 col-xs-12 no-padding"
                       href="../Forum/#/MainForum/TransportManagement">
                        <div class="hovereffect">
                            <img class="img-responsive" src="../images/final-carier.jpg" id="image">
                            <div ng-click="changeView('TransportManagement')" class="overlay link">
                                <table style="height:100%;width:100%"><tr><td style="vertical-align:middle"><h2>گروه سیستم های انتقال</h2></td></tr></table>
                                <!--<a class="info" href="../#/">دنبال کردن</a>-->
                            </div>
                        </div>
                    </a><!-- ./col -->
                    <a class="col-sm-6 col-xs-12 no-padding"
                       href="../Forum/#/MainForum/DataSwitch">
                        <div class="hovereffect">
                            <img class="img-responsive" src="../images/final-markaz.jpg" id="image">
                            <div ng-click="changeView('DataSwitch')" class="overlay link">
                                <table style="height:100%;width:100%"><tr><td style="vertical-align:middle"><h2>گروه مراکز خودکار و دیتا سوئیچ</h2></td></tr></table>

                                <!--<a class="info" href="../#/">دنبال کردن</a>-->
                            </div>
                        </div>
                    </a><!-- ./col -->
                    <a class="col-sm-6 col-xs-12 no-padding"
                       href="../Forum/#/MainForum/Radio">
                        <div class="hovereffect">
                            <img class="img-responsive" src="../images/final-radio.jpg" id="image">
                            <div ng-click="changeView('Radio')" class="overlay link">
                                <table style="height:100%;width:100%"><tr><td style="vertical-align:middle"><h2>گروه رادیویی</h2></td></tr></table>
                                <!--<a class="info" href="../#/">دنبال کردن</a>-->
                            </div>
                        </div>
                    </a><!-- ./col -->
                </div>
                <div class="col-md-2 no-padding">
                    <a class="hovereffect no-background"
                       href="../Forum/#/MainForum/CommonTopics">
                        <img class="img-responsive" src="../images/final-right.jpg">
                        <div ng-click="changeView('CommonTopics')" class="overlay link">

                            <!--<a class="info" href="../#/">دنبال کردن</a>-->
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div><!-- ./wrapper -->


<!-- jQuery 2.1.4 -->
<script src="../cms/js/jQuery/jQuery-2.1.4.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../cms/js/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.5 -->
<script src="../cms/js/bootstrap.min.js"></script>
<!-- Bootstrap WYSIHTML5
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>-->
<!-- Slimscroll -->
<script src="../cms/js/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../cms/js/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="../cms/js/app.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes)
<script src="../dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="../cms/js/demo.js"></script>
<script src="../js/canvas-snow.js"></script>

</body>
</html>



