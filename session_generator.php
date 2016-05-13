<?php
/**
 * Created by PhpStorm.
 * User: -MR-
 * Date: 13/05/2016
 * Time: 07:33 PM
 */

function generateSessionAsJavascriptVariable()
{
    if (!isset($_SESSION)) {
        session_start();
    }

    if(isset($_SESSION["UserID"])){
        echo "<script>".
            "var session = {};".
            "session.UserID = '".$_SESSION["UserID"]."';".
            "session.FullName = '".$_SESSION["FullName"]."';".
            "session.Email = '".$_SESSION["Email"]."';".
            "session.SSN = '".$_SESSION["SSN"]."';".
            "session.IsAdmin = '".$_SESSION["IsAdmin"]."';".
            "session.Image = '".$_SESSION["Image"]."';".
            "session.SignupDate = '".$_SESSION["SignupDate"]."';".
            "</script>";
    }else{
        echo "<script>".
            "var session = {};".
            "</script>";
    }
}
?>
