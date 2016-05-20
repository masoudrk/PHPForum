<?php
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields,$request_params) {
    $error = false;
    $error_fields = "";
    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(200, $response);
        $app->stop();
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateSessionID($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()/?>;,';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
    die();
}

function echoError($message) {
    $response=[];
    $response["Status"] = "error";
    $response["Message"] = $message;
    echoResponse(201, $response);
    die();
}

function echoSuccess($obj = null) {
    $response["Data"]=$obj;
    $response["Status"] = "success";
    echoResponse(200, $response);
    die();
}

function getIPAddress(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function userRequire($db){
    $sess = new Session();

    $rq = $db->makeQuery("SELECT u.ID FROM user as u
where u.ID='".$sess->UserID."' AND u.SessionID='".$sess->SSN."' AND u.ValidSessionID=1 LIMIT 1");

    $c=mysqli_num_rows($rq);
    if($c > 0){
        $db->updateRecord('user',"LastActiveTime=Now()","ID='$sess->UserID' LIMIT 1");
        return TRUE;
    }

    $sess->destroySession();
    $res = [];
    $res['AuthState'] = 'UN_AUTH';
    echoResponse(201,$res);
    die();
}

function createSecureDbHandler(){
    $db = new DbHandler();
    $sess = new Session();
//
//    $rq = $db->makeQuery(
//        "SELECT admin.ID FROM user left join admin on admin.UserID=user.ID left join admin_permission on admin_permission
//.ID=admin.PermissionID where admin_permission.PermissionLevel in('$permissionLevels') AND user.ValidSessionID=1 and 
//user
//.SessionID='$sess->SSN'");

    $rq = $db->makeQuery(
        "SELECT admin.ID FROM user left join admin on admin.UserID=user.ID left join admin_permission on admin_permission
.ID=admin.PermissionID where AND user.ValidSessionID=1 and 
user
.SessionID='$sess->SSN'");

    $r = $rq->fetch_assoc();
    if($r){
        return $db;
    }

    $res = [];
    $res['AuthState'] = 'UN_AUTH';
    echoResponse(201,$res);
    die('');
}

function getCurrentUser(){
    $db = new DbHandler();
    $sess = $db->getSession();
    $rq = $db->makeQuery(
        "SELECT * ,user.ID as UserID,admin.ID as AdminID FROM user Left JOIN admin on admin.UserID=user.ID where user.SessionID='".$sess["SSN"]
        ."' AND SessionValid=1");

    $r = $rq->fetch_assoc();
    return $r;
    die('Encrypted media , Admininistrator auth has been failed.');
}

function privilegeRequire($privilege){
    $db = new DbHandler();
    $sess = $db->getSession();
    $rq = $db->makeQuery("SELECT privilege.Privilege FROM user JOIN admin on admin.UserID=user.ID JOIN admin_privilege on admin_privilege.ID =admin.PrivilegeID where privilege.Privilege='".$privilege."' AND user.ID='".$sess["UserID"]."'");
    $r = $rq->fetch_assoc();
    if($r){
        return TRUE;
    }
    return False;
}

?>
