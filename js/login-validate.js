$(function (){
    // Bind form submits to relevant function
    $('form').submit( validateForm );
})

// Handles login form and relevant functionality
function validateForm(){
    // Verify form contents meet criteria, if not display relevant errors
    var error = false;
    var uename = $("input[name='uname-email']").val();
    var pass = $("input[name='password']").val();

    $("#match").addClass("hidden");

    if (uename != ""){
        $("#uemissing").addClass("hidden");
    }
    else{
        $("#uemissing").removeClass("hidden");
        error = true;
    }

    if (pass != ""){
        $("#pmissing").addClass("hidden");
    }
    else{
        $("#pmissing").removeClass("hidden");
        error = true;
    }

    // Dont perform additional checks if any fields were left empty
    if (error){
        return false;
    }

    // Attempt to login here via AJAX
    $.ajax({
        url:'./php/process-login.php',
        type:'post',
        data:{user:uename, pass:pass},
        success:function(response){
            if (response == 0){
                window.location.href = "./php/routepage.php";
            }
            else{
                $("#match").removeClass("hidden");
            }
        }
    })
    return false;
}