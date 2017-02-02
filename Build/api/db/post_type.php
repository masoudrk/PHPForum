<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getAllPostTypes($db){

    $resQ = $db->makeQuery("SELECT post_type.* FROM post_type");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getAdminPostType($db,$ptID){

    $resQ = $db->makeQuery("SELECT post_type.* FROM admin_post left join 
                            post_type on admin_post.PostTypeID=post_type.ID
                            where admin_post.ID='$ptID'");
    return $r = $resQ->fetch_assoc();
}
?>