<?php

class DbHandler {

    private $conn;
    private $db;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
        //$this->conn->query('SET CHARACTER SET utf8') or die($this->conn->error.__LINE__);
    }
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();    
    }
    
    public function startTransaction() {
	$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
       // $this->conn->autocommit(FALSE);
    }
    public function rollbackTransaction() {
        $this->conn->rollBack();
    }
    public function commitTransaction() {
        $this->conn->commit();
    }
    /**
     * Fetching records
     */
    public function getRecords($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        $result = array();
        while($res = $r->fetch_assoc()){
            $result[] = $res;
        }
        return $result;
    }
    /**
     * Fetching records
     */
    public function makeQuery($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        return $r;
    }
    public function execute($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        return $r;
    }
    public function multiQuery($queries) {
        $r = $this->conn->multi_query($queries) or die($this->conn->error.__LINE__);
        return $r;
    }
    /**
     * Fetching records
     */
    public function prepare($query) {
        $r = $this->conn->prepare($query) or die($this->conn->error.__LINE__);
        return $r;
    }
    /**
     * Fetching records
     */
    public function getConnection() {
        return $this->conn;
    }
    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {
        
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO `".$table_name."` (".trim($columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
    public function deleteFromTable($table_name , $where) {
        
        $query = "DELETE FROM `".$table_name."` WHERE ".$where;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            return $r;
        }
        return NULL;
    }
    public function existsRecord($table_name , $where) {
        
        $query = "SELECT count(*) as Total FROM `".$table_name."` WHERE ".$where;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
		$res = $r->fetch_assoc();
        if ($res["Total"] > 0) {
            return TRUE;
        }
        return FALSE;
    }
    public function getCount($table_name , $where) {
        
        $query = "SELECT count(*) as Total FROM `".$table_name."` WHERE ".$where;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
		$res = $r->fetch_assoc();
        return $res["Total"];
    }
    public function insertToTable($table_name , $col_names,$values ) {
        
        $query = "INSERT INTO `".$table_name."` (".$col_names.") VALUES(".$values.")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            return $r;
        }
        return NULL;
    }

    public function updateRecord($table_name , $set , $where) {
        
        $query = "UPDATE `".$table_name."` SET ".$set." WHERE ".$where;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            return $r;
        }
        return NULL;
    }

	public function getPage($table_name,$pageSize,$pageIndex,$selects,$where,$query){
		$total = $this->getCount($table_name,$where);
    	$offset = ($pageIndex-1) * $pageSize;
		
		$q = $this->makeQuery("SELECT ".$selects." FROM `".$table_name."` ".$query.
		" LIMIT $offset, $pageSize");
		
		$items = [];
		while($r = $q->fetch_assoc()){
            $items[] = $r;
        }
        
		$res = [];
		$res['Items'] = $items;
		$res['PageSize'] = $pageSize;
		$res['PageIndex'] = $pageIndex;
		$res['Total'] = $total;
		return $res;
	}
	
	
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
	    return $sess;
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
