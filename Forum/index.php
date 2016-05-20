<!DOCTYPE html>
<html ng-app="forumApp" ng-controller="MainCtrl">
<head>
    <?php
    require  '../cms/functions.php';

    if (!isset($_SESSION)) {
        session_start();
        if(!isset($_SESSION['UserID'])){?>
            <script>
                window.location ="../";
            </script>
            <?php
        }else{
            require_once  '../session_generator.php';
            generateSessionAsJavascriptVariable();
        }
    }
    generateMetas();
    generateRequiredCMSCssFiles();
            ?>

    <title ng-bind="($title || 'Loading ...')">Loading ...</title>
    <link rel="icon" href="{{titleIcon}}">

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
                            <span class="label label-success">{{UserMessages.NewMessages}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما {{UserMessages.NewMessages}} پیام جدید دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul  class="menu">
                                    <li style="margin:10px" ng-repeat="item in UserMessages" class="persian-rtl link">
                                        <h5 class="text-right" ui-sref="question({id:item.EventID})">
                                                <span ng-show="item.EventType =='Person'">
                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                    سوال
                                                </span>
                                                <span ng-show="item.EventType =='Answer'">
                                                    <i class="fa fa-reply" aria-hidden="true"></i>
                                                    پاسخ
                                                </span>
                                                <span ng-show="item.EventType =='Question'">
                                                    <i class="fa fa-question" aria-hidden="true"></i>
                                                    سوال
                                                </span>
                                            <small>- {{item.EventUser}}</small>
                                            <i ng-show="item.EventView !=null" class="fa fa-check text-success pull-left hvr-pulse" aria-hidden="true"></i>
                                            <i ng-hide="item.EventView !=null" class="fa fa-eye text-danger pull-left hvr-pulse" aria-hidden="true"></i>
                                            <small class="pull-left">{{item.EventDate | fromNow}}
                                            </small>
                                        </h5>
                                        <h5 class="text-right text-info" ui-sref="question({id:item.EventID})">{{item.EventTitle | subString :35}}
                                                <span class="pull-left" dir="ltr" ng-show="item.EventScore !=null">
                                                    <i class="fa fa-thumbs-o-up"></i>
                                                    {{item.EventScore}}
                                                </span>
                                        </h5>
                                        <hr />
                                        <!--<div class="hr col-xs-12" style="margin-bottom:5px;margin-top:5px;"></div>-->
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a>نمایش همه پیام ها</a></li>
                        </ul>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">0</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما 0 اعلان جدید دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                </ul>
                            </li>
                            <li class="footer"><a href="#">نمایش همه</a></li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                        </a>
                        <ul class="dropdown-menu">
                                <li class="header">آخرین سوالات شما</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li style="margin:10px" ng-repeat="item in UserQuestions" class="persian-rtl link">
                                            <h5 class="text-right" ui-sref="question({id:item.ID})">
                                                {{item.Title | subString :20}}
                                                <span class="pull-left persian-rtl" dir="rtl" ng-show="item.CreationDate !=null">
                                                    {{item.CreationDate | fromNow}}
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                            </h5>
                                            <h5>
                                                <span ng-show="item.QuestionUserFollow != null" class="description pull-right">
                                                    {{item.QuestionUserFollow}}
                                                    <i class="fa fa-users" aria-hidden="true"></i>
                                                </span>
                                                <span ng-show="item.questionView != null" class="description text-ceter">
                                                    {{item.questionView}}
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </span>
                                                <span ng-show="item.questionAnswers != null" class="description pull-left">
                                                    {{item.questionAnswers}}
                                                    <i class="fa fa-home" aria-hidden="true"></i>
                                                </span>
                                                <span ng-show="item.QuestionRate != null" class="description pull-left">
                                                    {{item.QuestionRate}}
                                                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                                </span>
                                            </h5>
                                            <hr />
                                            <!--<div class="hr col-xs-12" style="margin-bottom:5px;margin-top:5px;"></div>-->
                                        </li>
                                </ul>
                            </li>
                            <li class="footer">
                                    <a href="#/">نمایش همه سوالات شما</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu">
                        <a class="dropdown-toggle link" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">۴</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">شما {{UserMessages.NewMessages}} پیام جدید دارید!</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">

                                    <li ng-repeat="ou in socketData.OnlineUsers"><!-- start message -->
                                        <a >
                                            <div class="pull-right">
                                                <img src="../images/Avatar.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4 class="vazir-font">
                                               {{ou.FullName}}
                                                <small class="persian-rtl"><i class="fa fa-clock-o"></i> ۵ دقیق پیش</small>
                                            </h4>
                                            <p style="padding-top: 5px"> <i class="fa fa-circle text-success"></i>
                                                آنلاین </p>

                                        </a>
                                    </li><!-- end message -->
                                </ul>
                            </li>
                            <li class="footer"><a>نمایش همه پیام ها</a></li>
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
                                <img ng-src="{{user.Image}}" class="img-circle link" alt="User Image" ui-sref="profile">
                                <p>
                                    <span ng-bind="user.FullName"> </span> - کاربر
                                    <small class="vazir-font">عضویت در سال {{user.SignupDate |  jalaliDateSimple:'jYYYY'}}</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-5 text-center no-padding">
                                    <a href="">
                                        <small class="vazir-font">دنبال کنندگان</small>
                                    </a>
                                </div>
                                <div class="col-xs-3 text-center no-padding">
                                    <a href="">
                                        <small class="vazir-font">پیام ها</small>
                                    </a>
                                </div>
                                <div class="col-xs-4 text-center no-padding">
                                    <a href="">
                                        <small class="vazir-font">دوستان</small>
                                    </a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a ui-sref="profile" class="btn btn-default btn-flat">پروفایل</a>
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
                <a ui-sref="profile" class="pull-right image">
                    <img  ng-src="{{user.Image}}" class="img-circle" alt="User Image">
                </a>
                <div class="pull-left info">
                    <p><a ui-sref="profile" ng-bind="user.FullName"></a></p>
                    <a>آنلاین <i class="fa fa-circle text-success"></i></a>
                </div>
            </div>
            <!-- search form -->
            <form class="sidebar-form" style="overflow: visible">
<!--                <div class="input-group persian-rtl">-->
<!--                    <input type="text" name="q" class="form-control vazir-font" placeholder="جستجو ...">-->
<!--              <span class="input-group-btn">-->
<!--                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--                </div>-->

                <div class="input-group input-group input-group-ltr">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-search" style="padding-right: 5px"></i>
                            <span class="fa fa-caret-down"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li ng-class="{'active':pagingParams.searchType==0}">
                                <a ng-click="searchTypeChanges(0)" class="link">
                                    <i class="fa fa-question-circle"></i>
                                    جستجو در انجمن
                                </a>
                            </li>
                            <li ng-class="{'active':pagingParams.searchType==1}">
                                <a ng-click="searchTypeChanges(1)" class="link">
                                    <i class="fa fa-user"></i>
                                    جستجو کاربر
                                </a>
                            </li>
                            <li ng-class="{'active':pagingParams.searchType==2}">
                                <a ng-click="searchTypeChanges(2)" class="link">
                                    <i class="fa fa-bullhorn"></i>
                                    جستجو کامل
                                </a>
                            </li>
                        </ul>
                    </div><!-- /btn-group -->
                    <input type="text" class="form-control persian-rtl" ng-model="pagingParams.searchValue"
                           placeholder="جستجو ..." ng-change="searchBoxChanged()" ng-enter="search()"
                           ng-model-options="{debounce : 100}" >
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">


                <li id="SForum" class="treeview">
                    <a class="link">
                        <i class="fa fa-users"></i> <span>انجمن</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="SForumHome"><a ui-sref="forum_home"><i class="fa fa-circle-o"></i>
                                خانه </a></li>
                        <li id="SDataSwitch"><a ui-sref="main_forum({id:'DataSwitch'})"><i class="fa fa-circle-o"></i>
مراکز خودکار و دیتا سوئیچ</a></li>
                        <li id="SRadio"><a ui-sref="main_forum({id:'Radio'})"><i class="fa fa-circle-o"></i>
                                رادیوئی</a></li>
                        <li id="STransportManagement"><a ui-sref="main_forum({id:'TransportManagement'})"><i class="fa
                        fa-circle-o"></i> نظارت بر سیستم ها انتقال
                            </a></li>
                        <li id="STransition"><a ui-sref="main_forum({id:'Transition'})"><i class="fa fa-circle-o"></i>
                                خطوط انتقال</a></li>

                        <li id="SCommonTopics"><a ui-sref="main_forum({id:'CommonTopics'})"><i class="fa fa-circle-o"></i>
                                مباحث مشترک</a></li>
                    </ul>
                </li>

                <li id="SQuestion" class="treeview">
                    <a class="link">
                        <i class="fa fa-question-circle"></i> <span>مدیریت سوال ها</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="SQuestionNew">
                            <a ui-sref="new_question">
                                <i class="fa fa-circle-o"></i>ایجاد سوال جدید
                            </a>
                        </li>
                        <li id="SQuestions">
                            <a ui-sref="questions">
                                <i class="fa fa-circle-o"></i>سوالات شما
                            </a>
                        </li>
                    </ul>
                </li>
                <li id="SProfile">
                    <a ui-sref="profile">
                        <i class="fa fa-male"></i><span>  پروفایل من </span>
                        <small class="label pull-right bg-green">جدید</small>
                    </a>
                </li>
                <li ng-if="user.IsAdmin=='1'" >
                    <a href="../Admin/#/">
                        <i class="fa fa-briefcase"></i><span> پنل ادمین </span>
                    </a>
                </li>
                <li class="treeview">
                    <a class="link" ng-click="logout()">
                        <i class="fa fa-power-off"></i>
                        <span>خارج شدن</span>
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

    <div class="content-wrapper" style="min-height: 600px;">
        <treasure-overlay-spinner active='spinner.active' spinner-storke-width="3" spinner-size="60">
        </treasure-overlay-spinner>
        <!-- Content Wrapper. Contains page content -->
        <div ui-view ng-hide="globalSearchActive" id="mainContent" data-anim-speed="600"
             class="anim-in-out anim-slide-below-fade" data-anim-sync="true"></div>
        <div ng-show="globalSearchActive" ng-include src="'partials/GlobalSearch.html'" id="searchContent" ></div>
        <!-- /.content-wrapper -->
    </div>

    <footer class="main-footer persian-rtl">
        <div class="pull-left hidden-xs">
            <b>نسخه</b> 1.0.0
        </div>
            <strong class="persian-rtl"></strong>  حقوق مادی و معنوی متعلق به اداره کل علائم و ارتباطات راه آهن جمهوری اسلامی ایران است.
          Copyright &copy;
        2014-2015 <!--<a class="persian-rtl">طراحی شده توسط MagicCube.ir</a>-->.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark" id="side-bar-fixer">
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

    <?php  generateRequiredCMSJavaFiles(); ?>
    <script src="angular/forum-app.js"></script>
    <script src="partials/MainCtrl.js"></script>
    <script src="../app/directives/auto-pagination.js"></script>

</body>
</html>

