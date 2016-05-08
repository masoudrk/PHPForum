<?php
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

$app->post('/likePost', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $sess = $db->getSession();
    $postID = $data->PostID;
    $isLiked = $data->Like;
    
	if(isset($sess['UserID'])){
		if($isLiked){
			$ex = $db->existsRecord('post_like',"PostID='".$postID."' AND UserID='".$sess['UserID']."'");
			if(!$ex){
				$db->insertToTable('post_like','Identity,PostID,UserID,Date',"'".getIPAddress().
				"','".$postID."','".$sess['UserID']."',NOW()");
				$countQ = $db->makeQuery("SELECT count(*) as Total FROM `post_like` WHERE PostID='".$postID."'");
				$c = $countQ->fetch_assoc();
				$res = [];
				$res["Status"] = "Success";
				$res["Count"] = $c["Total"];
				$res["PostID"] = $postID;
				$res["Liked"] = TRUE;
    			echoResponse(200, $res);
    			return;
			}
		}else{
			$r = $db->deleteFromTable('post_like',"PostID='".$postID."' AND UserID='".$sess['UserID']."'");
			$countQ = $db->makeQuery("SELECT count(*) as Total FROM `post_like` WHERE PostID='".$postID."'");
			$c = $countQ->fetch_assoc();
			$res = [];
			$res["Status"] = "Success";
			$res["Count"] = $c["Total"];
			$res["PostID"] = $postID;
			$res["Liked"] = FALSE;
			echoResponse(200, $res);
			return;
		}
	}
    else{
		if($isLiked){
			$ex = $db->existsRecord('post_like',"PostID='".$postID."' AND Identity='".getIPAddress()."'");
			if(!$ex){
				$date = date('Y-m-d H:i:s');
				$db->insertToTable('post_like','Identity,PostID,Date',"'".getIPAddress().
				"','".$postID."','".$date."'");
				$countQ = $db->makeQuery("SELECT count(*) as Total FROM `post_like` WHERE PostID='".$postID."'");
				$c = $countQ->fetch_assoc();
				$res = [];
				$res["Status"] = "Success";
				$res["Count"] = $c["Total"];
				$res["PostID"] = $postID;
				$res["Liked"] = TRUE;
    			echoResponse(200, $res);
    			return;
			}else{
    			echoError("LastLiked");
    			return;
			}
		}else{
			$r = $db->deleteFromTable('post_like',"PostID='".$postID."' AND Identity='".getIPAddress()."'");
			$countQ = $db->makeQuery("SELECT count(*) as Total FROM `post_like` WHERE PostID='".$postID."'");
			$c = $countQ->fetch_assoc();
			$res = [];
			$res["Status"] = "Success";
			$res["Count"] = $c["Total"];
			$res["PostID"] = $postID;
			$res["Liked"] = FALSE;
			echoResponse(200, $res);
			return;
		}
	}
    echoResponse(200, "Success");
});

$app->post('/uploadFile', function() use ($app)  {
    $typeID = $_POST['fileTypeID'];  
    $description = $_POST['description'];  
    $filename = $_FILES['file']['name'];
    
	$rand = generateRandomString(18);
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	
	$db= new DbHandler();
	$rs = $db->makeQuery("SELECT * FROM file_type where ID=".$typeID);
	$r=$rs->fetch_assoc();
	
	$specFolder = $r['SpecialFolder'];
	$specFile ='';
	if($r["GeneralType"] == 'Image')
		$specFile = $rand.".".$ext;
	else
		$specFile = $filename;
	
	$absPath =  '../../content/'.$specFolder.'/';
    if (!file_exists($absPath)) {
    	mkdir($absPath, 0777, true);
	}
    
    $destination =$absPath.$specFile;
    move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );
    
    $db = new DbHandler();
    $column_names = array( 'FileTypeID','Path','FullPath');
    $object =(object)[
        "FileTypeID" => $typeID,
        "Path" => "content/".$specFolder."/",
        "Description" => $description,
        "FullPath" => "content/".$specFolder."/".$specFile,
        "IsMedia" => '1'
    ];
    $result = $db->insertIntoTable($object, $column_names, "gallery");

    echoResponse(200, "Success");
});

$app->post('/getPostByID', function() use ($app)  {
    $data = json_decode($app->request->getBody());

    $db = new DbHandler();
	$lang = isset($data->Lang);
	
	if($lang && $data->Lang == 'en'){
    	$r = $db->makeQuery("SELECT post.WriteDate,post.ID, post.TitleEN AS Title ,post.ContentEN as Content ,post.BriefContentEN as BriefContent , gallery.FullPath FROM `post` LEFT JOIN gallery on gallery.ID = post.ImageID where post.ID=".$data->PostID);
	}else{
    	$r = $db->makeQuery("SELECT post.WriteDate,post.ID, post.Title ,post.Content ,post.BriefContent, gallery.FullPath FROM `post` LEFT JOIN gallery on gallery.ID = post.ImageID where post.ID=".$data->PostID);
	}

    while($res = $r->fetch_assoc()){
    
        $authorsQ = $db->makeQuery("SELECT gallery.FullPath,admin.ID as AdminID , concat(FirstName , ' ' ,LastName) as FullName FROM post_author 
                                        Left Join admin on admin.ID = post_author.AdminID
                                        Left Join user on user.ID = admin.UserID
                                        Left Join gallery on gallery.ID = user.AvatarID
                                        where PostID=".$res["ID"]);
        $authors = array();
        while($resAuthor = $authorsQ->fetch_assoc()){
            $authors[] = $resAuthor;
        }
        $res["Authors"] = $authors;
        
        $subjectsQ = $db->makeQuery("SELECT * FROM post_subject 
                                        Left Join subject on subject.ID = post_subject.SubjectID
                                        where PostID=".$res["ID"]);
        $subjects = array();
        while($resSubject = $subjectsQ->fetch_assoc()){
            $subjects[] = $resSubject;
        }
        $res["Subjects"] = $subjects;

        echoResponse(200, $res);
        return;
    }
    echoResponse(201, "Error not found post!");
});

$app->post('/getPostComments', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	$query = 'SELECT comment.* , user.LastName , user.FirstName,gallery.FullPath FROM comment LEFT JOIN user on user.ID=comment.UserID LEFT JOIN gallery on gallery.ID=user.AvatarID';
	
	$pageRes = $pr->getPage($db,$query.' WHERE Accepted=1 AND ParentID=-1 AND comment.PostID='.$data->PostID.' ORDER BY Date Desc');
	
    foreach($pageRes['Items'] as &$c){
    	$cq = $db->makeQuery($query.' WHERE Accepted=1 AND comment.ParentID='.$c['ID'].' ORDER BY ID ASC');
    	$c['Childs'] = [];
    	while($cc = $cq->fetch_assoc()){
			$c['Childs'][] = $cc;
		}
    }

    echoResponse(200, $pageRes);
});

$app->post('/saveContactUs', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();

    $sess = $db->getSession();

    if(isset($sess['UserID'])){
    	$res = $db->insertToTable('contact_us',"UserID,Name,Email,PhoneNumber,Message,Date","'".$sess['UserID']."','".$data->name."','".$data->email."','".$data->phone."','".$data->message."',NOW()");
	}else{
        $ip = getIPAddress();
    	$res = $db->insertToTable('contact_us',"Identity,Name,Email,PhoneNumber,Message,Date","'".$ip."','".$data->name."','".$data->email."','".$data->phone."','".$data->message."',NOW()");
	}
	$httpRes = [];
	$httpRes['Status'] = 'success';
    echoResponse(200, $httpRes);
});
$app->post('/saveComment', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    
    $sess = $db->getSession();
    $parent = (isset($data->ParentID))? $data->ParentID : -1 ;
    
    if(isset($sess['UserID'])){
    	$count = $db->getCount('admin','admin.UserID='.$sess['UserID']);
    	if($count > 0){
    		$res = $db->insertToTable('comment',"Content,PostID,UserID,Date,ParentID,Accepted",
    	"'".$data->Content."','".$data->PostID."','".$sess['UserID']."',NOW(),'".$parent."','1'");
		}else{
    		$res = $db->insertToTable('comment',"Content,PostID,UserID,Date,ParentID",
    	"'".$data->Content."','".$data->PostID."','".$sess['UserID']."',NOW(),'".$parent."'");
		}
	}else{
    	$res = $db->insertToTable('comment',"Content,PostID,Identity,Email,Date,ParentID","'".$data->Content."','".$data->PostID."','".$data->Identity."','".$data->Email."',NOW(),'".$parent."'");
	}
	$httpRes = [];
	$httpRes['Status'] = 'success';
	$httpRes['CommentID'] = $res;
    echoResponse(200, $httpRes);
});

$app->post('/getAllPosts', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
    $hasCat = isset($data->catID);
    $lang = isset($data->Lang);
    
    $pageRes = null;
    if(!$hasCat){
    	if($lang && $data->Lang=='en')
			$pageRes = $pr->getPage($db,'SELECT post.WriteDate,post.ID, post.TitleEN AS Title ,post.ContentEN as Content ,post.BriefContentEN as BriefContent, gallery.FullPath as Image FROM post LEFT JOIN gallery on post.ImageID = gallery.ID WHERE Hidden=0 AND EnableEnglish=1 ORDER BY post.ID DESC');
		else
			$pageRes = $pr->getPage($db,'SELECT post.WriteDate,post.ID, post.Title ,post.Content ,post.BriefContent , gallery.FullPath as Image FROM post LEFT JOIN gallery on post.ImageID = gallery.ID WHERE Hidden=0 ORDER BY post.ID DESC');
	}
    else{
    	if($lang && $data->Lang=='en')
			$pageRes = $pr->getPage($db,'SELECT post.ID, post.TitleEN AS Title ,post.ContentEN as Content ,post.BriefContentEN as BriefContent  , gallery.FullPath as Image FROM post LEFT JOIN post_subject on post_subject.PostID=post.ID LEFT JOIN gallery on post.ImageID = gallery.ID WHERE post_subject.SubjectID='.$data->catID.' AND EnableEnglish=1 ORDER BY post.ID DESC');
		else
			$pageRes = $pr->getPage($db,'SELECT post.WriteDate,post.ID, post.Title ,post.Content ,post.BriefContent , gallery.FullPath as Image FROM post LEFT JOIN post_subject on post_subject.PostID=post.ID LEFT JOIN gallery on post.ImageID = gallery.ID WHERE post_subject.SubjectID='.$data->catID.' ORDER BY post.ID DESC');
	}
	
	$sess = $db->getSession();
	$isUser=isset($sess["UserID"]);
	$ip = getIPAddress();
	
    foreach($pageRes['Items'] as &$res){
    	if($isUser){
			$res["Liked"]=$db->existsRecord("post_like","UserID='".$sess["UserID"]."' AND PostID='".$res["ID"]."'");	
		}else{
			$res["Liked"]=$db->existsRecord("post_like","PostID='".$res["ID"]."' AND Identity='".$ip."'");	
		}
		
		$res["LikesCount"] = $db->getCount('post_like','PostID='.$res["ID"]);
		
        $authorsQ = $db->makeQuery("SELECT gallery.FullPath, admin.ID as AdminID , concat(FirstName , ' ' ,LastName) as FullName FROM post_author 
                                        Left Join admin on admin.ID = post_author.AdminID
                                        Left Join user on user.ID = admin.UserID
                                        Left Join gallery on gallery.ID = user.AvatarID
                                        where PostID=".$res["ID"]);
        $authors = array();
        while($resAuthor = $authorsQ->fetch_assoc()){
            $authors[] = $resAuthor;
        }
        $res["Authors"] = $authors;
        
        $subjectsQ = $db->makeQuery("SELECT * FROM post_subject 
                                        Left Join subject on subject.ID = post_subject.SubjectID
                                        where PostID=".$res["ID"]);
        $subjects = array();
        while($resSubject = $subjectsQ->fetch_assoc()){
            $subjects[] = $resSubject;
        }
        $res["Subjects"] = $subjects;

    }

    echoResponse(200, $pageRes);
});

$app->post('/getAllMedia', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $pageSize = 20;
    $pageIndex = 0;
    
    if(isset($data->pageSize) && isset($data->pageIndex)){
        $pageSize = $data->pageSize;
        $pageIndex = $data->pageIndex;
    }
    $hasType = isset($data->fileTypes);
    $isMedia = isset($data->isMedia);
    $hasSearchValue = isset($data->searchValue);
    
    if($isMedia)
    	$isMedia = ($isMedia)?1:0;
    else
    	$isMedia = 0;
    
    $searchValueQuery = "";
    if($hasSearchValue)
    	$searchValueQuery = " and Description LIKE '%".$data->searchValue."%' ";
    	
    $db = new DbHandler();

    $offset = ($pageIndex-1) * $pageSize;
    $total = 0;
    $resCount=null;

    if(!$hasType)
        $resCount = $db->makeQuery("SELECT count(*) as Total FROM `gallery` WHERE IsMedia=".$isMedia.$searchValueQuery);
    else{
		$query = "SELECT count(*) as Total FROM `gallery` 
		 Left Join file_type on file_type.ID=gallery.FileTypeID 
		 WHERE IsMedia=".$isMedia." AND file_type.Type in(".$data->fileTypes.")".$searchValueQuery;
		 
		 $resCount = $db->makeQuery($query);
	}
       

    while($res = $resCount->fetch_assoc()){
        $total = $res["Total"];
    }
    
    $r = null;
    if(!$hasType) 
        $r = $db->makeQuery("SELECT gallery.*,file_type.Type,file_type.NgClass FROM `gallery` LEFT JOIN file_type on file_type.ID = gallery.FileTypeID WHERE IsMedia=".$isMedia.$searchValueQuery." LIMIT $offset, $pageSize");
    else
        $r = $db->makeQuery("SELECT gallery.*,file_type.Type,file_type.NgClass FROM `gallery` LEFT JOIN file_type on file_type.ID = gallery.FileTypeID WHERE IsMedia=".$isMedia.$searchValueQuery." AND file_type.Type in(".$data->fileTypes.") LIMIT $offset, $pageSize");

    $result = array();
    while($res = $r->fetch_assoc()){
       $result[] = $res;
    }
    $dataRes = (object)[
            'Items' => $result,
            'PageIndex' => $pageIndex,
            'PageSize' => $pageSize,
            'Total' => $total
    ];

    echoResponse(200, $dataRes);
});

$app->post('/getAllSubjects', function() use ($app)  {
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    if(!isset($data->arrenged)){
	    $r = $db -> makeQuery("SELECT * FROM `subject` WHERE ParentID=-1");
	    
	    $result = array();
	    while($res = $r->fetch_assoc()){
	    
	        $childsQ = $db->makeQuery("SELECT * FROM subject where ParentID=".$res["ID"]);
	        $childs = array();
	        while($child = $childsQ->fetch_assoc()){
	            $childs[] = $child;
	        }
	        
	        $res["childs"] = $childs;
	        $result[] = $res;
	    }
	    echoResponse(200, $result);
	}else{
	    $r = $db -> makeQuery("SELECT * FROM `subject`");
	    $result = array();
	    while($res = $r->fetch_assoc()){
	        $result[] = $res;
	    }
	    echoResponse(200, $result);
	}
});

$app->post('/getAllFileTypes', function() use ($app)  {
    $db = new DbHandler();
    $r = $db -> makeQuery("SELECT * FROM `file_type`");
    
    $result = array();
    while($res = $r->fetch_assoc()){
        $result[] = $res;
    }
    echoResponse(200, $result);
});

$app->post('/getSubjectByID', function() use ($app)  {
    $db = new DbHandler();
    $data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT * FROM `subject` WHERE ID=".$data->CatID);
    
    while($res = $r->fetch_assoc()){
        echoResponse(200, $res);
        return ;
    }
    echoResponse(201, "No result found");
});

$app->post('/getSiteSettings', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT * FROM `global_settings` ORDER BY ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});

$app->post('/getAboutPage', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT * FROM `global_settings` LEFT JOIN page on page.ID=AboutPageID ORDER BY global_settings.ID DESC LIMIT 1");
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});

$app->post('/getHomePageData', function() use ($app)  {
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT * FROM `global_settings` LEFT JOIN page on page.ID=FooterPageID ORDER BY global_settings.ID DESC LIMIT 1");
    
    $res = [];
    $res['Footer'] = $r->fetch_assoc();
    $res['PostCount'] = 12;
    
    $r = $db -> makeQuery("SELECT * FROM `site_module` LEFT JOIN page on page.ID=site_module.PageID LEFT JOIN module_position on module_position.ID =site_module.ModulePositionID ORDER BY site_module.SortOrder ASC");
    
    $rightbarModules = [];
    $headerModules = [];
    $footerModules = [];
    while($ty = $r->fetch_assoc()){
    	switch($ty['Position']){
			case 'RightBar':
    			$rightbarModules[] = $ty;
				break;
			case 'Header':
    			$headerModules[] = $ty;
				break;
			case 'Footer':
    			$footerModules[] = $ty;
				break;
		}
	}
    $res['RightBarModules'] = $rightbarModules;
    $res['HeaderModules'] = $headerModules;
    $res['FooterModules'] = $footerModules;
    
    echoResponse(200, $res);
});


?>