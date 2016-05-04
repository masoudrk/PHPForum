<?php

require_once 'dbHandler.php';
require_once 'passwordHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

require_once 'classes.php';
require_once 'authentication.php';
require_once 'default_service.php';
require_once 'admin_service.php';
require_once 'update_service.php';

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

}
function echoError($message) {
	$response=[];
	$response["Status"] = "error";
	$response["Message"] = $message;
	echoResponse(201, $response);
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

function userRequire(){
	$db = new DbHandler();
    $sess = $db->getSession();
    $rq = $db->makeQuery("SELECT ID FROM user where user.SessionID='".$sess["SSN"]."' AND SessionValid=1");
    $r = $rq->fetch_assoc();
    if($r){
		return TRUE;
	} 
	die('Encrypted media , Admininistrator auth has been failed.');
}

function adminRequire(){
	$db = new DbHandler();
    $sess = $db->getSession();
    $rq = $db->makeQuery(
    "SELECT admin.ID FROM user JOIN admin on admin.UserID=user.ID where user.SessionID='".$sess["SSN"]
    ."' AND SessionValid=1");
    
    $r = $rq->fetch_assoc();
    if($r){
		return TRUE;
	} 
	die('Encrypted media , Admininistrator auth has been failed.');
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

$app->run();
?>