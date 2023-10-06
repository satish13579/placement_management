<?php
//include 'auth.php';
include 'auth.php';
session_start();
$college_id = $_SESSION['id'];
$college_name = $_SESSION['name'];
$email = $_SESSION['email'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

</head>

<body>

    <?php //include 'admin.php'; 
    ?>
    <form action="" method="post" style="margin-left:240px;">
        <label>Dept ID</label>
        <select name="dept_id">
            <?php
            $deptq = $conn->prepare("SELECT * FROM dept WHERE college_id=?");
            $deptq->execute(array($college_id));
            $depts = $deptq->fetchAll();
            foreach ($depts as $dept) {
            ?><option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option> <?php
                                                                                                        } ?>
        </select>

        <label>Package Less Than</label>
        <input type="number" name="less_than" min="0">

        <label>Package Greater Than</label>
        <input type="number" name="greater_than" min="0">

        <label>Passout Year</label>
        <input type="number" name="passout_year" min="2000">

        <input type="submit">
    </form>
                      <div style="width:300px;">                                                                                  
    <canvas id="myPieChart" width="400" height="400"></canvas>
                </div>
                <div style="width:300px;">                                                                                  
                <canvas id="jobTitlesChart" width="400" height="400"></canvas>
                </div>
                <div style="width:300px;">                                                                                  
                <canvas id="CompanyChart" width="400" height="400"></canvas>
                </div>


    <?php
    if (count($_POST) > 0) {
        if ($_POST['dept_id'] != '' && $_POST['passout_year'] != '') {
            $studentq = "SELECT roll_no FROM students WHERE dept_id=" . $_POST['dept_id'] . " and passout_year=" . $_POST['passout_year'];
        } else if ($_POST['dept_id'] == '' && $_POST['passout_year'] != '') {
            $studentq = "SELECT roll_no FROM students WHERE passout_year=" . $_POST['passout_year'];
        } else if ($_POST['dept_id'] != '' && $_POST['passout_year'] == '') {
            $studentq = "SELECT roll_no FROM students WHERE dept_id=" . $_POST['dept_id'];
        } else {
            $studentq = "SELECT roll_no FROM students WHERE dept_id in (SELECT dept_id in dept WHERE college_id=" . $_SESSION['id'] . ")";
        }

        $mainq = "SELECT * FROM placements WHERE roll_no in (" . $studentq . ") ";
        if ($_POST['greater_than'] > 0 && $_POST['less_than']=='') {
            $mainq .= "AND package>=" . $_POST['greater_than'];
        } else if ($_POST['less_than'] > 0 && $_POST['greater_than']=='') {
            $mainq .= "AND package<=" . $_POST['less_than'];
        }else if($_POST['less_than'] > 0 && $_POST['greater_than']>0){
            $mainq .= "AND ( package<=" . $_POST['less_than']." OR package>=".$_POST['greater_than']." )";
        }

        $q = $conn->prepare($mainq);
        $q->execute();
        $res = $q->fetchAll();
        $pie_graph_data=array();
        $pie_graph_data['Placed']=0;
        $pie_graph_data['Rejected']=0;
        $jobs = array();
        $companies = array();
        foreach($res as $row){
            if($row['job_status']==1){
                $pie_graph_data['Placed']++;
                if(array_key_exists($row['job_role'],$jobs)){
                    $jobs[$row['job_role']]++;
                }else{
                    $jobs[$row['job_role']]=1;
                }
                if(array_key_exists($row['company'],$companies)){
                    $companies[$row['company']]++;
                }
                else{
                    $companies[$row['company']]=1;
                }
            }else{
                $pie_graph_data['Rejected']++;
            }
        }
        ?>
        

        <div classs="table-responsive">
            <table id="placement_table" class="table table-striped table-hover">
                <thead><th>S.NO</th><th>Roll No</th><th>Job Title</th><th>Company</th><th>Package</th><th>Job Status</th><th>Is Accepted</th></thead>
                <tbody>
                    <?php $i=0; foreach($res as $row){ ?>
                        <tr>
                            <td><?php echo ++$i; ?></td>
                            <td><?php echo $row['roll_no']; ?></td>
                            <td><?php echo $row['job_role']; ?></td>
                            <td><?php echo $row['company']; ?></td>
                            <td><?php echo $row['package']; ?></td>
                            <td><?php if($row['job_status']==1){
                                echo "<i class='fa-solid fa-circle-check'></i> <span class='placed'>Placed</span>"; }else{ echo "<i class='fa-solid fa-circle-xmark'></i> <span class='rejected'>Rejected</span>"; }?></td>
                            <td><?php if($row['job_status']==1){
                                echo "<i class='fa-solid fa-circle-check'></i> <span class='placed'>Placed</span>"; }else{ echo "<i class='fa-solid fa-circle-xmark'></i> <span class='rejected'>Rejected</span>"; }?></td>
                        </tr>
<?php
                    } ?>
                </tbody>
            </table>
        </div>

        <script>
            $(document).ready( function () {
    $('#placement_table').DataTable({});
        } );
        </script>
        <script>
            var Stats = <?php echo json_encode($pie_graph_data); ?>;
            var labels = Object.keys(Stats);
            var data = Object.values(Stats);

        // Check if data is empty
        console.log(data);
        if (data[0] == 0 && data[1]== 0) {
            var ctt = document.getElementById('myPieChart').getContext('2d');
            ctt.font = '20px Arial';
            ctt.textAlign = 'center';
            ctt.textBaseline = 'middle';
            ctt.fillText('No sufficient data available', 200, 200);
        } else {
            var data = {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            };

            // Get the canvas element
            var ctx = document.getElementById('myPieChart').getContext('2d');

            // Create the pie chart
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: data,
			options : {
				title : {
					display : true,
					text : 'Responsive Pie Chart'
				},
				responsive : true,
        }
            });
        }

            

            //2nd pie 
        var jobData = <?php echo json_encode($jobs); ?>;

        // Convert the data object into arrays for labels and data
        var labels = Object.keys(jobData);
        var data = Object.values(jobData);

        if (data.length === 0) {
            var ctx = document.getElementById('jobTitlesChart').getContext('2d');
            ctx.font = '20px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('No sufficient data available', 200, 200);
        } else {
        // Generate random background colors for each slice
        var backgroundColors = labels.map(function() {
            return '#' + (Math.random().toString(16) + '000000').slice(2, 8);
        });

        // Get the canvas element
        var ctx = document.getElementById('jobTitlesChart').getContext('2d');

        // Create the pie chart
        var jobTitlesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                }]
            },
        });
    }


        //companywise data

        var companyData = <?php echo json_encode($companies); ?>;

        // Convert the data object into arrays for labels and data
        var labels = Object.keys(companyData);
        var data = Object.values(companyData);
        if (data.length === 0) {
            var ctx = document.getElementById('CompanyChart').getContext('2d');
            ctx.font = '20px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('No sufficient data available', 200, 200);
        } else {
        // Generate random background colors for each slice
        var backgroundColors = labels.map(function() {
            return '#' + (Math.random().toString(16) + '000000').slice(2, 8);
        });

        // Get the canvas element
        var ctx = document.getElementById('CompanyChart').getContext('2d');

        // Create the pie chart
        var jobTitlesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                }]
            },
        });
    }


        </script>

        <?php

    ?>
        
    <?php
    }

    ?>