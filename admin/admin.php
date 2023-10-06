

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard_container">
        <div class="main">
            <h2 class="admin-heading">Admin</h2>
            <div class="hr"></div>
            <ul>
                <li><a href="#" class="ul-el" aria-expanded="false">Dashboard</a></li>
                <li><a href="#" class="ul-el" aria-expanded="false">Add Student</a></li>
            </ul>
        </div>
    </div>


    <script>
        let UlElement = document.querySelector('.ul-el');
        
        UlElement.addEventListener('click', function(){
            UlElement.setAttribute('aria-expanded',true);
        });
    </script>
</body>
</html>
