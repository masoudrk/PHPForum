
<?php
if (!isset($_SESSION)) {
    session_start();
    if(!isset($_SESSION['UserID'])){?>
        <script>
         //     window.location ="../";
        </script>
        <?php
    }else{?>

        <script>
            var session = {};
        </script>
        <?php
    }
}
?>
<!DOCTYPE html>
<html ng-app="forumApp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="fa"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>

    <title ng-bind="($title || 'Loading ...')">Loading ...</title>
    <link rel="icon" href="{{titleIcon}}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    <link rel="stylesheet" href="../css/toaster.css">
    <link rel="stylesheet" href="../css/treasure-overlay-spinner.css">
    <link rel="stylesheet" href="../cms/css/select/select.css">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../cms/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- Ionicons -->
    <!--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
    <!-- Theme style -->
    <link rel="stylesheet" href="../css/site-styles.css">

    <link rel="stylesheet" href="../cms/css/AdminLTE-rtl.css">
    <link rel="stylesheet" href="../cms/css/AdminLTE-rtl-fix.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../cms/css/skins/_all-skins-srtl.css">

</head>
<body class="hold-transition skin-blue sidebar-mini vazir-font">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>M</b>S</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg vazir-font">پنل مدیریت</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a class="link" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما 4 پیام جدید دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-right">
                                                <img src="../images/masoud.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                                تیم پشتیبانی
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> 5 دقیق پیش
                                                </small>
                                            </h4>
                                            <p>چرا شما یک تم جدید خریداری نمی کنید؟</p>
                                        </a>
                                    </li><!-- end message -->
                                    <li>
                                        <a href="#">
                                            <div class="pull-right">
                                                <img src="../images/user3-128x128.jpg" class="img-circle"
                                                     alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                                تیم طراحی
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> 2 ساعت</small>
                                            </h4>
                                            <p>لطفا به پیشنهاد من توجه کنید!</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-right">
                                                <img src="../images/user4-128x128.jpg" class="img-circle"
                                                     alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                                برنامه نویس
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> امروز</small>
                                            </h4>
                                            <p>بهترین نسخه این cms در حال بروزرسانیست! به من یک پیام بدهید.</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-right">
                                                <img src="../images/user3-128x128.jpg" class="img-circle"
                                                     alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                                فروشندگان
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> دیروز</small>
                                            </h4>
                                            <p>لطفا یک پیام به من بدهید</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-right">
                                                <img src="../images/user4-128x128.jpg" class="img-circle"
                                                     alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                                بینندگان
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> 2 روز پیش
                                                </small>
                                            </h4>
                                            <p>ممنون از انتقاد شما دوست عزیز</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">نمایش همه پیام ها</a></li>
                        </ul>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما 10 اعلان جدید دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 5 عضو جدید به ما پیوستند!
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-warning text-yellow"></i> در حال حاضر سایت به صورت فعال عمل
                                            میکند اما اخیرا به مشکلی در سرور برخورده ایم
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-red"></i> 12 عضو جدید به ما پیوستند
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-shopping-cart text-green"></i> سوالات شما در حال منقضی شدن
                                            هستند
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-user text-red"></i> شما رمز خود را عوض کردید
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">نمایش همه</a></li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما 9 کار انجام نشده دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3 class="vazir-font">
                                                طراحی دکمه ها
                                                <small class="pull-left">20%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">20% انجام شده</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3 class="vazir-font">
                                                ایجاد تم زیبا
                                                <small class="pull-left">40%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">40% انجام شده</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3 class="vazir-font">
                                                برخی از کارها که باید انجام شوند
                                                <small class="pull-left">60%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-red" style="width: 60%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">60% انجام شده</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3 class="vazir-font">
                                                ایجاد انیمیشن های زیبا
                                                <small class="pull-left">80%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-yellow" style="width: 80%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">80% انجام شده</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li><!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">نمایش همه وظایف</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <img ng-src="{{user.Image}}" class="user-image" alt="User Image">
                            <span class="hidden-xs" ng-bind="user.FullName"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img ng-src="{{user.Image}}" class="img-circle" alt="User Image">
                                <p>
                                    <span ng-bind="user.FullName"> </span> - ادمین
                                    <small class="vazir-font">عضویت در سال 1392</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-5 text-center no-padding">
                                    <a href="#">
                                        <small class="vazir-font">دنبال کنندگان</small>
                                    </a>
                                </div>
                                <div class="col-xs-3 text-center no-padding">
                                    <a href="#">
                                        <small class="vazir-font">فروش ها</small>
                                    </a>
                                </div>
                                <div class="col-xs-4 text-center no-padding">
                                    <a href="#">
                                        <small class="vazir-font">دوستان</small>
                                    </a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">پروفایل</a>
                                </div>
                                <div class="pull-right">
                                    <a ng-click="logout()" class="btn btn-default btn-flat">خروج</a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-right image">
                    <img  ng-src="{{user.Image}}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p ng-bind="user.FullName"></p>
                    <a ui-sref="profile">آنلاین <i class="fa fa-circle text-success"></i></a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group persian-rtl">
                    <input type="text" name="q" class="form-control vazir-font" placeholder="جستجو ...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">

                <li>
                    <a ui-sref="profile">
                        <i class="fa fa-male"></i><span>  پروفایل من </span>
                        <small class="label pull-right bg-green">جدید</small>
                    </a>
                </li>
                <li class="treeview">
                    <a class="link">
                        <i class="fa fa-laptop"></i>
                        <span>فروم</span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="link">
                        <i class="fa fa-envelope"></i>
                        <span>پیام ها</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <script  id="notifyModal.html" type="text/ng-template">
        <div class="modal-header">
            <h3 class="modal-title">Error in XHR request </h3>
        </div>
        <div class="modal-body">
            <data compile="data"></data>
        </div>
    </script>

    <toaster-container toaster-options="{'time-out': 10000, 'position-class': 'toast-bottom-right', 'close-button':true, 'animation-class': 'toast-bottom-right'}"></toaster-container>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" ui-view style="min-height:981">
    </div><!-- /.content-wrapper -->

    <footer class="main-footer persian-rtl">
        <div class="pull-left hidden-xs">
            <b>نسخه</b> 2.3.0
        </div>
        <strong class="persian-rtl"></strong> تمام حقوق مادی و معنوی این وبسایت برای سازنده محفوظ است.Copyright &copy;
        2014-2015 <a class="persian-rtl" href="http://almsaeedstudio.com">طراحی شده توسط MagicCube.ir</a>.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Activity</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-user bg-yellow"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
                                <p>New phone +1(800)555-1234</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>
                                <p>nora@example.com</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-file-code-o bg-green"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
                                <p>Execution time 5 seconds</p>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

                <h3 class="control-sidebar-heading">Tasks Progress</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="label label-danger pull-right">70%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Update Resume
                                <span class="label label-success pull-right">95%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Laravel Integration
                                <span class="label label-warning pull-right">50%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Back End Framework
                                <span class="label label-primary pull-right">68%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

            </div><!-- /.tab-pane -->
            <!-- Stats tab content -->
            <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div><!-- /.tab-pane -->
            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">General Settings</h3>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Some information about this general settings option
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Allow mail redirect
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Other sets of options are available
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Expose author name in posts
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Allow the user to show his name in blog posts
                        </p>
                    </div><!-- /.form-group -->

                    <h3 class="control-sidebar-heading">Chat Settings</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Show me as online
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Turn off notifications
                            <input type="checkbox" class="pull-right">
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Delete chat history
                            <a href="javascript::;" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                        </label>
                    </div><!-- /.form-group -->
                </form>
            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->

<script src="../js/angular.js"></script>
<script src="../js/angular-route.min.js"></script>
<script src="../js/angular-cookies.min.js"></script>
<script src="../js/angular-animate.min.js"></script>
<script src="../js/angular-ui-router.js"></script>

<script src="../js/lazyLoad/ocLazyLoad.min.js" type="text/javascript"></script>
<script src="../js/toaster.js" type="text/javascript"></script>
<script src="../js/ui-bootstrap-tpls-1.2.5.min.js" type="text/javascript"></script>
<script src="../cms/js/select/select.min.js" type="text/javascript"></script>

<script src="angular/forum-app.js"></script>
<script src="partials/MainCtrl.js"></script>

<script type="text/javascript" src="../js/moment.js"></script>
<script type="text/javascript" src="../js/moment-jalaali.js"></script>

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
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>-->
<!-- Slimscroll -->
<script src="../cms/js/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../cms/js/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="../cms/js/app.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes)
<script src="dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="../cms/js/demo.js"></script>
</body>
{{session}}
</html>

