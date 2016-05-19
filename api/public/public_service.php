<?php

$app->post('/logout', function() use ($app)  {
    $sess = new Session();
    $res = $sess->destroySession();
    echoResponse(200, $res);
});

$app->post('/savePerson', function() use ($app) {

    $r = json_decode($app->request->getBody());

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $postfields = array('secret'=>'6LcPBSATAAAAAKqQ2Bl5Y5_rNmX0NelIHchT1ztZ',
        'response'=>$r->myRecaptchaResponse);

    $ch = curl_init( $url );

    if (FALSE === $ch)
        throw new Exception('failed to initialize');

    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = array();
    $googleResponse = curl_exec( $ch );
    if(!$googleResponse){
        $response["Status"] = "error-captcha-response";
        echoResponse(201, $response);
        return;
    }else{
        $resG = json_decode($googleResponse);
        if($resG->success == FALSE){
            $response["Status"] = "error-captcha";
            echoResponse(201, $response);
            return ;
        }
    }

    require_once '../passwordHash.php';
    $password = $r->Password;
    $r->Password = passwordHash::hash($password);

    $db = new DbHandler();

    $resq = $db->makeQuery("select COUNT(*) as resp FROM user WHERE Email = '$r->Email'");
    $res = $resq->fetch_assoc();

    if ($res['resp'] > 0 )
    {
        $response["Status"] = "error-captcha";
        echoResponse(201, $response);
    }


    $userID = $db->insertToTable('user','FullName,Email,Password,Tel,SignupDate,OrganizationID'
        ,"'$r->FullName','$r->Email','$r->Password','$r->Tel',now(),'$r->OrganizationID'",true);


    $linkID = generateRandomString(250);
    $dateplus_1 = strtotime("+1 day", time());
    $subject = 'Sepantarai.com';
// message
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
                <p style="direction: rtl">سلام '.$r->FullName.'! لطفا برای فعال سازی حساب کاربری خود روی لینک زیر کلیک
                 کنید:
<br>
                 ----> <a href="sepantarai.com/verify.php?link='.$linkID.'"> Verify account link </a> <----
                </p>
                </body>
                </html>
';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .=  'From: offical@sepantarai.com' . "\r\n" .
        'Reply-To: '.$r->Email."\r\n" .
        'X-Mailer: Sepantarai.com';

    //if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")))
    {
        mail($r->Email, $subject, $message, $headers);
    }


    $db->insertToTable('user_mail','UserID,LinkID,ExpireDate',"'$userID','$linkID','".date('M d, Y', $dateplus_1)."'");

    $result = [];
    if($userID){
        $result['Status'] = 'success';
    }else{
        $result['Status'] = 'error';
    }

    echoResponse(200, $result);
});

$app->post('/getAllPositions', function() use ($app)  {
    $db = new DbHandler();
    $resq = $db->makeQuery("SELECT * FROM `organ_position` WHERE 1");
    $res=[];
    while($r = $resq->fetch_assoc())
        $res[] = $r;
    echoResponse(200, $res);
});

$app->post('/checkEmail', function() use ($app)  {
    $r = json_decode($app->request->getBody());
    $db = new DbHandler();
    $resq = $db->makeQuery("select COUNT(*) as resp FROM user WHERE Email = '$r->value'");
    $res = $resq->fetch_assoc();
    if ($res['resp'] > 0 )
    {
        echoResponse(200, false);
    }else
        echoResponse(200, true);
});

$app->post('/checkUserName', function() use ($app)  {
    $r = json_decode($app->request->getBody());
    $db = new DbHandler();
    $resq = $db->makeQuery("select COUNT(*) as resp FROM user WHERE Username = '$r->value'");
    $res = $resq->fetch_assoc();
    if ($res['resp'] > 0 )
    {
        echoResponse(200, false);
    }else
        echoResponse(200, true);
});

$app->post('/signInUser', function() use ($app)  {
    require_once '../passwordHash.php';
    $r = json_decode($app->request->getBody());
    $response = array();
    $db = new DbHandler();
    $password = $r->Password;
    $email = $r->Username;

    $user = $db->getOneRecord("select user.ID,FullName,Email,Password,Username,SignupDate,file_storage.FullPath as Image from 
    user 
    LEFT JOIN 
    file_storage ON 
            file_storage.ID=user.AvatarID where Email='$email' and UserAccepted = 1");

    if ($user != NULL) {
        if(passwordHash::check_password($user['Password'],$password)){

            $sessionID = generateSessionID(100);
            $resSessionIDQ = $db->updateRecord('user',"SessionID='".$sessionID."',ValidSessionID=1","ID='".$user['ID']."'");

            $IsAdmin=false;
            $IsAdmin=false;
            $admin = $db->getOneRecord("select * from admin left join admin_permission on admin_permission.ID=admin.PermissionID
             where UserID=".$user['ID']);

            if($admin){
                $IsAdmin=true;
            }

            $response['Status'] = "success";
            $response['SSN'] = $sessionID;
            $response['FullName'] = $user['FullName'];
            $response['Email'] = $user['Email'];
            $response['IsAdmin'] = $IsAdmin;
            $response['UserID'] = $user['ID'];
            $response['Image'] = $user['Image'];
            $response['SignupDate'] = $user['SignupDate'];
            if($IsAdmin){
                $response['AdminID'] = $admin['ID'];
                $response['AdminPermission'] = $admin['PermissionLevel'];
            }

            if (!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['Status'] = "success";
            $_SESSION['SSN'] = $sessionID;
            $_SESSION['FullName'] = $user['FullName'];
            $_SESSION['IsAdmin'] = $IsAdmin;
            $_SESSION['Email'] = $email;
            $_SESSION['UserID'] = $user['ID'];
            $_SESSION['AdminID'] = $admin['ID'];
            $_SESSION['SignupDate'] = $user['SignupDate'];
            $_SESSION['Image'] = $user['Image'];

            if($IsAdmin){
                $_SESSION['AdminID'] = $admin['ID'];
                $_SESSION['AdminPermission'] = $admin['PermissionLevel'];
            }
        } else {
            $response['Status'] = "error";
            $response['Message'] = 'Login failed. Incorrect credentials';
        }
    }else {
        $response['Status'] = "error";
        $response['Message'] = 'No such user is registered';
    }
    echoResponse(200, $response);
});

$app->post('/session', function() use ($app)  {
    $sess = new Session();
    $s = $sess->getSession();
    echoResponse(200, $s);
});

$app->post('/getSiteTitleIcon', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT SiteTitleIcon FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});
$app->post('/getSiteName', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT SiteName FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();

    echoResponse(200, $res);
});
$app->post('/getSiteNameEN', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT SiteNameEN as SiteName FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});

?>