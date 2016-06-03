<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getPageUserMessages($db ,$uID,$pin){
    $pr = new Pagination(null);
    $pr->setParams($pin);
    $sess = new Session();

    $res= $pr->getPage($db,
"SELECT message.* , user.ID as AdminUserID,user.FullName , file_storage.FullPath as Image
FROM message 
LEFT JOIN admin on admin.ID=message.AdminID 
LEFT JOIN user on user.ID=admin.UserID 
LEFT JOIN file_storage on file_storage.ID=user.AvatarID 
WHERE message.UserID = '$sess->UserID' ORDER BY message.MessageDate ASC");
    return $res;
}
?>