<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getAllSkills($db){

    $resQ = $db->makeQuery("SELECT skill.* FROM skill");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

function getUserSkills($db ,$userID){

    $resQ = $db->makeQuery("SELECT skill.* FROM user_skill LEFT JOIN user ON user_skill.UserID = user.ID LEFT JOIN skill ON 
skill.ID = user_skill.SkillID WHERE user.ID = '$userID'");

    $arr = [];
    while($r = $resQ->fetch_assoc())
        $arr[] = $r;

    return $arr;
}

?>