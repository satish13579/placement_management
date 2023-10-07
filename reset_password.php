<?php
include 'conn.php';
$stat = 1;
if (count($_GET) > 0) {
	$salt = $_GET['salt'];
    $q=$conn->prepare("SELECT * FROM `reset_password` WHERE `salt`=?");
    $q->execute(array($salt));
	if ($q->rowCount() >= 1) {
		$arr = $q->fetch();
		$flag = $arr['flag'];
		$date = $arr['date'];
		$id = $arr['user_id'];
		if ($flag == 1) {
			$stat = 2;
		} else {
			date_default_timezone_set('Asia/Kolkata');
			$prev_date = date_create($date);
			date_add($prev_date, date_interval_create_from_date_string('24 hours'));
			$cur_date = date_create();
			if ($cur_date <= $prev_date) {
				$stat = 4;
			} else {
				$stat = 3;
			}
		}
	} else {
		$stat = 1;
	}
}
?>
<script src="https://kit.fontawesome.com/a4e89c7158.js" crossorigin="anonymous"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600&display=swap');

    *{
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        scroll-behavior: smooth;
        font-family: 'Poppins', sans-serif;
    }

    body{
        min-height: 100vh; 
        background-color: rgb(20, 20, 39);
        display: flex; 
        justify-content: center; 
        align-items: center;
    }

    .user_card{
        border: 1px solid #fff;
        border-radius: 6px;
        padding: 20px;
    }

    .input-group{
        background-color: #fff;
        border: 2px solid #000;
        border-radius: 4px;
        padding: 5px 10px;
        box-shadow: rgb(219, 219, 219) 3px 3px 6px 0px inset, 
        rgba(221, 220, 220, 0.5) -3px -3px 6px 1px inset;
        margin: auto;
        margin-bottom: 20px;
    }

    input:focus{
        outline: none;
    }

    input{
        border: none;
        padding-left: 5px;
    }

    .req{
        padding: 10px;
    }

    .req p{
        font-size: 12px;
        color:#fff;
        margin-bottom: 4px;
    }

    #resetpassword{
        background-color: transparent;
        color: #fff;
        padding: 5px 20px;
        border: 2px solid #fff;
        transition: all .2s;
        border-radius: 4px;
        font-weight: bold;
        width: 90%;
        text-align: center;
    }

    #resetpassword:hover{
        background-color: #fff;
        color: rgb(20, 20, 39);
        cursor: pointer;
    }

    #resetpassword:active{
        transform: scale(.95);
    }

    .login_container{
        text-align: center;
    }

    .links{
        text-align: center;
        padding: 20px;
    }

    .links a{
        color: #fff;
    }

</style>
<div class="user_card">
    <div class="d-flex justify-content-center form_container">
        <?php
        if ($stat == 1) {
        ?>
            <h5 style="text-align:center"> The Link is Invalid.!!<br><br>Check Your URL Correctly.!! </h5>
        <?php
        } else if ($stat == 2) {
        ?>
            <h5 style="text-align:center"> This Link is Already Used.!!<br><br>Regenerate Another Reset Link From Login Page Given Below
            </h5>
        <?php
        } else if ($stat == 3) {
        ?>
            <h5 style="text-align:center"> This Link Has Expired.!!<br><br>Regenerate Another Reset Link From Login Page Given Below and
                Use It Before 24 Hrs After Link Generation.</h5>
        <?php
        } else if ($stat == 4) {
        ?>
            <form id="rpform">
                <div class="input-group mb-3">
                    <label for=""><i class="fa fa-solid fa-lock" style="color: rgb(20, 20, 39)"></i></label>
                    <input id="p1" type="password" name="p1" class="form-control input_user" value="" placeholder="Enter a New Password" required>
                </div>
                <div class="input-group mb-2">

                    <label for=""><i class="fa fa-solid fa-lock" style="color: rgb(20, 20, 39)"></i></label>        
                    <input id="p2" type="text" name="p2" class="form-control input_pass" value="" placeholder="Retype Your Password" required>
                </div>
                <div style="display:none;" class="req">
                    <p id='head'><b>Password Must FulFill Below Requirements :</b></p>
                    <p id='len'>* Length 8 - 15 Characters <span id='splen'></span></p>
                    <p id='lower'>* 1 lowercase Character <span id='splower'></span></p>
                    <p id='upper'>* 1 UpperCase Character <span id='spupper'></span></p>
                    <p id='number'>* 1 Number <span id='spnumber'></span></p>
                    <p id='special'>* 1 special Character <span id='spspecial'></span></p>
                </div>
                <div style="display:none;" class="passmatch">
                    <p style="color:red;" id="pmatch">Both Passwords Should Match</p>
                </div>

                <p id='data' style="display:none" data-p1="0" data-p2="0"></p>
                <div class="d-flex justify-content-center mt-3 login_container">
                    <button style="margin-top: 22px;" type="button" name="button" id="resetpassword" class="btn login_btn">Reset Password</button>
                </div>
                <input type="hidden" name="type" value="reset_password"></input>
                <input type="hidden" name="salt" value="<?php echo $salt; ?>"></input>
            </form>
            <script>
                let Eye = document.getElementById('')
            </script>
        <?php
        }

        ?>
    </div>

    <div class="mt-4">
        <div class="d-flex justify-content-center links">
            <a href="http://localhost/placement_management/login.html">Go to Login</a>
        </div>
    </div>
</div>

<script>
    var rpform = document.getElementById('rpform');
		if (rpform != null) {
			var p1 = document.getElementById('p1');
			var p2 = document.getElementById('p2');
			p1.addEventListener('keyup', function (event) {
				if(event.key=='Enter'){
					p2.focus()
				}
				var headbox = document.getElementById('head');
				var pass = this.value;
				var upper = false;
				var upperbox = document.getElementById('upper');
				var lower = false;
				var lowerbox = document.getElementById('lower');
				var digit = false;
				var digitbox = document.getElementById('number');
				var special = false
				var specialbox = document.getElementById('special');
				var len = false;
				var lenbox = document.getElementById('len');
				if (this.value.length >= 8 && this.value.length <= 15) {
					len = true;
				}
				if (hasNumber(pass)) {
					digit = true;
				}
				if (containsSpecialChars(pass)) {
					special = true;
				}
				if (containsLowercase(pass)) {
					lower = true;
				}
				if (containsUppercase(pass)) {
					upper = true;
				}
				var data=document.getElementById('data');
				if(upper && lower && digit && special && len){
					data.setAttribute('data-p1',"1");
				}
				else{
					data.setAttribute('data-p1',"0");
				}

				if (!upper) {
					upperbox.style.display = "block";
					upperbox.style.color = "red";
					var span=document.getElementById('spupper');
					span.innerHTML="<i class='fa-regular fa-circle-xmark'></i>";
					
				}
				else {
					upperbox.style.display = "block";
					upperbox.style.color = "green";
					var span=document.getElementById('spupper');
					span.innerHTML="<i class='fa-regular fa-circle-check'></i>";
				}

				if (!lower) {
					lowerbox.style.display = "block";
					lowerbox.style.color = "red";
					var span=document.getElementById('splower');
					span.innerHTML="<i class='fa-regular fa-circle-xmark'></i>";

				}
				else {
					lowerbox.style.display = "block";
					lowerbox.style.color = "green";
					var span=document.getElementById('splower');
					span.innerHTML="<i class='fa-regular fa-circle-check'></i>";
				}

				if (!digit) {
					digitbox.style.display = "block";
					digitbox.style.color = "red";
					var span=document.getElementById('spnumber');
					span.innerHTML="<i class='fa-regular fa-circle-xmark'></i>";
				}
				else {
					digitbox.style.display = "block";
					digitbox.style.color = "green";
					var span=document.getElementById('spnumber');
					span.innerHTML="<i class='fa-regular fa-circle-check'></i>";
				}

				if (!special) {
					specialbox.style.display = "block";
					specialbox.style.color = "red";
					var span=document.getElementById('spspecial');
					span.innerHTML="<i class='fa-regular fa-circle-xmark'></i>";
				}
				else {
					specialbox.style.display = "block";
					specialbox.style.color = "green";
					var span=document.getElementById('spspecial');
					span.innerHTML="<i class='fa-regular fa-circle-check'></i>";
				}

				if (!len) {
					lenbox.style.display = "block";
					lenbox.style.color = "red";
					var span=document.getElementById('splen');
					span.innerHTML="<i class='fa-regular fa-circle-xmark'></i>";
				}
				else {
					lenbox.style.display = "block";
					lenbox.style.color = "green";
					var span=document.getElementById('splen');
					span.innerHTML="<i class='fa-regular fa-circle-check'></i>";
				}


			});
			p1.addEventListener('focusin', function (event) {
				divv = document.getElementsByClassName('req');
				divv[0].style.display = 'block';
			});
			p1.addEventListener('focusout', function (event) {
				divv = document.getElementsByClassName('req');
				divv[0].style.display = 'none';
			});
			p2.addEventListener('keyup',function(event){
				if(event.key=='Enter'){
					$('#resetpassword').click()
				}
				var p1=document.getElementById('p1');
				var pass=p1.value;
				var confpass=this.value;
				var divv=document.getElementsByClassName('passmatch');
				if(pass==confpass){
					divv[0].style.display="none";
				}
				else{
					divv[0].style.display="block";
				}
			});
			p2.addEventListener('focusin', function (event) {
				var p1=document.getElementById('p1');
				var pass=p1.value;
				var confpass=this.value;
				var divv=document.getElementsByClassName('passmatch');
				if(pass==confpass){
					divv[0].style.display="none";
				}
				else{
					divv[0].style.display="block";
				}
			});
			p2.addEventListener('focusout',function(event){
				var divv=document.getElementsByClassName('passmatch');
				divv[0].style.display="none";
			})
			var resetpassword = document.getElementById('resetpassword');
			resetpassword.addEventListener('click',function(event){
				var data=document.getElementById('data');
				var p1=data.getAttribute('data-p1');
				if(p1=='0'){
					var p1box=document.getElementById('p1');
					p1box.focus();
					return;
				}
				var p1box=document.getElementById('p1');
				var p1pass=p1box.value;
				var p2box=document.getElementById('p2');
				var p2pass=p2box.value;
				console.log(p1pass);
				console.log(p2pass);
				if(p1pass!=p2pass){
					var p2box=document.getElementById('p2');
					p2box.focus();
					return;
				}
				var data = $("#rpform").serialize();
				$.ajax({
					data:data,
					type:"post",
					url:"login.php",
					success:function(dataResult){
						var data=JSON.parse(dataResult);
						if(data.statusCode==400){
							alert("Something Went Wrong.!!\n"+data.msg);
							location.reload();
						}
						else if(data.statusCode==200){
							alert("Password Reset is Successful For "+data.id+".");
							window.location.href="<?php echo $BASE_URL; ?>login.html";
						}
					}
				});
			});
		}

		function containsUppercase(str) {
			return /[A-Z]/.test(str);
		}
		function containsLowercase(str) {
			return /[a-z]/.test(str);
		}
		function hasNumber(myString) {
			return /\d/.test(myString);
		}
		function containsSpecialChars(str) {
			const specialChars = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
			return specialChars.test(str);
		}
</script>