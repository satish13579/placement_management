<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="admin.css">
    
</head>

<body>

    <?php include 'admin.php';
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

    $mail->Subject = "Account Password Generation Link";
    $link = $BASE_URL . 'reset_password.php?salt=' . $salt;
    $mail->Body = reset_template($link, $roll_no);

    $mail->send();
}
    ?>
<div class="main-container">

        <!--Upload student data in bulk-->

        <div class="bulk">
            <div class="first">
                <h2>Group Insert</h2>
            </div>
            <div class="second">
                <form enctype='multipart/form-data' action="" method="post">
                	<div class="choose_file">
                		<input size='50' type='file' class='filename' accept='.csv' name='filename[]' required multiple>
                	</div>
					<?php 
					$deptq=$conn->prepare("SELECT * FROM dept WHERE college_id=?");
					$deptq->execute(array(1));
					$depts=$deptq->fetchAll();
					$departments = array();
					?>
                	<div class="drop_down">
                    	<select name="dept_id" id="">
                        	<option value="">Select Department</option>
                        	<?php foreach($depts as $dept){
                            	?>
                            <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; $departments[$dept['dept_id']]=$dept['dept_name']; ?></option>
                            <?php
                        	} 		?>
                    	</select>
                	</div>
                	<div class="submit_button">
                    	<input type="submit" name="submit" value="submit" id="submit_btn"></input>
                	</div>
                </form>
            </div>
        </div>
        <?php
        if (isset($_POST['submit'])) {

            for ($j = 0; $j < count($_FILES['filename']['name']); $j++) {
                $errmsg = '';
                $handle = fopen($_FILES['filename']['tmp_name'][$j], "r");
                $ext = strtolower(pathinfo($_FILES['filename']['name'][$j], PATHINFO_EXTENSION));
                if ($ext != 'csv') {
                    $errmsg .= "<br><h3>This File Format for " . $_FILES['filename']['name'][$j] . " is not supported" . "<br>Please Use CSV File Formats Only.</h3>";
                }

                $line = 0;
                if ($ext == "csv") {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $line += 1;
                        if (count($data) >= 6) {
                            if ($data[0] == '' && $data[1] == '' && $data[2] == '' && $data[3] == '' && $data[4] == '' && $data[5] == '') {
                                continue;
                            }
                            if (
                                $data[0] == ''
                                || $data[1] == ''
                                || $data[2] == ''
                                || $data[3] == ''
                                || $data[4] == ''
                                || $data[5] == ''
                            ) {
                                $errmsg .= "<br><h3>Format Doesn't Matched in Line " . $line . " in " . $_FILES['filename']['name'][$j] . " file.</h3>";
                            }
                        } else {
                            $errmsg .= "<br><h3>Format Doesn't Matched in Line " . $line . " in " . $_FILES['filename']['name'][$j] . " file.</h3>";
                        }
                    }
                }
                fseek($handle, 0);
                if ($errmsg == '') {

                    $rows = '';
                    $uninserted = 0;
                    $inserted = 0;
                    $total = 0;
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if ($data[0] == '' && $data[1] == '' && $data[2] == '' && $data[3] == '' && $data[4] == '' && $data[5] == '') {
                            continue;
                        }
                        $total += 1;
                        $roll_no = TRIM($data[0]);
                        $first_name = TRIM($data[1]);
                        $last_name = TRIM($data[2]);
                        $dob = TRIM($data[3]);
                        $email = TRIM($data[4]);
                        $passout_year = TRIM($data[5]);
                        $dept_id = $_POST['dept_id'];
                        try {
                            $result = false;
                            $insert = $conn->prepare("INSERT INTO `students` (`roll_no`,`first_name`,`last_name`,`dob`,`email`,`passout_year`,`dept_id`) 
                    VALUES (?,?,?,?,?,?,?)");
                            $result = $insert->execute(array($roll_no, $first_name, $last_name, $dob, $email, $passout_year, $dept_id));
                            $salt = randomSalt(32);
                            date_default_timezone_set('Asia/Kolkata');
                            $cur = date('Y-m-d H:i:s');
                            sendSalt($email, $salt, $roll_no);
                            $insq = $conn->prepare("INSERT INTO `reset_password`(`role`, `user_id`, `salt`, `flag`, `date`)
        VALUES (?,?,?,?,?)");
                            $insq->execute(array('students', $roll_no, $salt, 0, $cur));
                        } catch (PDOException $e) {
                            $err_code = $e->getCode();

                            if ($err_code == 23000) {
                                $rows .= "<tr><td>$roll_no</td><td>$first_name</td><td>$last_name</td><td>$dob</td><td>$email</td><td>$passout_year</td></tr>";
                            }
                        }

                        if ($result == true) {
                            $inserted += 1;
                        } else {
                            $uninserted += 1;
                        }
                    }

        ?>
                    <div class="container carde" id='clear<?php echo $_FILES['filename']['name'][$j]; ?>'>
                        <?php echo "<h1>" . "File " . $_FILES['filename']['name'][$j] . " uploaded successfully." . "</h1>"; ?>
                        <h3>Total entries :
                            <?php echo $total; ?>
                        </h3>
                        <h3>Total inserted :
                            <?php echo $inserted; ?>
                        </h3>
                        <h3>Total uninserted :
                            <?php echo $uninserted; ?>
                        </h3>

                        <?php
                        if ($uninserted > 0) {
                        ?>
                            <h3>The Following Rollno's Data is Already in the Database.!!</h3>
                            <table class='table table-striped table-condensed'>
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>DOB</th>
                                        <th>EMAIL</th>
                                        <th>Passout Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $rows;
                                    ?>
                                </tbody>
                            </table>
                            <?php echo "<br>"; ?>

                    </div>
                <?php } else {
                            echo "<br>";
                ?>
	<?php
                        }
                    } else {
	?>
	<div class="container carde" id='clear<?php echo $_FILES['filename']['name'][$j]; ?>'>
    	<?php echo "<h1>" . "File " . $_FILES['filename']['name'][$j] . " uploaded successfully." . "</h1>";


                        echo $errmsg . "<br>";
    	?>
	</div>
	<?php
                    }

                    fclose($handle);
                }
	?>

	<?php } ?>

	<!--upload single student data-->
	<div style="margin: auto;
				margin-top:20px;
    	padding: 10px;" class="table-wrapper">
			<div class="table-title">
				<div class="row">
					<div class="col-sm-6">
						<h2>Manage <b>Students </b></h2>
					</div>
					<div class="col-sm-6 text-end">
                        <a href="#addPlacement"class="btn btn-primary"data-bs-toggle="modal"><i class="fa-solid fa-upload"></i> <span>Add Placement</span></a>
						<a href="#addStudent" id='addStudentbtn' class="btn btn-success" data-bs-toggle="modal"><i class="fa-solid fa-circle-plus"></i> <span>Add New Student</span></a>
						<a href="JavaScript:void(0);" class="btn btn-danger" id="delete_multiple"><i class="fa-solid fa-circle-minus"></i> <span>Delete</span></a>						
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<table id="students_table" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>
								<span class="custom-checkbox">
									<input type="checkbox" id="selectAll">
									<label for="selectAll"></label>
								</span>
							</th>
							<th class='align-middle text-center'>SL NO</th>
							<th class='align-middle text-center'>ROLL NO</th>
							<th class='align-middle text-center'>FIRST NAME</th>
							<th class='align-middle text-center'>LAST NAME</th>
							<th class='align-middle text-center'>DOB</th>
							<th class='align-middle text-center'>EMAIL</th> 
							<th class='align-middle text-center'>DEPT NAME</th>
							<th class='align-middle text-center'>PASSOUT YEAR</th>
							<th class='align-middle text-center'>ACTIONS</th>
						</tr>
					</thead>
					<tbody>
			
						<?php
						$result = $conn->prepare("SELECT * FROM students WHERE dept_id in (SELECT dept_id from dept WHERE college_id=?)");
						$result->execute(array(1));
						
						$arrs = $result->fetchAll();
							$i=1;
							foreach($arrs as $row) {
						?>
						<tr id="<?php echo $row['roll_no']; ?>">
						<td>
									<span class="custom-checkbox">
										<input type="checkbox" class="user_checkbox" data-user-id="<?php echo $row['roll_no']; ?>">
										<label for="checkbox2"></label>
									</span>
								</td>
							<td class='align-middle text-center'><?php echo $i; ?></td>
							<td class='align-middle text-center'><?php echo $row["roll_no"]; ?></td>
							<td class='align-middle text-center'><?php echo $row["first_name"]; ?></td>
							<td class='align-middle text-center'><?php echo $row["last_name"]; ?></td>
							<td class='align-middle text-center' style="white-space:pre;"><?php echo $row["dob"]; ?></td>
							<td class='align-middle text-center'><?php echo $row["email"]; ?></td>
							<td class='align-middle text-center'><?php echo $departments[$row['dept_id']]; ?></td>
							<td class='align-middle text-center'><?php echo $row["passout_year"]; ?></td>
							<td class='align-middle text-center' id="flex_items">
								<a style="text-decoration:none;" href="#editStudent" class="edit" data-bs-toggle="modal">
								<i class="fa-solid fa-pen-to-square update " 
									style="color: #fff;"
									data-id="<?php echo $row['roll_no']; ?>"
									data-firstname="<?php echo $row['first_name']; ?>"
									data-lastname="<?php echo $row["last_name"]; ?>"
									data-dob="<?php echo $row["dob"]; ?>"
									data-email="<?php echo $row["email"]; ?>"
									data-deptid="<?php echo $row["dept_id"]; ?>"
									data-passoutyear="<?php echo $row["passout_year"]; ?>"
									title="Edit"></i>
								</a>
								<a href="#deleteStudent" class="delete" data-id="<?php echo $row['roll_no']; ?>" data-bs-toggle="modal"><i class="fa-sharp fa-solid fa-trash" style="color: #fff;" title="Delete"></i>
									</a>
								<a href="#" class="regenerate" data-roll="<?php echo $row['roll_no']; ?>"><i class="fa-solid fa-lock" style="color: #ffffff;"></i></a>
							</td>
						</tr>
						<?php
						$i++;
						}
						?>
						</tbody>
					</table>
					</div>
				
				</div>
</div>
    <script>
        $(document).ready( function () {
    $('#students_table').DataTable({
        columnDefs: [{ orderable: false, targets: 0 },{ orderable: false, targets: 9 }],
        order: [[1, "asc"]]
    });
} );
    </script>
</body>

<!-- Add Placement HTML -->
<div id="addPlacement" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="placement_form">
				<div class="modal-header">						
					<h4 class="modal-title">Add Placement</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">	
					<div class="form-group">
						<label>COMPANY</label>
						<input type="text" id="company" name="company" class="form-control" required>
					</div>
					<div class="form-group">
						<label>PACKAGE</label>
						<input type="number" min="0" id="package" name="package" class="form-control" required>
					</div>				
					<div class="form-group">
						<label>JOB ROLE</label>
						<input type="text" id="role" name="role" class="form-control" required>
					</div>
                    <div class="form-group">
						<label>JOB STATUS</label>
						<select id="job_status" name="job_status" class="form-control" required>
                            <option value="">Select Job Status</option>
                            <option value="1">Placed</option>
                            <option value="0">Rejected</option>
                        </select>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" value="placement_upload" name="type">
					<input id="place-add-sub" type='submit' style="display:none">
					<input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
					<button type="button" class="btn btn-primary" id="place-add">Add</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Add Modal HTML -->
<div id="addStudent" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="user_form">
				<div class="modal-header">						
					<h4 class="modal-title">Add Student</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">	
					<div class="form-group">
						<label>ROLL NO</label>
						<input type="text" id="roll_no" name="roll_no" class="form-control" required>
					</div>
					<div class="form-group">
						<label>FIRST NAME</label>
						<input type="text" id="ay" name="first_name" class="form-control" required>
					</div>				
					<div class="form-group">
						<label>LAST NAME</label>
						<input type="text" id="branch" name="last_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>DOB</label>
						<input type="date" id="sec" name="dob" class="form-control" required>
					</div>
                    <div class="form-group">
                        <label>DEPARTMENT</label>
                        <select name="dept_id" required>
                        <option value="">Select Department</option>
                        <?php foreach($depts as $dept){
                            ?>
                            <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; $departments[$dept['dept_id']]=$dept['dept_name']; ?></option>
                            <?php
                        } ?>
                    </select>
                    </div>
					<div class="form-group">
						<label>EMAIL</label>
						<input type="email" id="sem" name="email" class="form-control" required>
					</div>
					<div class="form-group">
						<label>PASSOUT YEAR</label>
						<input  type="number" min="1900" max="2099" step="1" name="passout_year" class="form-control" required>
					</div>					
				</div>
				<div class="modal-footer">
					<input type="hidden" value="student_single_add" name="type">
					<input id="btn-add-sub" type='submit' style="display:none">
					<input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
					<button type="button" class="btn btn-primary" id="btn-add">Add</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Edit Modal HTML -->
<div id="editStudent" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="update_form">
				<div class="modal-header">						
					<h4 class="modal-title">Edit Student</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>ROLL NO</label>
						<input type="text" id="roll_no_u" name="roll_no" class="form-control" required>
					</div>
					<div class="form-group">
						<label>FIRST NAME</label>
						<input type="text" id="first_name_u" name="first_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>LAST NAME</label>
						<input type="text" id="last_name_u" name="last_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>DOB</label>
						<input type="date" id="dob_u" name="dob" class="form-control" required>
					</div>
					<div class="form-group">
						<label>DEPARTMENT</label>
						<select id="dept_id_u" name="dept_id" required>
                        <option value="">Select Department</option>
                        <?php foreach($depts as $dept){
                            ?>
                            <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; $departments[$dept['dept_id']]=$dept['dept_name']; ?></option>
                            <?php
                        } ?>
                    </select>
					</div>
					<div class="form-group">
						<label>EMAIL</label>
						<input type="email" id="email_u" name="email" class="form-control" required>
					</div>				
                    <div class="form-group">
						<label>PASSOUT YEAR</label>
						<input  type="number" min="1900" max="2099" step="1" id="passout_year_u" name="passout_year" class="form-control" required>
					</div>			
				</div>
				<div class="modal-footer">
					<input type='submit' style="display:none" id='update-sub'>
				<input type="hidden" value="student_single_edit" name="type">
					<input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
					<button type="button" class="btn btn-primary" id="update">Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Delete Modal HTML -->
<div id="deleteStudent" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form>
				<div class="modal-header">						
					<h4 class="modal-title">Delete Student</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="roll_no_d" name="r" class="form-control">					
					<p>Are you sure you want to delete this Student Record?</p>
					<p class="text-warning"><small>This action cannot be undone.</small></p>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
					<button type="button" class="btn btn-danger" id="delete">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="addstudent.js"></script>
</html>

<?php

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
								<p style='margin: 0;'>As Your Account Has Been Created By The Rollno <span style='background-color:#808080;color:#ffffff;padding:4px 6px 4px 6px;border-radius:4px;'>$roll_no</span> , We've Sent The Password Creation Link. Just Click The Below Button To Create Your Password.</p>
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

?>