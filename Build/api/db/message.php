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
"SELECT message.* ,user.FullName , file_storage.FullPath as Image
FROM message 
LEFT JOIN user on user.ID=message.SenderUserID 
LEFT JOIN file_storage on file_storage.ID=user.AvatarID 
WHERE message.UserID = '$sess->UserID' ORDER BY message.MessageDate desc");
    return $res;
}

function getUserUnreadMessages($db ,$uID,$limit){

    $res= $db->makeQuery(
        "SELECT message.* ,user.FullName , file_storage.FullPath as Image
FROM message 
LEFT JOIN user on user.ID=message.SenderUserID 
LEFT JOIN file_storage on file_storage.ID=user.AvatarID 
WHERE message.UserID = '$uID' 
and message.MessageViewed=0
ORDER BY message.MessageDate desc limit $limit");

    $resp = [];
    while($r = $res->fetch_assoc()){
        $resp[] = $r;}
    return $resp;
}

function getUserMessageByID($db ,$uID ,$mID){

    $res= $db->makeQuery(
        "SELECT message.* ,user.FullName , file_storage.FullPath as Image
FROM message 
LEFT JOIN user on user.ID=message.SenderUserID 
LEFT JOIN file_storage on file_storage.ID=user.AvatarID 
WHERE message.UserID = '$uID' 
and message.ID='$mID'
ORDER BY message.MessageDate desc");

    return $res->fetch_assoc();
}

function getUserUnreadMessagesCount($db ,$uID){

    $res= $db->makeQuery(
        "SELECT count(*) as Total
FROM message 
WHERE message.UserID = '$uID'
and message.MessageViewed=0");

    return $res->fetch_assoc()['Total'];
}
?>