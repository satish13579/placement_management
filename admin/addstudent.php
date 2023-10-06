<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <?php include 'admin.php'; ?>
    <div class="main-container">

        <!--Upload student data in bulk-->

        <div class="bulk">
            <div class="first">
                <h2>Group Insert</h2>
            </div>
            <div class="second">
                <div class="choose_file">
                    <input type="file" name="" id="choose" required>
                </div>
                <div class="drop_down">
                    <select name="" id="">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div class="submit_button">
                    <button name="submit" value="submit" id="submit_btn">SUBMIT</button>
                </div>
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
      }
      else{
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
      $rollno = TRIM($data[0]);
      $ay = TRIM($data[1]);
      $sem = TRIM($data[2]);
      $branch = TRIM($data[3]);
      $sec = TRIM($data[4]);
      $reg = TRIM($data[5]);
      $deptq = "SELECT * FROM `bran_dept_map` WHERE `sem`='$sem' and `branch`='$branch'";
      $deptr = mysqli_query($conn, $deptq);
      if (mysqli_num_rows(($deptr)) == 0) {
        $dept_id = $_SESSION['dept_id'];
      } else {
        $deptarr = mysqli_fetch_array($deptr);
        $dept_id = $deptarr['dept_id'];
      }
      try {
        $result = false;
        $q = "INSERT INTO `student_data` (`roll_no`,`ay`,`sem`,`dept_id`,`branch`,`sec`,`reg`) VALUES ('$rollno','$ay','$sem','$dept_id','$branch','$sec','$reg')";
        $result = mysqli_query($conn, $q);
      } catch (Exception $e) {
        $err_code = $e->getCode();

        if ($err_code == 1062) {
          $rows .= "<tr><td>$rollno</td><td>$ay</td><td>$sem</td><td>$branch</td><td>$sec</td><td>$reg</td></tr>";
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
      $duptables += 1;
?>
<h3>The Following Rollno's Data is Already in the Database.!!</h3>
<table class='table table-striped table-condensed'>
  <thead>
    <tr>
      <th>roll_no</th>
      <th>ay</th>
      <th>sem</th>
      <th>branch</th>
      <th>sec</th>
      <th>reg</th>
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
</div>
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
    </div>
</body>
</html>