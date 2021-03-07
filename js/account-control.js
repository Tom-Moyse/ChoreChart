$(function(){
    $('#displayname-form').submit( changeName );
    $('#email-form').submit( changeEmail );
    $('#password-form').submit( changePassword );

    $("#edit-displayname").on('click', function(e){
        $("#edit-displayname-modal").removeClass('hidden');
        e.stopPropagation();
    });

    $("#edit-email").on('click', function(e){
        $("#edit-email-modal").removeClass('hidden');
        e.stopPropagation();
    });

    $("#change-pass").on('click', function(e){
        $("#edit-password-modal").removeClass('hidden');
        e.stopPropagation();
    });

    $("#user-img").on('click', function(e){
        $("#upload-image-modal").removeClass('hidden');
        e.stopPropagation();

        $("#confirm-upload").one('click', function(e){
            handleUpload();
            e.stopPropagation();
        });
    });

    $("#leave-group").on('click', function(e){
        $("#leave-confirm-modal").removeClass('hidden');
        e.stopPropagation();
    });

    $("#confirm-leave").on('click', function(e){
        window.location.href = "php/leave-group.php";
        e.stopPropagation();
    });


    $(document).on('click', function(e){
        if( $(e.target).closest(".modal-content").length > 0) {
            return;     
        }
        if ($(e.target).attr("type") == "submit"){
            return;
        }

        $(".modal").addClass('hidden');
        $(".warning").addClass('hidden');
        $("#displayname-form").trigger('reset');
        $("#email-form").trigger('reset');
        $("#password-form").trigger('reset');
    });
});

function changeName(){
    var name = $("#displayname-input").val();
    if (name != ""){
        $("#nmissing").addClass("hidden");
    }
    else{
        $("#nmissing").removeClass("hidden");
        return false;
    }

    $.ajax({
        url: 'php/process-updatedname.php',
        type: 'post',
        data: {'name': name},
        success:function(response){
            if (response == "0"){
                $("#displayname-input").attr("value",name);
                $("#user-displayname").text(name);
                $("#edit-displayname-modal").addClass('hidden');
            }
            else{
                alert("Error changing displayname please try again later");
            }
        }
    });
    return false;
}

function changeEmail(){
    var emailregex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var email = $("#email-input").val();
    if (email != ""){
        $("#emissing").addClass("hidden");
    }
    else{
        $("#emissing").removeClass("hidden");
        return false;
    }
    if (emailregex.test(email)){
        $("#einvalid").addClass("hidden");
    }
    else{
        $("#einvalid").removeClass("hidden");  
        return false;   
    }
    $("#etaken").addClass("hidden");

    $.ajax({
        url: 'php/process-updateemail.php',
        type: 'post',
        data: {'email': email},
        success:function(response){
            if (response == "0"){
                $("#email-input").attr("value",email);
                $("#user-email").text(email);
                $("#edit-email-modal").addClass('hidden');
            }
            else{
                $("#etaken").removeClass("hidden");
            }
        }
    });
    return false;
}

function changePassword(){
    var pass1 = $("#password-input").val();
    var pass2 = $("#password-confirm-input").val();
    var passregex = /^\S*$/
    var error = false;

    if (pass1 != ""){
        $("#pmissing").addClass("hidden");
    }
    else{
        $("#pmissing").removeClass("hidden");
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

    if (passregex.test(pass1)){
        $("#pinvalid").addClass("hidden");
    }
    else{
        $("#pinvalid").removeClass("hidden");
        error = true;
    }

    if (error){
        return false;
    }

    $.ajax({
        url: 'php/process-updatepass.php',
        type: 'post',
        data: {'pass1': pass1, 'pass2': pass2},
        success:function(response){
            if (response == "0"){
                $("#edit-password-modal").addClass('hidden');
            }
            else{
                alert("Error changing password please try again later");
            }
        }
    });
    return false;
}

function handleUpload(){
    if ($("#avatar").get(0).files.length === 0){
        $("#fmissing").removeClass('hidden');
        return;
    }
    else{
        $("#fmissing").addClass('hidden');
    }
    if ($("#avatar").get(0).files[0].size > 500000){
        $("#fsize").removeClass('hidden');
        return;
    }
    else{
        $("#fsize").addClass('hidden');
    }


    var filename = $("#avatar").val();
    var ext = filename.split('.').pop().toLowerCase();
    if (ext != "jpg" && ext != "jpeg"){
        $("#fmissing").removeClass('hidden');
        return;
    }
    else{
        $("#fmissing").addClass('hidden');
    }

    var file_data = $('#avatar').prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);

    $.ajax({
        url: "php/uploadimg.php",
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success:function(data){
            alert(data);
            var result = $.parseJSON(data);
            console.log("File uploaded");
            if (result[0] == "0"){
                $('#avatar').attr('src', result[1]);
            }
            else{
                alert("Error uploading file, please try again later");
            }
        },
        error:function(response){
            alert(response);
            console.log("File not uploaded")
        }
    });
}