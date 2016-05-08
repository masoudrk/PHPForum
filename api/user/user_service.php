<?php
$app->post('/getAllPostsAdmin', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	$where = "";
	$hasWhere = FALSE;
	
    if(isset($data->searchValue)){
    	$hasWhere = TRUE;
		$where = " AND post.Title LIKE '%".$data->searchValue."%'";
	}
	if(isset($data->fromDate)){
    	$hasWhere = TRUE;
		$where = $where." AND post.WriteDate >= '".$data->fromDate."'";
	}
	if(isset($data->toDate)){
    	$hasWhere = TRUE;
		$where = $where." AND post.WriteDate <= '".$data->toDate."'";
	}
	
    $pageRes = null;
    
	if(!isset($data->searchTags)){
		$pageRes = $pr->getPage($db,'SELECT post.* , gallery.FullPath as Image FROM post LEFT JOIN gallery on post.ImageID = gallery.ID WHERE 1=1 '.$where.' ORDER BY post.ID DESC');
	}else{
		$pageRes = $pr->getPage($db,'SELECT DISTINCT post.* , gallery.FullPath as Image FROM subject JOIN post_subject on post_subject.SubjectID=subject.ID JOIN post on post.ID=post_subject.PostID LEFT JOIN gallery on post.ImageID = gallery.ID WHERE  subject.ID IN ('.$data->searchTags.')'.$where.' ORDER BY post.ID DESC');
	}
		
    foreach($pageRes['Items'] as &$res){
		
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

$app->post('/getPostByIDAdmin', function() use ($app)  {
    $data = json_decode($app->request->getBody());

    $db = new DbHandler();
	
    $r = $db->makeQuery("SELECT post.*, gallery.FullPath FROM `post` LEFT JOIN gallery on gallery.ID = post.ImageID where post.ID=".$data->PostID);

    $res = $r->fetch_assoc();
    if(!$res)
    {
    	echoResponse(201, "Error not found post!");
    	return;
	}
    
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
});

$app->post('/savePost', function() use ($app) {
	adminRequire();
	
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    
    $updateMode = isset($rObj->postID);

    $object = (object) [
        'Title' => $rObj->title,
        'Content' => $rObj -> postContent,
        'BriefContent' => $rObj -> postBrief,
        'TitleEN' => (isset($rObj->titleEN)?$rObj->titleEN:''),
        'ContentEN' => (isset($rObj->postContentEN)?$rObj->postContentEN:''),
        'BriefContentEN' => (isset($rObj->postBriefEN)?$rObj->postBriefEN:''),
        'ReleaseDate' => $rObj -> releaseDate,
        'WriteDate' => $rObj -> writeDate,
        'ImageID' => $rObj -> imageID,
        'Hidden' => (isset($rObj->hidden)?$rObj->hidden:'0') ,
        'EnableComment' => (isset($rObj->enableComment)?$rObj->enableComment:'0') ,
        'EnableEnglish' => (isset($rObj->enableEnglish)?$rObj->enableEnglish:'0') 
    ];

    try{

        $db->beginTransaction();

        $column_names = array( 'Title','Content','BriefContent','TitleEN','ContentEN','BriefContentEN','ReleaseDate','WriteDate','ImageID','Hidden','EnableComment','EnableEnglish');
        $result = null;

        if(!$updateMode){
            $result = $db->insertIntoTable($object, $column_names, "post");
        }else{
                                        
            $result = $db->updateRecord("post","`Title`='".$object->Title."' , `Content`='".$object->Content."' , `BriefContent`='".$object->BriefContent."' , `WriteDate`='".$object->WriteDate."' ,`ReleaseDate`='".$object->ReleaseDate."' , `ImageID`='".$object->ImageID."' , `Hidden`='".$object->Hidden."' , `EnableComment`='".$object->EnableComment."',TitleEN='".$object->TitleEN."',ContentEN='".$object->ContentEN."',BriefContentEN='".$object->BriefContentEN."',EnableEnglish='".$object->EnableEnglish."'"
                                        , "post.ID=".$rObj->postID );

            $resDelS = $db->deleteFromTable("post_subject","PostID=".$rObj->postID);
            $resDelA = $db->deleteFromTable("post_author","PostID=".$rObj->postID);
        }
    
        foreach ($rObj->subjects as $value) {
            $s = (object) [
            	'PostID' => ($updateMode)? $rObj->postID : $result,
                'SubjectID' => $value -> ID
            ];
            $db->insertIntoTable($s, array( 'PostID','SubjectID'), 'post_subject');
        }

        foreach ($rObj->authors as $value) {
            $a = (object) [
            	'PostID' => ($updateMode)? $rObj->postID : $result,
                'AdminID' => $value -> AdminID
            ];
            $db->insertIntoTable($a, array( 'PostID','AdminID'), 'post_author');
        }

        $db->commitTransaction();
    } catch (Exception $e) {
        $db->rollbackTransaction();
        echoResponse(201, $e);
        return;
    }
    $res = [];
    $res['Status'] = 'success';
    $res['PostID'] = $result;
    
    echoResponse(200, $res);
});

$app->post('/savePage', function() use ($app) {
	adminRequire();
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    
    $updateMode = isset($rObj->PageID);

    $result = null;
	$httpRes = [];
	$sess = $db->getSession();

    if(!$updateMode){
         $result = $db->insertToTable('page','Name,NameEN,HtmlContent,HtmlContentEN,AdminID'
         ,"'".$rObj->Name."','".$rObj->NameEN."','".$rObj->HtmlContent."','".$rObj->HtmlContentEN."','".$sess['AdminID']."'");
         if($result)
         {
			$httpRes['Status'] = "success";
    		echoResponse(200, $httpRes);
    		return;
		 }
    }else{
                                    
        $result = $db->updateRecord("page","`Name`='".$rObj->Name."' , `NameEN`='".$rObj->NameEN."' , `HtmlContent`='".$rObj->HtmlContent."' , `HtmlContentEN`='".$rObj->HtmlContentEN."',`AdminID`='".$sess['AdminID']."'" , "page.ID=".$rObj->PageID );
         if($result)
         {
			$httpRes['Status'] = "success";
    		echoResponse(200, $httpRes);
    		return;
		 }
    }
    
    $httpRes['Status'] = 'error';
    echoResponse(201, $httpRes);
});

$app->post('/getUserProfile', function() use ($app) {
	adminRequire();
	
    $db = new DbHandler();
    $user = $db->getSession();
    $r = $db->makeQuery("SELECT user.* , gallery.FullPath FROM user LEFT JOIN gallery on gallery.ID = user.AvatarID WHERE user.ID=".$user["UserID"]);
    
    $res = $r->fetch_assoc();
    echoResponse(200, $res);
});

$app->post('/saveSlide', function() use ($app) {
	adminRequire();
	
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    
    $data = (object)[
            'Link' => $rObj->Link,
            'ShowOrder' => $rObj->Order,
            'ImageID' => $rObj->ImageID,
            'Content' => $rObj->Content,
            'Title' => $rObj->Title
    ];
    
	$result = $db->insertIntoTable($data, array('Link','ShowOrder','ImageID','Title','Content'), 'slider');
	$response = [];
    if($result){
		$response["Status"] = "success";
    	echoResponse(200, $response);
	}else{
		$response["Status"] = "error";
    	echoResponse(201, $response);
	}
});

$app->post('/deletePost', function() use ($app) {
	adminRequire();
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    $result = $db->deleteFromTable('post','ID='.$rObj->PostID);
});

$app->post('/deletePage', function() use ($app) {
	adminRequire();
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    $result = $db->deleteFromTable('page','ID='.$rObj);
   	if($result){
		$httpRes = [];
		$httpRes['Status'] = "success";
		echoResponse(200, $httpRes);
		return;
	}
	echoResponse(201, $result);
});

$app->post('/deleteSlide', function() use ($app) {
	adminRequire();
    $response = array();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    $result = $db->deleteFromTable('slider','ID='.$rObj->slideID);
    $response = [];
    if($result){
		$response["Status"] = "success";
	}else{
		$response["Status"] = "error";
	}
    echoResponse(200, $response);
});

$app->post('/deleteMedia', function() use ($app)  {
	adminRequire();
    $db = new DbHandler();
    $data = json_decode($app->request->getBody());
    $r = $db->makeQuery("SELECT * FROM gallery WHERE ID=".$data->mediaID." LIMIT 1");

    while($res = $r->fetch_assoc()){
        $filename = "../../".$res["FullPath"];
        if (file_exists($filename)) {
            unlink($filename);
            $result = $db->deleteFromTable('gallery','ID='.$data->mediaID);
            echoResponse(200, 'File '.$filename.' has been deleted');
            return;
        } else {
            $result = $db->deleteFromTable('gallery','ID='.$data->mediaID);
            echoResponse(201, 'Could not delete '.$filename.'');
            return;
        }
    }
    echoResponse(201, 'Could not delete '.$filename.', file does not exist');
});

$app->post('/getAllAuthors', function() use ($app)  {
	adminRequire();
    $db = new DbHandler();
    $authorsQ = $db->makeQuery("SELECT admin.ID as AdminID , concat(FirstName , ' ' ,LastName) as FullName FROM admin 
                                    Left Join user on user.ID = admin.UserID");
    $authors = array();
    while($resAuthor = $authorsQ->fetch_assoc()){
        $authors[] = $resAuthor;
    }
    echoResponse(200, $authors);
});

$app->post('/getAllPrivileges', function() use ($app)  {
	adminRequire();
	
    $db = new DbHandler();
    $resQ = $db->makeQuery("SELECT ID as PrivilegeID , Privilege ,Description FROM admin_privilege");
    $result = array();
    while($res = $resQ->fetch_assoc()){
        $result[] = $res;
    }
    echoResponse(200, $result);
});

$app->post('/getAllPageTypes', function() use ($app)  {
	adminRequire();
	
    $db = new DbHandler();
    $resQ = $db->makeQuery("SELECT * FROM page_type");
    $result = array();
    while($res = $resQ->fetch_assoc()){
        $result[] = $res;
    }
    echoResponse(200, $result);
});

$app->post('/getAllSlides', function() use ($app)  {
	//adminRequire();
	
    $data = json_decode($app->request->getBody());
    $pageSize = 20;
    $pageIndex = 0;
    
    if(isset($data->pageSize) && isset($data->pageIndex)){
        $pageSize = $data->pageSize;
        $pageIndex = $data->pageIndex;
    }
    $hasCat = isset($data->catID);
    
    $db = new DbHandler();

    $offset = ($pageIndex-1) * $pageSize;
    if($offset < 0)
    	$offset = 0;
    $total = 0;
    $resCount=null;

    if(!$hasCat)
        $resCount = $db->makeQuery("SELECT count(*) as Total FROM slider");
    else
        $resCount = $db->makeQuery("SELECT DISTINCT count(*) as Total FROM `post` JOIN post_subject on post_subject.PostID=post.ID 
                                    WHERE post_subject.SubjectID=".$data->catID);

    while($res = $resCount->fetch_assoc()){
        $total = $res["Total"];
    }
    
    $r = null;
    if(!$hasCat)
        $r = $db->makeQuery("SELECT slider.* , gallery.FullPath FROM slider LEFT JOIN gallery on gallery.ID = slider.ImageID ORDER BY ShowOrder DESC  LIMIT $offset, $pageSize");
    else
        $r = $db->makeQuery("SELECT DISTINCT post.* , gallery.FullPath as Image FROM `post` JOIN post_subject on post_subject.PostID=post.ID LEFT JOIN gallery on post.ImageID = gallery.ID 
                             WHERE post_subject.SubjectID=".$data->catID." LIMIT $offset, $pageSize");
 
    $result = array();
    while($res = $r->fetch_assoc()){
        $result[] = $res;
    }
    $data = (object)[
            'Items' => $result,
            'PageIndex' => $pageIndex,
            'PageSize' => $pageSize,
            'Total' => $total
    ];

    echoResponse(200, $data);
});

$app->post('/getAllUsers', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	/*$where = (isset($data->searchValue))? "WHERE MATCH(`FirstName`, `FirstNameEN`, `LastName`, `LastNameEN`, `Username`, `Email`, `IP`) AGAINST('".$data->searchValue."' IN BOOLEAN MODE)" : "";*/
	$where = "";
	$hasWhere = FALSE;
	if(isset($data->searchValue) && strlen($data->searchValue) > 0){
		$s = mb_convert_encoding($data->searchValue, "UTF-8", "auto");
		$where = "WHERE (Username LIKE '%".$s."%' OR FirstName LIKE '%".$s."%' OR Email LIKE '%".$s."%')";
		$hasWhere = TRUE;
	}
	if(isset($data->PrivilegeID)){
		if($hasWhere)
			$where = $where." AND admin_privilege.ID='".$data->PrivilegeID."'";
		else
			$where = "WHERE admin_privilege.ID='".$data->PrivilegeID."'";
	}
	
	$pageRes = $pr->getPage($db,"SELECT user.`ID`, `FirstName`, `FirstNameEN`, `LastName`, `LastNameEN`, `Username`, `Email`, `IP` ,admin_privilege.Privilege,admin_privilege.ID as PrivilegeID ,admin.ID as AdminID FROM user LEFT JOIN admin on admin.UserID = user.ID LEFT JOIN admin_privilege on admin_privilege.ID = admin.PrivilegeID ".$where." ORDER BY user.ID ASC");
	
    foreach($pageRes['Items'] as &$res){
    	if(isset($res["PrivilegeID"])){
			$rpQ = $db->makeQuery("SELECT * FROM admin_privilege WHERE ID=".$res["PrivilegeID"]);
			$rp = $rpQ->fetch_assoc();
			$pr = array();
			if(isset($rp["ID"])){
				$pr["ID"] = $rp["ID"];
				$pr["Privilege"] = $rp["Privilege"];
				$pr["Description"] = $rp["Description"];
			}else{
				$pr["ID"] = null;
				$pr["Privilege"] =  null;
				$pr["Description"] =  null;
			}
			$res["Privilege"] = $pr;
		}
    }
	
    echoResponse(200, $pageRes);
});

$app->post('/getAllPages', function() use ($app)  {
	adminRequire();
	
	$data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	$query = "SELECT page.ID, page.Name ,page.IsStatic , TypeNameEN, TypeName , user.LastName ,user.FirstName FROM page LEFT JOIN page_type on page_type.ID = page.PageTypeID LEFT JOIN admin on admin.ID=page.AdminID LEFT JOIN user on user.ID = admin.UserID 
	WHERE IsStatic=0 ORDER BY page.ID DESC";
	
	$pageRes = $pr->getPage($db,$query);
    echoResponse(200, $pageRes);
});

$app->post('/getAllPageNames', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody());
    $pr = new Pagination($data);
    
    $db = new DbHandler();
    $pageRes = $db->makeQuery("SELECT page.ID , page.Name , page.NameEN FROM page");
    $res = [];
	while($r = $pageRes->fetch_assoc()){
		$res[] = $r;
	}

    echoResponse(200, $res);
});

$app->post('/getAllPositions', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody());
    $pr = new Pagination($data);
    
    $db = new DbHandler();
    $pageRes = $db->makeQuery("SELECT * FROM module_position");
    $res = [];
	while($r = $pageRes->fetch_assoc()){
		$res[] = $r;
	}

    echoResponse(200, $res);
});

$app->post('/getPageByID', function() use ($app)  {
	adminRequire();
    $data = json_decode($app->request->getBody());
    
    $db = new DbHandler();
    $page = $db->getOneRecord("SELECT page.* , TypeNameEN, TypeName , user.LastName ,user.FirstName FROM page LEFT JOIN page_type on page_type.ID = page.PageTypeID LEFT JOIN admin on admin.ID=page.AdminID LEFT JOIN user on user.ID = admin.UserID WHERE page.ID='".$data->PageID."'");

    echoResponse(200, $page);
});

$app->post('/saveAdminPrivilege', function() use ($app)  {
	adminRequire();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    $response = [];
    
    $sess = $db->getSession();
    if(isset($sess["AdminID"])){
		$resM = $db->makeQuery("SELECT * FROM admin LEFT JOIN admin_privilege on admin_privilege.ID = admin.PrivilegeID WHERE admin.ID=".$sess["AdminID"]);
		$admin = $resM->fetch_assoc();
		if($admin["Privilege"] == "Manager"){
			if(isset($rObj->AdminID)){
				if($rObj->PrivilegeID!=-1){
					$db->updateRecord('admin','PrivilegeID='.$rObj->PrivilegeID,'ID='.$rObj->AdminID);
				}else{
					$db->deleteFromTable('admin','ID='.$rObj->AdminID);
				}
			}else if($rObj->PrivilegeID!=-1){
				$adminObj = array();
				$adminObj["UserID"] = $rObj->UserID;
				$adminObj["PrivilegeID"] = $rObj->PrivilegeID;
				$db->insertIntoTable($adminObj,array('UserID','PrivilegeID'),'admin');
			}
			$response["Status"] = "success";
	   		echoResponse(200, $response);
		}else{
			echoError("NoPrivilege");
		}
	}else{
		echoError("IsNoAdmin");
	}
});

$app->post('/deleteUser', function() use ($app) {
    
	adminRequire();
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    $response = [];
    
    $sess = $db->getSession();
    if(isset($sess["AdminID"])){
		$resM = $db->makeQuery("SELECT * FROM admin LEFT JOIN admin_privilege on admin_privilege.ID = admin.PrivilegeID WHERE admin.ID=".$sess["AdminID"]);
		$admin = $resM->fetch_assoc();
		if($admin["Privilege"] == "Manager"){
		    $result = $db->deleteFromTable('admin','UserID='.$rObj->UserID);
		    $result = $db->deleteFromTable('user','ID='.$rObj->UserID);
			$response["Status"] = "success";
	   		echoResponse(200, $response);
		}else{
			echoError("NoPrivilege");
		}
	}else{
		echoError("IsNoAdmin");
	}
});

$app->post('/changeUserPassword', function() use ($app) {
	
    $rObj = json_decode($app->request->getBody());
    $db = new DbHandler();
    
    if(strlen ($rObj->newPassword) < 6){
		echoError("PasswordIsShort");
		return;
	}
    
    $sess = $db->getSession();
    if(isset($sess["UserID"])){
		$resM = $db->makeQuery("SELECT * FROM user WHERE user.ID=".$sess["UserID"]);
		$user = $resM->fetch_assoc();
		if(passwordHash::check_password($user['Password'],$rObj->oldPassword)){
			$newPass = passwordHash::hash($rObj->newPassword);
			$res = $db->updateRecord('user',"Password='".$newPass."'","ID='".$user['ID']."'");
			if(!$res){
				echoError("CannotUpdateData");
			}else{
    			$response = [];
				$response["Status"] = "success";
		   		echoResponse(200, $response);
		   		$db->destroySession();
			}
		}else{
		   	echoError("PasswordNotMatch");
		}
	}else{
		echoError("IsNoUser");
	}
});

$app->post('/changeUserAvatar', function() use ($app) {
    
    $filename = $_FILES['file']['name'];
    
	$rand = generateRandomString(18);
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	
    $destination = '../../content/img/'.$rand.".".$ext;
    move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );
    
    $db = new DbHandler();
    $column_names = array( 'FileTypeID','Path','FullPath','UserID','IsMedia');
    $object =(object)[
        "FileTypeID" => '1',
        "Path" => "content/img/",
        "Description" => "",
        "FullPath" => "content/img/".$rand.".".$ext,
        "UserID" => $sess["UserID"],
        "IsMedia"=>"0"
    ];
    $result = $db->insertIntoTable($object, $column_names, "gallery");

    $sess = $db->getSession();
    if(isset($sess["UserID"])){
		$res = $db->updateRecord('user',"AvatarID='".$result."'","ID='".$sess["UserID"]."'");
		if($res){
			$response["Status"] = "success";
	    	echoResponse(200, $response);
		}
		else
			echoError("ErrorUpdate");
	}else{
		echoError("IsNoUser");
	}
});

$app->post('/saveSubject', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody(),true);
    $db = new DbHandler();
    
	$obj = array();
	$obj["Title"] = $data["Title"];
	$obj["TitleEN"] = $data["TitleEN"];
		
	if(isset($data["ParentID"])){
		$obj["ParentID"] = $data["ParentID"];
	}else{
		$obj["ParentID"] = -1;
	}
	
	if(!isset($data["ID"]))
		$result = $db->insertIntoTable($obj, array('ParentID','Title','TitleEN'), 'subject');
	else
		$result = $db->updateRecord('subject', "Title='".$obj["Title"]."',TitleEN='".$obj["TitleEN"].
		"'","ID='".$data["ID"]."'");
	
	$response = array();
	if($result){
		$response["Status"] = "success";
    	echoResponse(200, $response);
	}else{
		$response["Status"] = "error";
    	echoResponse(201, $response);
	}
});

$app->post('/saveSiteSettings', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $res = $db->insertToTable('global_settings',"AboutPageID,FooterPageID,SiteName,SiteNameEN,SiteTitleIcon",
                                "'".$data->AboutPageID."','".$data->FooterPageID."','"
                                .((isset($data->SiteName))?$data->SiteName:'')."','".((isset($data->SiteNameEN))?$data->SiteNameEN:'')
                                ."','".((isset($data->SiteTitleIcon))?$data->SiteTitleIcon:'')."'");
    $db->deleteFromTable('site_module','1=1');
	foreach($data->Modules as &$d){
		$db->insertToTable('site_module','ModulePositionID,PageID,SortOrder',"'".$d->ModulePositionID."','".$d->PageID."','".$d->SortOrder."'");
	}
    
    if(!$res){
    	echoError('Error on inserting!');
    	return;
	}
	$response = array();
	$response["Status"] = "success";
	echoResponse(200, $response);
});

$app->post('/updateSubject', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody(),true);
    $db = new DbHandler();
    
	$result = $db->updateRecord('subject', "Title='".$data["Title"]."'",'ID='.$data["ID"]);
	
	$response = array();
	if($result){ 
		$response["Status"] = "success";
    	echoResponse(200, $response);
	}else{
		$response["Status"] = "error";
    	echoResponse(201, $response);
	}
});

$app->post('/deleteSubject', function() use ($app)  {
	adminRequire();
	
    $data = json_decode($app->request->getBody(),true);
    $db = new DbHandler();
    
	$result = $db->deleteFromTable('subject','ID='.$data["ID"]);
	
	$response = array();
	if($result){
		$response["Status"] = "success";
    	echoResponse(200, $response);
	}else{
		$response["Status"] = "error";
    	echoResponse(201, $response);
	}
});

$app->post('/getAllModulesSorted', function() use ($app)  {
	adminRequire();
	
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $r = $db -> makeQuery("SELECT site_module.*,page.ID as PageID,page.HtmlContent,module_position.Position FROM `site_module` LEFT JOIN module_position on module_position.ID=site_module.ModulePositionID LEFT JOIN page on page.ID = site_module.PageID ORDER BY SortOrder ASC");
    
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
    $res = [];
    $res['RightBarModules'] = $rightbarModules;
    $res['HeaderModules'] = $headerModules;
    $res['FooterModules'] = $footerModules;
    
    echoResponse(200, $res);
});

$app->post('/getAdminHeaderData', function() use ($app)  {
	adminRequire();
    $db = new DbHandler();
    //$data = json_decode($app->request->getBody());
    $sess = $db->getSession();
    $AdminID = $sess['AdminID'];
    $r = $db -> makeQuery("SELECT comment.* , post.Title,gallery.FullPath , concat(user.FirstName,'  ',user.LastName) as FullName FROM comment_read LEFT JOIN comment on comment.ID=comment_read.CommentID LEFT JOIN user on user.ID=comment.UserID LEFT JOIN post on post.ID=comment.PostID LEFT JOIN gallery on gallery.ID=user.AvatarID WHERE comment_read.AdminID='".$AdminID."' ORDER BY Date DESC");
    
    $res = [];
    while($ty = $r->fetch_assoc()){
    	$res[] = $ty;
	}
    
    echoResponse(200, $res);
});

$app->post('/getAllCommentsForManage', function() use ($app)  {
	adminRequire();
	$data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	$query = "SELECT comment.*, post.Title , concat(user.LastName ,' ', user.FirstName) as FullName, concat(up.LastName ,' ', up.FirstName) as AnswerToFullName ,cp.Identity AS AnswerToIdentity ,up.ID as AnswerToUserID FROM comment LEFT JOIN user on user.ID=comment.UserID LEFT JOIN post on post.ID=comment.PostID LEFT JOIN comment AS cp on comment.ParentID=cp.ID LEFT JOIN user AS up on up.ID=cp.UserID ORDER BY comment.Date Desc";
	
	$pageRes = $pr->getPage($db,$query);
	/*
    foreach($pageRes['Items'] as &$c){
    	$cq = $db->makeQuery($query.' WHERE Accepted=1 AND comment.ParentID='.$c['ID'].' ORDER BY ID ASC');
    	$c['Childs'] = [];
    	while($cc = $cq->fetch_assoc()){
			$c['Childs'][] = $cc;
		}
    }
*/
    echoResponse(200, $pageRes);
});

$app->post('/markAsReadComments', function() use ($app)  {
	adminRequire();
	
    $db = new DbHandler();
    $sess = $db->getSession();
    $AdminID = $sess['AdminID'];
    $r = $db -> makeQuery("DELETE FROM comment_read where AdminID='".$AdminID."'");
    
    echoResponse(200, $r);
});

$app->post('/deleteContact', function() use ($app)  {
	adminRequire();
	$data = json_decode($app->request->getBody());
	
    $db = new DbHandler();
    $r = $db -> makeQuery("DELETE FROM contact_us where ID='".$data->ID."'");
    
    $response =[];
    if($r)
    {
		$response["Status"] = "success";
    	echoResponse(200, $response);
    	return;
	}
	$response["Status"] = "error";
	echoResponse(201, $response);
});

$app->post('/deleteComment', function() use ($app)  {
	adminRequire();
	$data = json_decode($app->request->getBody());
	
    $db = new DbHandler();
    $r = $db -> makeQuery("DELETE FROM comment where ID='".$data->ID."'");
    
    $response =[];
    if($r)
    {
		$response["Status"] = "success";
    	echoResponse(200, $response);
    	return;
	}
	$response["Status"] = "error";
	echoResponse(201, $response);
});

$app->post('/acceptComment', function() use ($app)  {
	adminRequire();
	$data = json_decode($app->request->getBody());
	
    $db = new DbHandler();
    $acc = ($data->Accepted)?'1':'0';
    $r = $db -> updateRecord('comment',"Accepted=".$acc,"ID=".$data->CommentID);
    
	$response = array();
	if($r){ 
		$response["Status"] = "success";
    	echoResponse(200, $response);
	}else{
		$response["Status"] = "error";
    	echoResponse(201, $response);
	}
});
 
$app->post('/getAllContactsUs', function() use ($app)  {
	adminRequire();
	$data = json_decode($app->request->getBody());
    $db = new DbHandler();
    $pr = new Pagination($data);
	
	$query = "SELECT contact_us.* , concat(FirstName,' ', LastName) as FullName FROM contact_us LEFT JOIN user on user.ID=contact_us.UserID ORDER BY contact_us.Date Desc";
	
	$pageRes = $pr->getPage($db,$query);
    echoResponse(200, $pageRes);
});
?>