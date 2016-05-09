<?php

class Session {
	
	public function getSession(){
	    if (!isset($_SESSION)) {
	    	session_start();
	    }
	    $sess = [];

        if(isset($_SESSION['UserID'])){
	        $sess['Status'] = "success";
	        $sess["LastName"] = $_SESSION['LastName'];
	        $sess["FirstName"] = $_SESSION['FirstName'];
	        $sess["Email"] = $_SESSION['Email'];
	        $sess["SSN"] = $_SESSION['SSN'];
	        $sess["UserID"] = $_SESSION['UserID'];
	        $sess["IsAdmin"] = $_SESSION['IsAdmin'];
		}
        
        if (isset($_SESSION["AdminID"])) {
         	$sess["AdminID"] = $_SESSION['AdminID'];
        }

		$db = new DbHandler();

		$resQ = $db->makeQuery("SELECT  `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, `SignupDate`, `Gender` , FullPath as 
AvatarImagePath FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = 1");

		$res = $resQ->fetch_assoc();
		$res['Valid'] = true;
	    return $res;
	}
	
	public function destroySession(){
	    if (!isset($_SESSION)) {
	    session_start();
	    }
	    if(isSet($_SESSION['UserID']))
	    {
	        unset($_SESSION['AdminID']);
	        unset($_SESSION['UserID']);
	        unset($_SESSION['LastName']);
	        unset($_SESSION['FirstName']);
	        $info='info';
	        if(isSet($_COOKIE[$info]))
	        {
	            setcookie ($info, '', time() - $cookie_time);
	        }
	        $msg="Logged Out Successfully...";
	    }
	    else
	    {
	        $msg = "Not logged in...";
	    }
	    return $msg;
	}
 
}

?>
