<?php

include 'auth.php';
date_default_timezone_set('Asia/Kolkata');


if (count($_POST) > 0) {
    if ($_POST['type'] == 'offer_upload') {
        $placement_id = $_POST['placement_id'];
        $filename = date('YmdHis');
        $filetype = explode('.', $_FILES['uploadedFile']['name']);
        $filetype = end($filetype);
        $filename .= "." . $filetype;
        $msg = '';
        if (move_uploaded_file($_FILES['uploadedFile']['tmp_name'], '../Offer_Letters/' . $filename)) {
            $link = $BASE_URL.'Offer_Letters/' . $filename;
        } else {
            $msg .= "Error While Uploading File : " . $_FILES['uploadedFile']['error'];
        }

        if ($msg != '') {
            echo json_encode(array("statusCode" => 400, "msg" => $msg));
        } else {
            try {
                $checkq=$conn->prepare("SELECT * FROM `offer_letters` WHERE placement_id=?");
                $checkq->execute(array($placement_id));
                if($checkq->rowCount()>0){
                    $upq=$conn->prepare("UPDATE `offer_letters` SET offer_letter=?,approved=? WHERE placement_id=?");
                    $upq->execute(array($link,-1,$placement_id));
                }else{
                    $insq=$conn->prepare("INSERT INTO `offer_letters`( `placement_id`, `offer_letter`, `approved`) VALUES (?,?,?)");
                    $insq->execute(array($placement_id,$link,-1));
                }
            } catch (PDOException $e) {
                $msg .= "Notification Not Added Due to the Following Error: \n" . "Error Code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
            }
            if ($msg != '') {
                echo json_encode(array("statusCode" => 400, "msg" => $msg));
            } else {
                echo json_encode(array('statusCode' => 200));
            }
        }
    }
}
