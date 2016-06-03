<?php

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


    $userID = $db->insertToTable('user','FullName,Email,Password,PhoneNumber,SignupDate,OrganizationID'
        ,"'$r->FullName','$r->Email','$r->Password','$r->PhoneNumber',now(),'$r->OrganizationID'",true);


    $linkID = generateRandomString(30);
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
                 ----> <a href="http://www.sepantarai.com/verify.php?link='.$linkID.'"> Verify account link </a> 
                 <----
                </p>
                </body>
                </html>
';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: offical@sepantarai.com' . "\r\n" .
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

$app->post('/forgetPassword', function() use ($app)  {
    $db = new DbHandler();
    $data = json_decode($app->request->getBody());
    if(!isset($data->Email))
        echoError('bad request');
    $reqsql = $db->makeQuery("select u.FullName ,u.UserAccepted, u.ID from user as u where u.Email = '$data->Email'");
    $res = $reqsql->fetch_assoc();
    if(!$res)
        echoError('this email is not valid');
    if($res["UserAccepted"] != 1)
        echoError('this user is not accepted yet . you dont have permission to change password');

    $newPassword = generateRandomString(10);
    require_once '../passwordHash.php';
    $password = passwordHash::hash($newPassword);
    $reqsql = $db->updateRecord('user' , "Password ='".$password."'" , "ID = ".$res["ID"]);
    if(!$reqsql)
        echoError('failed to update password');

    $subject = 'Sepantarai.com';
// message
    $message = '
                <html>
                <head>
                  <title></title>
                </head>
                <body>
<p style="direction: rtl">سلام '.$res["FullName"].'</p><br>
                <p style="direction: rtl">پسورد جدید شما :
<br>'.$newPassword.'
<br> ایمیل شما:
'.$data->Email.'
                </p>
                </body>
                </html>
';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .=  'From: sepantarai@sepantarai.com' . "\r\n" .
        'Reply-To: '.$data->Email."\r\n" .
        'X-Mailer: Sepantarai.com';

    //if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")))
    {
        mail($data->Email, $subject, $message, $headers);
    }

    echoSuccess();
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
    user LEFT JOIN 
    file_storage ON file_storage.ID=user.AvatarID where Email='$email' and UserAccepted = 1");

    if ($user != NULL) {
        if(passwordHash::check_password($user['Password'],$password)){

            $sessionID = generateSessionID(100);
            $resSessionIDQ = $db->insertToTable('user_session',"UserID,SessionID,LoginDate,DeviceName,IP","'"
                .$user['ID']."','$sessionID',NOW(),'".getOperatingSystem()."','".getIPAddress()."'");
//
//            $db->deleteFromTable('LoginTime','ID IN (
//     SELECT ID
//     WHERE user_id = 1
//     ORDER BY datetime DESC
//     LIMIT 0, 5
//)');

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
                $response['AdminPermissionLevel'] = $admin['PermissionLevel'];
                $response['AdminPermission'] = $admin['Permission'];
            }

            if (!isset($_SESSION)) {
                session_start();
            }

            $cookiePath = '/';
            $cookieTime = time() + 914748364;

            setcookie("SSN", $sessionID, $cookieTime ,$cookiePath);
            setcookie("FullName", $user['FullName'],$cookieTime,$cookiePath);
            setcookie("IsAdmin", ($IsAdmin)?1:0, $cookieTime,$cookiePath);
            setcookie("Email", $email, $cookieTime,$cookiePath);
            setcookie("UserID", $user['ID'], $cookieTime,$cookiePath);
            setcookie("SignupDate", $user['SignupDate'], $cookieTime,$cookiePath);
            setcookie("Image", $user['Image'],$cookieTime,$cookiePath);

            $_SESSION['Status'] = "success";
            $_SESSION['SSN'] = $sessionID;
            $_SESSION['FullName'] = $user['FullName'];
            $_SESSION['IsAdmin'] = $IsAdmin;
            $_SESSION['Email'] = $email;
            $_SESSION['UserID'] = $user['ID'];
            $_SESSION['SignupDate'] = $user['SignupDate'];
            $_SESSION['Image'] = $user['Image'];

            if($IsAdmin){
                $_SESSION['AdminID'] = $admin['ID'];
                $_SESSION['AdminPermissionLevel'] = $admin['PermissionLevel'];
                $_SESSION['AdminPermission'] = $admin['Permission'];

                setcookie("AdminID", $admin['ID'], $cookieTime,$cookiePath);
                setcookie("AdminPermissionLevel", $admin['PermissionLevel'], $cookieTime,$cookiePath);
                setcookie("AdminPermission", $admin['Permission'],$cookieTime,$cookiePath);
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