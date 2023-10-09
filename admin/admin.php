<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>

</head>

<body>
    <div class="dashboard_container">
        <div class="main">
            <h2 class="admin-heading">ADMIN &nbsp;<i class="fa fa-solid fa-user fa-sm" style="color:#fff"></i></h2>
            <div class="hr"></div>
            <ul>
                <li><i class="fa-solid fa-flag" style="color: #ffffff;"></i>&nbsp;<a href="./dashboard.php" class="ul-el" aria-expanded="false">Reports</a></li>
                <li><i class="fa-solid fa-users" style="color: #ffffff;"></i>&nbsp;<a href="./addstudent.php" class="ul-el" aria-expanded="false"> Manage Student</a></li>
                <li><i class="fa-solid fa-building-columns" style="color: #ffffff;"></i>&nbsp;<a href="./adddepartment.php" class="ul-el" aria-expanded="false">Manage Departments</a></li>
                <li><i class="fa-solid fa-arrow-right-from-bracket" style="color: #ffffff;"></i>&nbsp;&nbsp;<a href="../logout.php" class="ul-el" aria-expanded="false">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="offcanvas_mobile">
        <nav>
            <div class="navbar_main">
                <div class="admin_heading">
                    <h2 class="admin-heading">ADMIN &nbsp;<i class="fa fa-solid fa-user fa-sm" style="color:#fff"></i></h2>
                </div>
                <div class="items" id="items">
                    <button id="side_item">
                        <i class="fa-solid fa-bars fa-lg" style="color: #ffffff;" id="hamburger_menu"></i>
                    </button>
                    <button id="item_side">
                        <i class="fa-solid fa-xmark fa-lg" style="color: #ffffff; display:none;" id="hamburger_cross"></i>
                    </button>
                </div>
            </div>
        </nav>
    </div>
    <div class="sidebar_items">
        <ul>
            <li><i class="fa-solid fa-flag" style="color: #ffffff;"></i>&nbsp;&nbsp;<a href="./dashboard.php" class="ul-el" aria-expanded="false">Reports</a></li>
            <li><i class="fa-solid fa-users" style="color: #ffffff;"></i>&nbsp;<a href="./addstudent.php" class="ul-el" aria-expanded="false"> Manage Student</a></li>
            <li><i class="fa-solid fa-building-columns" style="color: #ffffff;"></i>&nbsp;&nbsp;<a href="./adddepartment.php" class="ul-el" aria-expanded="false">Manage Departments</a></li>
            <li><i class="fa-solid fa-arrow-right-from-bracket" style="color: #ffffff;"></i>&nbsp;&nbsp;<a href="../logout.php" class="ul-el" aria-expanded="false">Logout</a></li>
        </ul>
    </div>
    <script>
        let UlElement = document.querySelector('.ul-el');
        let SideItem = document.getElementById('side_item');
        let SidebarItems = document.querySelector('.sidebar_items');
        let Cross = document.getElementById('hamburger_cross');
        let Items = document.getElementById('item_side');
        let Bars = document.getElementById('hamburger_menu');

        UlElement.addEventListener('click', function() {
            UlElement.setAttribute('aria-expanded', true);
        });

       SideItem.addEventListener('click',function(){
            SidebarItems.style.display = 'block';
            Cross.style.display = 'block';
            Bars.style.display = 'none';
       });

       Items.addEventListener('click',function(){
            SidebarItems.style.display = 'none';
            Cross.style.display = 'none';
            Bars.style.display = 'block';
       })
       

    </script>
</body>

</html>