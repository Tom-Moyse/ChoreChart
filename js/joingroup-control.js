$(function (){
    $('#join-form').submit( joinGroup );
    $('#create-form').submit( createGroup );

    $('#join').on('click', function(e){
        e.stopPropagation();
        $('#join-modal').removeClass("hidden");
    });
    $('#create').on('click', function(e){
        e.stopPropagation();
        $('#create-modal').removeClass("hidden");
    });
    $('.modal-content').on('click', function(e){
        e.stopPropagation();
    });
    $(document).on('click', function(e){
        $('#join-modal').addClass("hidden");
        $('#join-form').trigger("reset");
        $('#create-modal').addClass("hidden");
        $('#create-form').trigger("reset");
    });
})

function joinGroup(){
    var coderegex = /^([a-zA-Z0-9])+$/;
    var code = $("input[name='code']").val();

    if (!coderegex.test(code) || code.length != 5){
        $("#cinvalid").removeClass("hidden");
        return false;
    }
    else{
        $("#cinvalid").addClass("hidden");
    }

    // Check validity of group invite via ajax (and add user join request)
    $.ajax({
        url:'./php/process-joingroup.php',
        type:'post',
        data:{code:code},
        success:function(response){
            switch (response) {
                case '0':
                    window.location.href = 'joinstatus.php';
                    break;
                default:
                    error = true;
                case '1':
                    $("#cinvalid").removeClass("hidden");
                    break;
            }
        }
    })

    return false;
}

function createGroup(){
    var nameregex = /^([a-zA-Z0-9_!?,'. -])+$/;
    var name = $("input[name='group']").val();

    if (name != ""){
        $("#nmissing").addClass("hidden");
    }
    else{
        $("#nmissing").removeClass("hidden");
        return false
    }

    if (nameregex.test(name)){
        $("#ninvalid").addClass("hidden");
    }
    else{
        $("#ninvalid").removeClass("hidden");
        return false;
    }

    // Create new group via ajax (and add user to group appropriately)
    $.ajax({
        url:'./php/process-creategroup.php',
        type:'post',
        data:{name:name},
        success:function(response){
            switch (response) {
                case '0':
                    window.location.href = 'chores.php';
                    break;
                default:
                    error = true;
                case '1':
                    $("#ninvalid").removeClass("hidden");
                    break;
            }
        }
    })

    return false;
}
