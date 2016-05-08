<?php

require_once '../dbHandler.php';
require_once '../classes.php';
require_once '../passwordHash.php';
require '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

require_once 'admin_service.php';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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