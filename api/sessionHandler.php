<?php

class Session {
	
	public function getSession(){
	    if (!isset($_SESSION)) {
	    	session_start();
	    }
	    $sess = [];

        if(isset($_SESSION['UserID'])){
            $res['Valid'] = true;
	        $sess['Status'] = "success";
	        $sess["FullName"] = $_SESSION['FullName'];
	        $sess["Email"] = $_SESSION['Email'];
	        $sess["SSN"] = $_SESSION['SSN'];
	        $sess["UserID"] = $_SESSION['UserID'];
	        $sess["IsAdmin"] = $_SESSION['IsAdmin'];
		}else
        {
            $res['Valid'] = false;
            return $res;
        }

        if (isset($_SESSION["AdminID"])) {
         	$sess["AdminID"] = $_SESSION['AdminID'];
             $res['Valid'] = true;
        }elseif(!isset($_SESSION['UserID']))
            $res['Valid'] = false;

        $res['Session'] = $sess;
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
