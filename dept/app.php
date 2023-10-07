<?php
include 'auth.php';

if(count($_POST)>0){
    if($_POST['type']=='approve'){
        $q=$conn->prepare("UPDATE `offer_letters` SET `approved`='1' WHERE `placement_id`=?");
        $place_q=$conn->prepare("UPDATE `placements` SET `acceptance`='1' WHERE `id`=?");
        try{
            $q->execute(array($_POST['placement_id']));
            $place_q->execute(array($_POST['placement_id']));
            echo json_encode(array("statusCode"=>200));
        }catch(PDOException $e){
            $msg= "The Following error Occured : \n Error Code: ".$e->getCode()."\n Error Message: ".$e->getMessage();
            echo json_encode(array("statusCode"=>400,"msg"=>$msg));
        }
    }
    else if($_POST['type']=='reject'){
        $q=$conn->prepare("UPDATE `offer_letters` SET `approved`='0' WHERE `placement_id`=?");
        $place_q=$conn->prepare("UPDATE `placements` SET `acceptance`='0' WHERE `id`=?");
        try{
            $q->execute(array($_POST['placement_id']));
            $place_q->execute(array($_POST['placement_id']));
            echo json_encode(array("statusCode"=>200));
        }catch(PDOException $e){
            $msg= "The Following error Occured : \n Error Code: ".$e->getCode()."\n Error Message: ".$e->getMessage();
            echo json_encode(array("statusCode"=>400,"msg"=>$msg));
        }
    }
}

?>