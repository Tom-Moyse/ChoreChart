$(function(){
    // Bind form submits to relevant function
    $('#name-form').submit( renameGroup );

    // Bind all buttons to their relevant modals
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

    // Bind toggle mod button to relevant functionality
    $("#toggle-mod").on('click', function(e){
        // Check if button disabled, in which case ignore event
        if ($("#toggle-mod").hasClass("disabled")){
            return;
        }
        // Get id of user from data
        var id = $("#user-button-group").data('uid');
        // Set mod variable appropriately
        if ($("#user-modstatus").text() == "Yes"){
            var mod = 0;
        }
        else{
            var mod = 1;
        }

        // Make ajax post request to update-mod-status with the uid and mod var. In case of non "0"
        // response inform user of error
        $.ajax({
            url: 'php/update-mod-status.php',
            type: 'post',
            data: {"uid": id, "moderator": mod},
            success:function(result){
                if (result != "0"){
                    alert("Toggle moderator request failed, please try again later");
                }       
            }
        });

        // Update html to show/hide mod crown icon
        if (mod == 0){
            $('td[data-uid="'+id+'"]').parent().children(":first").html("");
        }
        else{
            $('td[data-uid="'+id+'"]').parent().children(":first").html('<embed src="img/crown.svg" alt="Mod Icon">');
        }
        
    });

    // Bind kick button to relevant functionality
    $("#kick").on('click', function(e){
        // Check if button disabled, in which case ignore event
        if ($("#kick").hasClass("disabled")){
            return;
        }
        // Get id of user from data
        var id = $("#user-button-group").data('uid');

        // Unmod the user prior to them being removed from the group via ajax post request to
        // update-mod-status
        $.ajax({
            url: 'php/update-mod-status.php',
            type: 'post',
            data: {"uid": id, "moderator": 0},
        });

        // Remove the user from the group via an ajax post request to remove-from-group and in case
        // of non "0" response inform user of error.
        $.ajax({
            url: 'php/remove-from-group.php',
            type: 'post',
            data: {"uid": id},
            success:function(result){
                if (result != "0"){
                    alert("Failed to remove user, please try again later");
                }       
            }
        });

        // Update relevant html - removing user from list of members
        $('td[data-uid="'+id+'"]').parent().remove();
    });

    // Bind accept button to relevant functionality
    $("#accept-button").on('click', function(e){
        // Get id of user from data
        var id = $(e.currentTarget).parent().data("joinid");

        // Make ajax post request to process-joinrequest and in case of non "0" response inform user
        // of error. Otherwise, make a second ajax post request to gen-memberrow, using response
        // from first request. Upon success add user to list of members with the html response
        // returned.
        $.ajax({
            url: 'php/process-joinrequest.php',
            type: 'post',
            data: {"reqID":id, "add":1},
            success:function(data){
                var result = $.parseJSON(data);
                if (result[0] != "0"){
                    alert ("Failed to add user, please try again later");
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
        // Fade out join request panel row
        $(e.currentTarget).parent().fadeOut(300, function(){this.remove();});
    });

    // Bind decline button to relevant functionality
    $("#decline-button").on('click', function(e){
        // Get id of user from data
        var id = $(e.currentTarget).parent().data("joinid");

        // Make ajax post request to process-joinrequest and in case of non "0" response inform user
        // of error.
        $.ajax({
            url: 'php/process-joinrequest.php',
            type: 'post',
            data: {"reqID":id, "add":0},
            success:function(data){
                var result = $.parseJSON(data);
                if (result[0] != "0"){
                    alert ("Failed to decline request, please try again later");
                }
            }
        });
        // Fade out join request panel row
        $(e.currentTarget).parent().fadeOut(300, function(){this.remove();});
    });

    // Minimise and reset all modals when any non-modal content is clicked on
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

// Function handles user modal and associated functionality
function configUserModal(e){
    // Get id of user from parent data
    var id = $(e.currentTarget).parent().data('uid');
    // Store uid within modal
    $("#user-button-group").data('uid',id);

    // Make ajax request to verify-uid and in case of non "0" response an error is displayed.
    // Otherwise, attempts to read the address of the user pfp is made. If this is successful user 
    // png image source is set appropriately, otherwise image src is set to default profile picture.
    // Modstatus is also received from response and used to set user mod status as appropriate. 
    // Lastly an ajax post request is made to check-isself and dependent upon result the modal
    // buttons are enabled/disabled
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
                alert("Error retrieving user information, please try again later.");
            }
        }
    });
}

// Handles rename form and functionality
function renameGroup(){
    // Validates group name input and displays error if invalid
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
    
    // Ajax post request is made to renamegroup and if successful the group name is updated.
    // If unseccessful error message is displayed to user.
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