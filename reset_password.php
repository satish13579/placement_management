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

                    <span class="input-group-text"><i class="fas fa-key"></i></span>

                    <input id="p1" type="password" name="p1" class="form-control input_user" value="" placeholder="Enter a New Password" required>
                </div>
                <div class="input-group mb-2">

                    <span class="input-group-text"><i class="fas fa-key"></i></span>

                    <input id="p2" type="text" name="p2" class="form-control input_pass" value="" placeholder="Retype Your Password" required>

                </div>
                <div style="display:none;" class="req">
                    <p id='head'>Password Must FulFill Below Requirements :</p>
                    <p id='len'>Length 8 - 15 Characters <span id='splen'></span></p>
                    <p id='lower'>1 lowercase Character <span id='splower'></span></p>
                    <p id='upper'>1 UpperCase Character <span id='spupper'></span></p>
                    <p id='number'>1 Number <span id='spnumber'></span></p>
                    <p id='special'>1 special Character <span id='spspecial'></span></p>
                </div>
                <div style="display:none;" class="passmatch">
                    <p style="color:red;" id="pmatch">Both Passwords Should Match</p>
                </div>

                <p id='data' style="display:none" data-p1="0" data-p2="0"></p>
                <div class="d-flex justify-content-center mt-3 login_container">
                    <button style="margin-top: 22px;" type="button" name="button" id="resetpassword" class="btn login_btn">Reset Password</button>
                </div>
                <input type="hidden" name="type" value="2"></input>
                <input type="hidden" name="salt" value="<?php echo $salt; ?>"></input>
            </form>
        <?php
        }

        ?>
    </div>

    <div class="mt-4">
        <div class="d-flex justify-content-center links">
            <a href="http://localhost/mid-automation/log_in.php">Go to Log In</a>
        </div>
    </div>
</div>