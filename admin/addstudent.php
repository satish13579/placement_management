<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
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
                    <input type="file" name="choose File" id="choose">
                </div>
                <div class="drop_down">
                    <select name="" id="">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div class="submit_button">
                    <button id="submit_btn">SUBMIT</button>
                </div>
            </div>
        </div>

        <!--upload single student data-->
    </div>
</body>
</html>