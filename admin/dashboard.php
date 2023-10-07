<?php
//include 'auth.php';
include 'auth.php';
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600&display=swap');

        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            font-family: 'Poppins', sans-serif;
        }

        :root{
            --bgcolor: #102435;
        }

        form{
            background-color: var(--bgcolor);
            border: 2px solid var(--bgcolor);
            padding: 10px 20px;
            border-radius: 4px;
            display: grid;
            grid-template-columns: auto auto;
            row-gap: 16px;
            text-align: center;
            width: 700px;
            margin-top: 0px;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        #submit_button{
            grid-column: span 2;
            width: 40%;
            margin: auto;
            box-shadow: none;
            color: #fff;
            border: 1px solid #fff;
            background-color: transparent;
            border-radius: 4px;
            padding-block: 5px;
        }

        #numbr_inpt{
            border: 1px solid #fff;
            background-color: transparent;
            border-radius: 4px;
            color: #fff;
            padding-block: 5px;
        }

        .lbl_data{
            color: #fff;
        }

        #slct_optn{
            border: 1px solid #fff;
            background-color: var(--bgcolor);
            border-radius: 4px;
            color: #fff;
            padding-block: 5px;
        }

        .overall_container{
            display: flex;
            flex-direction: row;
            gap: 10px;
            margin: auto;
            width: 900px;
            margin-top:30px;
            filter: drop-shadow(2px 2px 15px rgba(16, 36, 53, .5));
            margin-bottom: 20px;
        }

        .overall_container canvas{
            width: 300px;
        }

        #placement_table{
            padding: 10px 5px;
        }

        #placement_table th, #placement_table td{
            border: 1px solid rgba(255, 255, 255, .4);
            background-color: var(--bgcolor);
            color: #fff;
        }

        .table-responsive{
            overflow-x: auto;
        }

        @media screen and (width <= 500px){
            form{
                width: auto;
                row-gap: 20px;
                column-gap: 10px;
            }

            .lbl_data{
                font-size: 14px;
            }

            .overall_container{
                flex-direction: column;
                width: 300px;
                padding-left: 15px;
                gap: 30px;
            }
        }   
    </style>
</head>

<body>

    <?php include 'admin.php'; 
    ?>
<div class="main-container">
    <form action="" method="post" style="margin:auto;">
        <label for="slct_optn" class="lbl_data"><b>Dept ID:</b></label>
        <select name="dept_id" id="slct_optn">
            <option value="">Select Department</option>
            <?php
            $deptq = $conn->prepare("SELECT * FROM dept WHERE college_id=?");
            $deptq->execute(array($college_id));
            $depts = $deptq->fetchAll();
            foreach ($depts as $dept) {
            ?><option value="<?php echo $dept['dept_id']; ?>" <?php if(isset($_POST['dept_id'])){ if($_POST['dept_id']==$dept['dept_id']){ ?> selected <?php }} ?>><?php echo $dept['dept_name']; ?></option> <?php
                                                                                                        } ?>
        </select>

        <label for="numbr_inpt" class="lbl_data"><b>Package Less Than:</b></label>
        <input type="number" name="less_than" min="0" id="numbr_inpt" <?php if(isset($_POST['less_than'])){ ?> value="<?php echo $_POST['less_than']; ?>"<?php } ?>>

        <label for="numbr_inpt" class="lbl_data"><b>Package Greater Than:</b></label>
        <input type="number" name="greater_than" min="0" id="numbr_inpt" <?php if(isset($_POST['greater_than'])){ ?> value="<?php echo $_POST['greater_than']; ?>"<?php } ?>>

        <label for="numbr_inpt" class="lbl_data"><b>Passout Year:</b></label>
        <input type="number" name="passout_year" min="2000" id="numbr_inpt" <?php if(isset($_POST['passout_year'])){ ?> value="<?php echo $_POST['passout_year']; ?>"<?php } ?>>

        <input type="submit" id="submit_button">
    </form>

            <div class="overall_container">
                <div>                                                                                  
                    <canvas id="myPieChart" width="200" height="400"></canvas>
                </div>
                <div>                                                                                  
                    <canvas id="jobTitlesChart" width="200" height="400"></canvas>
                </div>
                <div>                                                                                  
                    <canvas id="CompanyChart" width="200" height="400"></canvas>
                </div>
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
            $studentq = "SELECT roll_no FROM students WHERE dept_id in (SELECT dept_id from dept WHERE college_id=" . $_SESSION['id'] . ")";
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
        

        <div class="table-responsive">
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
</div>