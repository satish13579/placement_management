

$(document).on('click','#btn-add',function(e) { 
    var form = document.querySelector("#user_form");
    if(!form.checkValidity()){
        var sbtn=document.querySelector("#btn-add-sub");
        sbtn.click();
    }
    else{
    var data = $("#user_form").serialize();
    $.ajax({
        data: data,
        type: "post",
        url: "student_actions.php",
        success: function(dataResult){
                var dataResult = JSON.parse(dataResult);
                if(dataResult.statusCode==200){
                    $('#addStudent').modal('hide');
                    alert('Data added successfully !'); 
                    window.location = window.location.href;						
                }
                else if(dataResult.statusCode==400){
                   alert(dataResult.err);
                }
        }
    });
}
});

//Function to add data in update form
$(document).on('click','.update',function(e) {
    var rollno=$(this).attr("data-id");
    var firstname=$(this).attr("data-firstname");
    var lastname=$(this).attr("data-lastname");
    var dob=$(this).attr("data-dob");
    var email=$(this).attr("data-email");
    var deptid=$(this).attr("data-deptid");
    var passoutyear=$(this).attr("data-passoutyear");
    $('#roll_no_u').val(rollno);
    $('#first_name_u').val(firstname);
    $('#last_name_u').val(lastname);
    $('#dob_u').val(dob);
    $('#dept_id_u').val(deptid);
    $('#email_u').val(email);
    $('#passout_year_u').val(passoutyear);
});

//Funtion to Update the data into the db
$(document).on('click','#update',function(e) {
    var form = document.querySelector("#update_form");
    if(!form.checkValidity()){
        var sbtn=document.querySelector("#update-sub");
        sbtn.click();
    }
    else{
        var data = $("#update_form").serialize();
        $.ajax({
            data: data,
            type: "post",
            url: "student_actions.php",
            success: function(dataResult){
                    var dataResult = JSON.parse(dataResult);
                    if(dataResult.statusCode==200){
                        $('#editStudent').modal('hide');
                        alert('Data updated successfully !'); 
                        window.location = window.location.href;						
                    }
                    else if(dataResult.statusCode==400){
                       alert(dataResult.err);
                    }
            }
        });
    }
});

//Function on clicking single entry delete icon
$(document).on("click", ".delete", function() { 
    var rollno=$(this).attr("data-id");
    $('#roll_no_d').val(rollno);
    
});

//Function executed when delete confirmation is given for single entry 
$(document).on("click", "#delete", function() { 
    $.ajax({
        url: "student_actions.php",
        type: "POST",
        cache: false,
        data:{
            type:'student_single_delete',
            roll_no: $("#roll_no_d").val()
        },
        success: function(dataResult){
            var dataResult = JSON.parse(dataResult);
                    if(dataResult.statusCode==200){
                
                alert('Data Deleted successfully !');
                window.location = window.location.href;
                    }
                    else if(dataResult.statusCode==400){
                        alert(dataResult.err);
                    }
        }
    });
});

//Function Executed When Delete button for multiple files pressed
$(document).on("click", "#delete_multiple", function() {
    var user = [];
    $(".user_checkbox:checked").each(function() {
        user.push($(this).data('user-id'));
    });
    if(user.length <=0) {
        alert("Please select records."); 
    } 
    else { 
        WRN_PROFILE_DELETE = "Are you sure you want to delete "+(user.length>1?"these":"this")+" row?";
        var checked = confirm(WRN_PROFILE_DELETE);
        if(checked == true) {
            var selected_values = user.join(",");
            console.log(selected_values);
            $.ajax({
                type: "POST",
                url: "student_actions.php",
                cache:false,
                data:{
                    type: 'student_multiple_delete',
                    roll_nos : user
                },
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    if(dataResult.statusCode==200){
                    alert('Selected Data Deleted successfully !'); 
                    window.location = window.location.href;
                    }else if(dataResult.statusCode==400){
                        alert(dataResult.err);
                    }
                } 
            }); 
        }  
    } 
});

$(document).on("click", ".regenerate", function() {
    $.ajax({
        type: "POST",
        url: "student_actions.php",
        cache:false,
        data:{
            type: 'student_reset_generate',
            roll_no : $(this).attr("data-roll")
        },
        success: function(dataResult) {
            var dataResult = JSON.parse(dataResult);
            if(dataResult.statusCode==200){
            alert('Password Reset Email has sent to '+dataResult.email); 
            window.location = window.location.href;
            }else if(dataResult.statusCode==400){
                alert(dataResult.msg);
            }
        } 
    }); 
});


$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    var checkbox = $('table tbody input[type="checkbox"]');
    $("#selectAll").click(function(){
        if(this.checked){
            checkbox.each(function(){
                this.checked = true;                        
            });
        } else{
            checkbox.each(function(){
                this.checked = false;                        
            });
        } 
    });
    checkbox.click(function(){
        if(!this.checked){
            $("#selectAll").prop("checked", false);
        }
    });
});

