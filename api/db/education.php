<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 10/05/2016
 * Time: 12:06 PM
 */

function getAllEducations($db){

    $resEducationQ = $db->makeQuery("SELECT education.* FROM education");

    $edus = [];
    while($r = $resEducationQ->fetch_assoc())
        $edus[] = $r;

    return $edus;
}

function getUserEducations($db ,$userID){

    $resEducationQ = $db->makeQuery("SELECT education.* FROM user_education LEFT JOIN user ON user_education.UserID = user.ID LEFT JOIN education ON 
education.ID = 
user_education.EducationID WHERE user.ID = '$userID'");

    $edus = [];
    while($r = $resEducationQ->fetch_assoc())
        $edus[] = $r;

    return $edus;
}

?>