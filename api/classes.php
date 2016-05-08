<?php

class Pagination {

    public $PageIndex = 1;
    public $PageSize = 200;

    function __construct($data) {  
	    if(isset($data->pageSize) && isset($data->pageIndex)){
	        $this->PageSize = $data->pageSize;
	        $this->PageIndex = $data->pageIndex;
	    }
    }
    
    public function getPage($db,$query){
		$startFromStr = strstr($query, 'FROM');
		$countQ = $db->makeQuery("SELECT count(*) as Total ".$startFromStr);
		$countRes = $countQ->fetch_assoc();
		$total = $countRes['Total'];
		
    	$offset = ($this->PageIndex-1) * $this->PageSize;
		
		$q = $db->makeQuery($query." LIMIT $offset, $this->PageSize");
		
		$items = [];
		while($r = $q->fetch_assoc()){
            $items[] = $r;
        }
        
		$res = [];
		$res['Items'] = $items;
		$res['PageSize'] = $this->PageSize;
		$res['PageIndex'] = $this->PageIndex;
		$res['Total'] = $total;
		
		return $res;
		
	}

}

class SiteInfo{
	
    public $version;
	public $forceUpdateDB;
	
    function __construct() {  
    	$forceUpdateDB = FALSE;
    }
    
    function getFromFile($filePath){
		
		$myfile = fopen($filePath, "r") or die("Unable to open '".$filePath."' version file!");
		$content = fgets($myfile);
		
		//$this->version = $line;
    	$splits = split(',', $content);
    	$this->version = $splits[0];
    	$this->forceUpdateDB = ($splits[1]=='0')?FALSE:TRUE;
		fclose($myfile);
	}
}
?>