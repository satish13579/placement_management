

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
		integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="admin.css">

</head>
<body>
    <div class="dashboard_container">
        <div class="main">
            <h2 class="admin-heading">Admin</h2>
            <div class="hr"></div>
            <ul>
                <li>
                    <i class="fa-solid fa-table-columns" style="color: #ffffff;"></i>
                    <a href="#" class="ul-el" aria-expanded="false">Dashboard</a>
                </li>
                <li>
                    <i class="fa-solid fa-users" style="color: #ffffff;"></i>
                    <a href="#" class="ul-el" aria-expanded="false">Add Student</a>
                </li>
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
