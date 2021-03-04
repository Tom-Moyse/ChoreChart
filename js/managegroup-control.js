$(function(){
    $('#name-form').submit( createGroup );

    $("#display-join").on('click', function(e){
        $("#join-code-modal").removeClass('hidden');
        e.stopPropagation();
    });
    $("#edit-name").on('click', function(e){
        $("#edit-name-modal").removeClass('hidden');
        e.stopPropagation();
    });
    $("#mgroupmembers").on('click', 'tr .magnify svg', function(e){
        configUserModal(e);
        $("#user-modal").removeClass('hidden');
        e.stopPropagation();
    });


    $("#toggle-mod").on('click', function(e){
        if ($("#toggle-mod").hasClass("disabled")){
            return;
        }
        var id = $("#user-button-group").data('uid');
        if ($("#user-modstatus").text() == "Yes"){
            var mod = 0;
        }
        else{
            var mod = 1;
        }

        $.ajax({
            url: 'php/update-mod-status.php',
            type: 'post',
            data: {"uid": id, "moderator": mod},
            success:function(result){
                if (result != "0"){
                    alert("Status update failed");
                }       
            }
        });

        if (mod == 0){
            $('td[data-uid="'+id+'"]').parent().children(":first").html("");
        }
        else{
            $('td[data-uid="'+id+'"]').parent().children(":first").html('<embed src="img/crown.svg" alt="Mod Icon">');
        }
        
    });

    $("#kick").on('click', function(e){
        if ($("#kick").hasClass("disabled")){
            return;
        }
        var id = $("#user-button-group").data('uid');

        $.ajax({
            url: 'php/update-mod-status.php',
            type: 'post',
            data: {"uid": id, "moderator": 0},
        });

        $.ajax({
            url: 'php/remove-from-group.php',
            type: 'post',
            data: {"uid": id},
            success:function(result){
                if (result != "0"){
                    alert("Removal failed");
                }       
            }
        });

        $('td[data-uid="'+id+'"]').parent().remove();
    });

    $("#accept-button").on('click', function(e){
        var id = $(e.currentTarget).parent().data("joinid");

        $.ajax({
            url: 'php/process-joinrequest.php',
            type: 'post',
            data: {"reqID":id, "add":1},
            success:function(data){
                var result = $.parseJSON(data);
                if (result[0] != "0"){
                    alert ("Add failed");
                }
                else{
                    $.ajax({
                        url: 'php/gen-memberrow.php',
                        type: 'post',
                        data: {"uid": result[1]},
                        success:function(html){
                            $('#mgroupmembers').append(html);
                        }
                    });
                }
            }
        });

        $(e.currentTarget).parent().fadeOut(300, function(){this.remove();});

    });

    $("#decline-button").on('click', function(e){
        var id = $(e.currentTarget).parent().data("joinid");

        $.ajax({
            url: 'php/process-joinrequest.php',
            type: 'post',
            data: {"reqID":id, "add":0},
            success:function(data){
                var result = $.parseJSON(data);
                if (result[0] != "0"){
                    alert ("Decline failed");
                }
            }
        });

        $(e.currentTarget).parent().fadeOut(300, function(){this.remove();});
    });

    $(document).on('click', function(e){
        if( $(e.target).closest(".modal-content").length > 0) {
            if (!$(e.target).hasClass("button") && $(e.target).attr("type") != "submit"){
                return;
            }     
        }
        if ($(e.target).hasClass("button") && $(e.target).hasClass("disabled")){
            return;
        }
        $("#join-code-modal").addClass('hidden');
        $("#user-modal").addClass('hidden');
        $("#edit-name-modal").addClass('hidden');
    });
});

function configUserModal(e){
    var id = $(e.currentTarget).parent().data('uid');
    
    $("#user-button-group").data('uid',id);

    $.ajax({
        url: 'php/verify-uid.php',
        type: 'post',
        data: {"uid": id},
        success:function(data){
            var result = $.parseJSON(data);
            
            if (result[0] == "0"){
                $.get("img/usr/"+id+".jpeg")
                    .done(function(){
                        $("#user-img").attr("src","img/usr/"+id+".jpeg");
                    })
                    .fail(function(){
                        $("#user-img").attr("src","img/usr/default.png");
                    })
                $("#user-name").text(result[1]);
                if (result[2] == 1){
                    $("#user-modstatus").text("Yes"); 
                }
                else{
                    $("#user-modstatus").text("No"); 
                }

                // Disable/enable buttons dependent on if selected user is current user
                $.ajax({
                    url: 'php/check-isself.php',
                    type: 'post',
                    data: {"uid": id},
                    success:function(result){
                        if (result == "0"){
                            $("#toggle-mod").addClass("disabled");
                            $("#kick").addClass("disabled");
                        }
                        else{
                            $("#toggle-mod").removeClass("disabled");
                            $("#kick").removeClass("disabled");
                        }
                    }
                });
            }
            else{
                alert("uid invalid");
            }
        }
    });
}

function createGroup(){
    var nameregex = /^([a-zA-Z0-9_!?,'. -])+$/;
    var name = $("input[name='group']").val();
    var gid = $("input[name='gid']").val();

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
    
    // Rename group via ajax
    $.ajax({
        url:'./php/process-renamegroup.php',
        type:'post',
        data:{"name":name, "gid":gid},
        success:function(response){
            switch (response) {
                case '0':
                    $("#title-container h3").text(name);
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