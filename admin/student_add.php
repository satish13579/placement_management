<?php
include 'auth.php';



if (count($_POST) > 0) {
    if ($_POST['type'] == 'student_single_add') {
        $roll_no = $_POST['roll_no'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $dept_id = $_POST['dept_id'];
        $passout_year = $_POST['passout_year'];
        $sql = $conn->prepare("SELECT * FROM students WHERE roll_no=?");
        $res = $sql->execute(array($roll_no));
        if ($sql->rowCount() > 0) {
            echo json_encode(array("statusCode" => 400, "err" => "Student already Exists.!!"));
        } else {
            try {
                $insert = $conn->prepare("INSERT INTO `students` (`roll_no`,`first_name`,`last_name`,`dob`,`email`,`passout_year`,`dept_id`) 
            VALUES (?,?,?,?,?,?,?)");
                $res = $insert->execute(array($roll_no, $first_name, $last_name, $dob, $email, $passout_year, $dept_id));
                echo json_encode(array("statusCode" => 200));
            } catch (Exception $e) {
                $msg = "Error code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
                echo json_encode(array("statusCode" => 400, "msg" => $msg));
            }
        }
    } else if ($_POST['type'] == 'student_reset_generate') {
        $roll_no = $_POST['roll_no'];
        $emailr = $conn->prepare("SELECT * FROM `students` WHERE `roll_no`=?");
        $emailr->execute(array($roll_no));
        if ($emailr->rowCount() == 1) {
            $email = $emailarr[0]['email'];
            if ($email == '') {
                $msg = "There is a Empty/NULL Email Feild For The Employee ID : " . $emp_id . " in The Server.<br>Go To Any Department Exam Incharge For Updating Your Email.";
                echo json_encode(array("statusCode" => 999, "msg" => $msg));
            } else {
                $errmsg = '';
                $salt = randomSalt(32);
                date_default_timezone_set('Asia/Kolkata');
                $cur = date('Y-m-d H:i:s');
                $insertr = mysqli_query($conn, "INSERT INTO `reset_password`(`emp_id`,`salt`, `flag`, `date`) VALUES ('$emp_id','$salt','0','$cur')");
                try {
                    sendSalt($email, $salt, $emp_id);
                } catch (Exception $e) {
                    $errmsg = "The Following Error Occured While Sending Mail To " . $email . " :\nError Code : " . $e->getCode() . "\nError Message : " . $e->getMessage();
                }
                if ($errmsg == '') {
                    echo json_encode(array("statusCode" => 200, "email" => $email));
                } else {
                    echo json_encode(array("statusCode" => 999, "msg" => $errmsg));
                }
            }
        } else {
            $msg = "There is No Data Available For The Employee ID : " . $emp_id . ".<br>Go To Any Department Exam Incharge For Your Employee Signup.";
            echo json_encode(array("statusCode" => 999, "msg" => $msg));
        }
    }
}
