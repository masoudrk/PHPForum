<?php
    function getAllPositions($db){
        $resQ = $db->makeQuery("SELECT * FROM `organ_position` WHERE 1");

        $arr = [];
        while($r = $resQ->fetch_assoc())
            $arr[] = $r;
        return $arr;
    }
?>