<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="admin.css">
    
</head>

<body>

    <?php include 'admin.php';
    include 'auth.php';
    use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
    ?>
<div class="main-container">

		<!--upload single student data-->

		<div style="margin:auto;
			padding: 20px;" class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-sm-6">
							<h2>Manage <b>Departments </b></h2>
						</div>
						<div class="col-sm-6 text-end" id="btns">
							<a href="#addStudent" id='addStudentbtn' class="btn btn-success" data-bs-toggle="modal"><i class="fa-solid fa-circle-plus"></i> <span>Add New Department</span></a>
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
							<th class='align-middle text-center'>DEPARTMENT NAME</th>
							<th class='align-middle text-center'>EMAIL</th>
							<th class='align-middle text-center'>ACTIONS</th>
						</tr>
					</thead>
					<tbody>
					
					<?php
					$result = $conn->prepare("SELECT * FROM dept WHERE college_id=?");
					$result->execute(array(1));
					
					$arrs = $result->fetchAll();
						$i=1;
						foreach($arrs as $row) {
					?>
					<tr id="<?php echo $row['dept_id']; ?>">
					<td>
								<span class="custom-checkbox">
									<input type="checkbox" class="user_checkbox" data-user-id="<?php echo $row['dept_id']; ?>">
									<label for="checkbox2"></label>
								</span>
							</td>
						<td class='align-middle text-center'><?php echo $i; ?></td>
						<td class='align-middle text-center'><?php echo $row["dept_name"]; ?></td>
						<td class='align-middle text-center'><?php echo $row["email"]; ?></td>
						<td class='align-middle text-center' id="flex_items">
							<a style="text-decoration:none;" href="#editStudent" class="edit" data-bs-toggle="modal">
							<i class="fa-solid fa-pen-to-square update"
								style="color: #fff;"
								data-id="<?php echo $row['dept_id']; ?>"
								data-deptname="<?php echo $row['dept_name']; ?>"
								data-email="<?php echo $row["email"]; ?>"
								title="Edit"></i>
							</a>
							<a href="#deleteStudent" class="delete" data-id="<?php echo $row['dept_id']; ?>" data-bs-toggle="modal"><i class="fa-sharp fa-solid fa-trash" style="color: #fff;" title="Delete"></i>
								</a>
							<a href="#" class="regenerate" data-roll="<?php echo $row['dept_id']; ?>"><i class="fa fa-solid fa-lock" style="color: #fff;"></i></a>
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
        columnDefs: [{ orderable: false, targets: 0 },{ orderable: false, targets: 4 }],
        order: [[1, "asc"]]
    });
} );
    </script>
</body>


<!-- Add Modal HTML -->
<div id="addStudent" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="user_form">
				<div class="modal-header">						
					<h4 class="modal-title">Add Department</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">	
					<div class="form-group">
						<label>DEPARTMENT NAME</label>
						<input type="text" id="roll_no" name="dept_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>EMAIL</label>
						<input type="email" id="ay" name="email" class="form-control" required>
					</div>							
				</div>
				<div class="modal-footer">
					<input type="hidden" value="department_single_add" name="type">
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
					<h4 class="modal-title">Edit Department</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>DEPARTMENT NAME</label>
                        <input type="hidden" id="dept_id_u" name="dept_id">
						<input type="text" id="roll_no_u" name="dept_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label>EMAIL</label>
						<input type="email" id="first_name_u" name="email" class="form-control" required>
					</div>
						
				</div>
				<div class="modal-footer">
					<input type='submit' style="display:none" id='update-sub'>
				<input type="hidden" value="departmemt_single_edit" name="type">
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
					<h4 class="modal-title">Delete Department</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="roll_no_d" name="r" class="form-control">					
					<p>Are you sure you want to delete this Department Record?</p>
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
<script src="adddepartment.js"></script>
</html>