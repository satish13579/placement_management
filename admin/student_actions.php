<?php
include 'auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
function randomSalt($len = 8)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $l = strlen($chars) - 1;
    $str = '';
    for ($i = 0; $i < $len; ++$i) {
        $str .= $chars[rand(0, $l)];
    }
    return $str;
}

function sendSalt($email, $salt, $roll_no)
{
    global $BASE_URL;
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'mail.newtutor.in';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@newtutor.in';
    $mail->Password = 'newtutor@123';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('noreply@newtutor.in', 'No Reply');

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = "Account Password Reset Link";
    $link = $BASE_URL . 'reset_password.php?salt=' . $salt;
    $mail->Body = reset_template($link, $roll_no);

    $mail->send();
}

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
                $salt = randomSalt(32);
                date_default_timezone_set('Asia/Kolkata');
                $cur = date('Y-m-d H:i:s');
                sendSalt($email, $salt, $roll_no);
                $insq = $conn->prepare("INSERT INTO `reset_password`(`role`, `user_id`, `salt`, `flag`, `date`)
        VALUES (?,?,?,?,?)");
                $insq->execute(array('students', $roll_no, $salt, 0, $cur));
                echo json_encode(array("statusCode" => 200));
            } catch (Exception $e) {
                $msg = "Error code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
                echo json_encode(array("statusCode" => 400, "err" => $msg));
            }
        }
    } else if ($_POST['type'] == 'student_reset_generate') {
        $roll_no = $_POST['roll_no'];
        $emailr = $conn->prepare("SELECT * FROM `students` WHERE `roll_no`=?");
        $emailr->execute(array($roll_no));
        if ($emailr->rowCount() == 1) {
            $student = $emailr->fetch();
            $email = $student['email'];
            if ($email == '' || $email == null) {
                $msg = "No email available to send reset password link, please update email for " . $roll_no . " Roll before requesting reset password.";
                echo json_encode(array("statusCode" => 400, "msg" => $msg));
            } else {
                $errmsg = '';
                $salt = randomSalt(32);
                date_default_timezone_set('Asia/Kolkata');
                $cur = date('Y-m-d H:i:s');
                try {
                    sendSalt($email, $salt, $roll_no);
                } catch (Exception $e) {
                    $errmsg = "The Following Error Occured While Sending Mail To " . $email . " :\nError Code : " . $e->getCode() . "\nError Message : " . $e->getMessage();
                }

                if ($errmsg == '') {
                    try {
                        $insq = $conn->prepare("INSERT INTO `reset_password`(`role`, `user_id`, `salt`, `flag`, `date`)
                         VALUES (?,?,?,?,?)");
                        $insq->execute(array('students', $roll_no, $salt, 0, $cur));
                    } catch (Exception $e) {
                        $errmsg = "Error code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
                    }
                    if ($errmsg == '') {
                        echo json_encode(array("statusCode" => 200, "email" => $email));
                    } else {
                        echo json_encode(array("statusCode" => 400, "msg" => $errmsg));
                    }
                } else {
                    echo json_encode(array("statusCode" => 400, "msg" => $errmsg));
                }
            }
        } else {
            echo json_encode(array("statusCode" => 400, "msg" => "No student available with " . $roll_no . " roll no."));
        }
    } else if ($_POST['type'] == 'student_single_edit') {
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
            try {
                $insert = $conn->prepare("UPDATE `students` SET `first_name`=?,`last_name`=?,`dob`=?,`email`=?,`dept_id`=?,`passout_year`=? WHERE roll_no=?");
                $res = $insert->execute(array($first_name, $last_name, $dob, $email, $dept_id, $passout_year, $roll_no));
                echo json_encode(array("statusCode" => 200));
            } catch (Exception $e) {
                $msg = "Error code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
                echo json_encode(array("statusCode" => 400, "err" => $msg));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "err" => "Student Doesn't Exists.!!"));
        }
    } else if ($_POST['type'] == 'student_single_delete') {
        $roll_no = $_POST['roll_no'];
        $sql = $conn->prepare("SELECT * FROM students WHERE roll_no=?");
        $res = $sql->execute(array($roll_no));
        if ($sql->rowCount() > 0) {
            try {
                $insert = $conn->prepare("DELETE FROM `students` WHERE roll_no=?");
                $res = $insert->execute(array($roll_no));
                echo json_encode(array("statusCode" => 200));
            } catch (Exception $e) {
                $msg = "Error code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
                echo json_encode(array("statusCode" => 400, "err" => $msg));
            }
        } else {
            echo json_encode(array("statusCode" => 400, "err" => "Student Doesn't Exists.!!"));
        }
    } else if ($_POST['type'] == 'student_multiple_delete') {
        $roll_nos = $_POST['roll_nos'];
        $err = '';
        for ($i = 0; $i < count($roll_nos); $i++) {
            $id = $roll_nos[$i];
            try {
                $sql = $conn->prepare("DELETE FROM `students` WHERE roll_no=?");
                $res = $sql->execute(array($id));
            } catch (Exception $e) {
                $err .= "Error For $id\nError code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
            }
        }
        if ($err == '') {
            echo json_encode(array("statusCode" => 200));
        } else {
            echo json_encode(array("statusCode" => 400, "err" => $err));
        }
    } else if ($_POST['type'] == 'placement_upload'){
        $company= $_POST['company'];
        $package=$_POST['package'];
        $job_role=$_POST['job_role'];
        $job_status = $_POST['job_status'];
        $roll_nos = $_POST['roll_nos'];
        $date = date('Y-m-d');
        $err = '';
        for ($i = 0; $i < count($roll_nos); $i++) {
            $id = $roll_nos[$i];
            try {
                $sql = $conn->prepare("INSERT INTO `placements`(`roll_no`, `company`, `package`, `date`, `job_role`, `job_status`, `acceptance`,`college_id`)
                 VALUES (?,?,?,?,?,?,?,?)");
                $res = $sql->execute(array($id,$company,$package,$date,$job_role,$job_status,0,$_SESSION['id']));
            } catch (PDOException $e) {
                $err .= "Error For $id roll no\nError code: " . $e->getCode() . "\nError Message: " . $e->getMessage();
            }
        }
        if ($err == '') {
            echo json_encode(array("statusCode" => 200));
        } else {
            echo json_encode(array("statusCode" => 400, "err" => $err));
        }
    }
}


function reset_template($link, $roll_no)
{
    return "<!DOCTYPE html>
	<html>
	
	<head>
		<title></title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge' />
		<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'/>
		<style type='text/css'>
			@media screen {
				@font-face {
					font-family: Lato;
					font-style: normal;
					font-weight: 400;
					src: local(LatoRegular'), local(LatoRegular'), url(https://fonts.gstatic.com/slatov11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
				}
	
				@font-face {
					font-family: Lato;
					font-style: normal;
					font-weight: 700;
					src: local(LatoBold'), local(LatoBold'), url(https://fonts.gstatic.com/slatov11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
				}
	
				@font-face {
					font-family: Lato;
					font-style: italic;
					font-weight: 400;
					src: local(LatoItalic'), local(LatoItalic'), url(https://fonts.gstatic.com/slatov11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
				}
	
				@font-face {
					font-family: Lato;
					font-style: italic;
					font-weight: 700;
					src: local(LatoBold Italic'), local(LatoBoldItalic'), url(https://fonts.gstatic.com/slatov11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
				}
			}
	
			/* CLIENT-SPECIFIC STYLES */
			body,
			table,
			td,
			a {
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			}
	
			table,
			td {
				mso-table-lspace: 0pt;
				mso-table-rspace: 0pt;
			}
	
			img {
				-ms-interpolation-mode: bicubic;
			}
	
			/* RESET STYLES */
			img {
				border: 0;
				height: auto;
				line-height: 100%;
				outline: none;
				text-decoration: none;
			}
	
			table {
				border-collapse: collapse !important;
			}
	
			body {
				height: 100% !important;
				margin: 0 !important;
				padding: 0 !important;
				width: 100% !important;
			}
	
			/* iOS BLUE LINKS */
			a[x-apple-data-detectors] {
				color: inherit !important;
				text-decoration: none !important;
				font-size: inherit !important;
				font-family: inherit !important;
				font-weight: inherit !important;
				line-height: inherit !important;
			}
	
			/* MOBILE STYLES */
			@media screen and (max-width:600px) {
				h1 {
					font-size: 32px !important;
					line-height: 32px !important;
				}
			}
	
			/* ANDROID CENTER FIX */
			div[style*='margin: 16px 0;'] {
				margin: 0 !important;
			}
		</style>
	</head>
	
	<body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'>
		<!-- HIDDEN PREHEADER TEXT -->
		<div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Lato, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'>Click The Given Link to Reset Your Password For $roll_no username.!
		</div>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<!-- LOGO -->
			<tr>
				<td bgcolor='#003366' align='center'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
						<tr>
							<td align='center' valign='top' style='padding: 40px 10px 40px 10px;'> </td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td bgcolor='#003366' align='center' style='padding: 0px 10px 0px 10px;'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
						<tr>
							<td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'>
								<h1 style='font-size: 48px; font-weight: 400; margin: 2;'>RESET PASSWORD!</h1> <img src='https://img.icons8.com/clouds/100/null/available-updates.png' width='125' height='120' style='display: block; border: 0px;' />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
						<tr>
							<td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
								<p style='margin: 0;'>As We Recieved Reset Password Request For The Rollno <span style='background-color:#808080;color:#ffffff;padding:4px 6px 4px 6px;border-radius:4px;'>$roll_no</span> , We've Sent The Reset Password Link. Just Click The Below Button To Reset Your Password.</p>
							</td>
						</tr>
						<tr>
							<td bgcolor='#ffffff' align='left'>
								<table width='100%' border='0' cellspacing='0' cellpadding='0'>
									<tr>
										<td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'>
											<table border='0' cellspacing='0' cellpadding='0'>
												<tr>
													<td align='center' style='border-radius: 3px;' bgcolor='#003366'><a href='$link' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 8px; border: 1px solid #003366; display: inline-block;'>Reset Password</a></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr> <!-- COPY -->
                        <tr>
                        <td bgcolor='#ffffff' align='left' style='padding: 0px 30px 0px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                            <p style=margin: 0;'>Link will Be Active For 24 Hrs Only.!!</p>
                        </td>
                    </tr>
                        <tr>
                        <td bgcolor='#ffffff' align='left' style='padding: 0px 30px 0px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                            <p style='margin: 0;'>If that doesn't work, copy and paste the following link in your browser:</p>
                        </td>
                    </tr> <!-- COPY -->
                    <tr>
                        <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 20px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                            <p style='margin: 0;'><a href='$link' target='_blank' style='color: #FFA73B;'>$link</a></p>
                        </td>
                    </tr>
                    <tr>
						<tr>
							<td bgcolor='#ffffff' align='left' style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
								<p style='margin: 0;'>Regards,<br>ByteBandits.</p>
							</td>
						</tr>
					</table>
                    
				</td>
			</tr>
			<tr>
				<td bgcolor='#f4f4f4' align='center' style='padding: 30px 10px 0px 10px;'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
						<tr>
							<td bgcolor='#003366' align='center' style='padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
								<h2 style='font-size: 20px; font-weight: 400; color: #FFCC99; margin: 0;'>Need more help?</h2>
								<p style='margin: 0;'><a href='mailto:help@newtutor.in?cc=help@newtutor.in&bcc=help@newtutor.in' target='_blank' style='color: #FFCC99;'>We&rsquo;re here to help you out <img src='https://img.icons8.com/pastel-glyph/64/ffcc99/external-link--v1.png' width='18' height='18' style='display: inline-block; border: 0px;' /></a></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
						<tr>
							<td bgcolor='#f4f4f4' align='left' style='padding: 0px 30px 30px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;'> <br>
								<p style='margin: 0;'>If these emails get annoying, please feel free to <a href='#' style='color: #111111; font-weight: 700;'>unsubscribe</a>.</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
	
	</html>";
}
