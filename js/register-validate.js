$(document).ready(function (){
    $('form').submit( validateForm );
})

function validateForm(){
    var error = false;
    var uname = $("input[name='username']").val();
    var email = $("input[name='email']").val();
    var pass1 = $("input[name='password']").val();
    var pass2 = $("input[name='password-confirm']").val();
    var emailregex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var unameregex = /^([a-zA-Z0-9_-])+$/;
    var passregex = /^\S*$/

    $("#utaken").addClass("hidden");
    $("#etaken").addClass("hidden");

    if (uname != ""){
        $("#umissing").addClass("hidden");
    }
    else{
        $("#umissing").removeClass("hidden");
        error = true;
    }

    if (email != ""){
        $("#emissing").addClass("hidden");
    }
    else{
        $("#emissing").removeClass("hidden");
        error = true;
    }

    if (pass1 != ""){
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

    if (pass1 == pass2){
        $("#pmatch").addClass("hidden");
    }
    else{
        $("#pmatch").removeClass("hidden");
        error = true;
    }


    if (pass1.length >= 8){
        $("#pshort").addClass("hidden");
    }
    else{
        $("#pshort").removeClass("hidden");
        error = true;
    }

    if (unameregex.test(uname)){
        $("#uinvalid").addClass("hidden");
    }
    else{
        $("#uinvalid").removeClass("hidden");
        error = true;
    }

    if (emailregex.test(email)){
        $("#einvalid").addClass("hidden");
    }
    else{
        $("#einvalid").removeClass("hidden");
        error = true;
    }

    if (passregex.test(pass1)){
        $("#pinvalid").addClass("hidden");
    }
    else{
        $("#pinvalid").removeClass("hidden");
        error = true;
    }

    // Dont attempt to register if fields were invalid
    if (error){
        return false;
    }

    // Check if fields taken via AJAX here
    $.ajax({
        url:'./php/process-register.php',
        type:'post',
        data:{username:uname, email:email, password1:pass1, password2:pass2},
        success:function(response){
            switch (response) {
                case '0':
                    window.location.href = 'joingroup.php';
                    break;
                default:
                    error = true;
                case '1':
                    $("#utaken").removeClass("hidden");
                    break;
                case '2':
                    $("#etaken").removeClass("hidden");
                    break;
            }
        }
    })

    return false;
}