<?php

$app->post('/session', function() use ($app)  {
    $sess = new Session();
    $s = $sess->getSession();
    echoResponse(200, $s);
});

$app->post('/getUserProfile', function() use ($app)  {
    //adminRequire();
    $db = new DbHandler();
    $session = new Session();

    $resQ = $db->makeQuery("SELECT user.`ID`, `FullName`, `Email`, `Username`, `PhoneNumber`, `Tel`, `SignupDate`, `Gender` , FullPath as 
AvatarImagePath FROM user LEFT JOIN file_storage on file_storage.ID = AvatarID WHERE user.ID = 1");

    echoResponse(200, $resQ->fetch_assoc());
});

$app->post('/sfasf', function() use ($app)  {
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

?>