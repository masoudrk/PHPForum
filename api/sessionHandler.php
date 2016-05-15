<?php

class Session {

	public $Valid;

	public $Status;
	public $FullName;
	public $Email;
	public $SSN;
	public $UserID;
	public $IsAdmin;
	public $SignupDate;
	public $Image;

	function __construct() {
		if (!isset($_SESSION)) {
			session_start();
		}
		if(isset($_SESSION['UserID'])){
			$this->Valid = true;
			$this->UserID = $_SESSION['UserID'];
			$this->FullName = $_SESSION['FullName'];
			$this->Email = $_SESSION['Email'];
			$this->SSN = $_SESSION['SSN'];
			$this->IsAdmin = $_SESSION['IsAdmin'];
			$this->SignupDate = $_SESSION['SignupDate'];
			$this->Image = $_SESSION['Image'];
		}
		else
		{
			$this->Valid = false;
		}
	}

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
			$sess["SignupDate"] = $_SESSION['SignupDate'];
			$sess["Image"] = $_SESSION['Image'];
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

	public function updateImage($value){
		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['Image'] = $value;
	}

	public function updateFullName($fullName){
		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['FullName'] = $fullName;
	}

	public function destroySession(){
		$res = [];
	    if (!isset($_SESSION)) {
	    session_start();
	    }
	    if(isSet($_SESSION['UserID']))
	    {
			unset($_SESSION['UserID']);
			unset($_SESSION['FullName']);
			unset($_SESSION['Email']);
			unset($_SESSION['SSN']);
	        unset($_SESSION['IsAdmin']);
			unset($_SESSION['SignupDate']);
			unset($_SESSION['Image']);
			unset($_SESSION['FirstName']);
	        $info='info';
	        if(isSet($_COOKIE[$info]))
	        {
	            setcookie ($info, '', time() - $cookie_time);
	        }
			$res['Status'] = 'success';
	    }
	    else
	    {
			$res['Status'] = 'error';
	    }

	    return $res;
	}
 
}

?>
