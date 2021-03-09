$(function(){
    // Bind all buttons to their relevant modals
    $("#create-single-button").on('click', function(e){
        createSingle(e);
        $("#create-single-modal").removeClass("hidden");
        e.stopPropagation();
    });
    $("#create-repeating-button").on('click', function(e){
        createRepeating(e);
        $("#create-repeating-modal").removeClass("hidden");
        e.stopPropagation();
    });

    // Bind buttons for individual chores to container div such that dynamically created chore rows
    // maintain on click functionality of displaying relevant modal/removing chore
    $("#single-chores").on('click', 'tr .edit-button', function(e){
        editSingle(e);
        $("#edit-single-modal").removeClass("hidden");
        e.stopPropagation();
    });
    $("#repeating-chores").on('click', 'tr .edit-button', function(e){
        editRepeating(e);
        $("#edit-repeating-modal").removeClass("hidden");
        e.stopPropagation();
    });
    $("#prev-chores").on('click', 'tr .delete-button', function(e){
        deleteChore(e);
        e.stopPropagation();
    });
    $("#single-chores").on('click', 'tr .delete-button', function(e){
        deleteChore(e);
        e.stopPropagation();
    });
    $("#repeating-chores").on('click', 'tr .delete-button', function(e){
        deleteChore(e);
        e.stopPropagation();
    });

    // Bind checkboxes in modals to display/hide additional form content based upon their state
    $("#cs-check").on('change', function(e){
        $("#cs-chorehold").toggleClass("hidden");
    });
    $("#cr-check").on('change', function(e){
        $("#cr-chorehold").toggleClass("hidden");
    });
    $("#es-check").on('change', function(e){
        $("#es-chorehold").toggleClass("hidden");
    });
    $("#es-repeats").on('change', function(e){
        $("#es-frequency").toggleClass("hidden");
    });
    $("#er-check").on('change', function(e){
        $("#er-chorehold").toggleClass("hidden");
    });

    // Minimise and reset all modals when any non-modal content is clicked on
    $(document).on('click', function(e){
        if ($(e.target).closest(".modal-content").length > 0){
            return;   
        }

        $(".modal").addClass("hidden");
        $(".modal-form").trigger("reset");
        hideAllErrors();
    });

    choreMessageDisplay();
});

// Helper function to reset all modals/forms
function minimizeModals(){
    $(".modal").addClass("hidden");
    $(".modal-form").trigger("reset");
    hideAllErrors();
    choreMessageDisplay();
}

// Helper function to hide all errors that may be displayed when interacting with forms
function hideAllErrors(){
    $(".chore-error").addClass("hidden");
    $(".date-error").addClass("hidden");
    $(".frequency-error").addClass("hidden");
}

// Handles displaying message in div panel in case of zero relevant chores of said type
function choreMessageDisplay(){
    if ($("#past-chores tr").length == 0){
        $("#no-past-chores").removeClass("hidden");
    }
    else{
        $("#no-past-chores").addClass("hidden");
    }
    if ($("#repeating-chores tr").length == 0){
        $("#no-repeating-chores").removeClass("hidden");
    }
    else{
        $("#no-repeating-chores").addClass("hidden");
    }
    if ($("#single-chores tr").length == 0){
        $("#no-single-chores").removeClass("hidden");
    }
    else{
        $("#no-single-chores").addClass("hidden");
    }
}

// Handles the edit single modal and all relevant functionality
function editSingle(e){
    // Retrieve chore id from parent data
    var id = $(e.currentTarget).parent().data("choreid");
    console.log(id);

    // Get all default form values via ajax post request to fetch-singlechoreinfo in case of non "0"
    // response inform user of error otherwise setup form default values
    $.ajax({
        url: 'php/fetch-singlechoreinfo.php',
        type: 'post',
        data: {"id": id},
        success:function(data){
            var result = $.parseJSON(data);
            if (result[0] != "0"){
                alert("Error handling request, please try again later");
            }
            else{
                $("#es-chore").val(result[1]);
                if (result[2] == "0"){
                    $("#es-check").prop('checked', true);
                    $("#es-chorehold").addClass('hidden');
                }
                else{
                    $("#es-check").prop('checked', false);
                    $("#es-chorehold").removeClass('hidden');
                    $("#es-choreholder").val(result[2]);
                }
                var date = new Date(result[3]);
                $("#es-date").val(date.toJSON().substring(0, date.toJSON().length - 8));
            }
        }
    });

    // Bind function to form submission button using 'one' handler to prevent duplicate submissions 
    $("#edit-single-chore").one('click', function(){
        hideAllErrors();
        var error = false;
        // Validate form inputs
        if ($("#es-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#es-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        if($("#es-repeats").is(":checked")){
            if ($("#es-fnum").val() == ""){
                $(".frequency-error").removeClass("hidden");
                error = true;
            }
    
            if ($("#es-fnum").val() % 1 != 0 || $("#es-fnum").val() <= 0){
                $(".frequency-error").removeClass("hidden");
                error = true;
            }
        }
        
        // If no errors occured make ajax post request to process-editsinglechore with serialized
        // form data. If non "0" response received inform user of error, otherwise check if chore
        // should be moved to repeating chores and handle appropriately
        if (!error){
            $.ajax({
                url: 'php/process-editsinglechore.php',
                type: 'post',
                data: $("#edit-single").serialize()+"&id="+id,
                success:function(data){
                    var result = $.parseJSON(data);
                    if (result[0] != "0"){
                        alert("Error handling request, please try again later");
                    }
                    else{
                        if (result[1]){
                            var html = '<tr>'+$(e.currentTarget).parent().html()+'</tr>';
                            $(e.currentTarget).parent().remove();
                            $("#repeating-chores").append(html);
                            $("#repeating-chores tr:last-child").data("choreid", id);
                        }
                        minimizeModals();
                    }
                }
            });
        }
    })


}

// Handles the edit repeating modal and all relevant functionality
function editRepeating(e){
    // Retrieve chore id from parent data
    var id = $(e.currentTarget).parent().data("choreid");

    // Get all default form values via ajax post request to fetch-repeatingchoreinfo in case of non "0"
    // response inform user of error otherwise setup form default values
    $.ajax({
        url: 'php/fetch-repeatingchoreinfo.php',
        type: 'post',
        data: {"id": id},
        success:function(data){
            var result = $.parseJSON(data);
            if (result[0] != "0"){
                alert("Error handling request, please try again later");
            }
            else{
                $("#er-chore").val(result[1]);
                if (result[2] == "0"){
                    $("#er-check").prop('checked', true);
                    $("#er-chorehold").addClass('hidden');
                }
                else{
                    $("#er-check").prop('checked', false);
                    $("#er-chorehold").removeClass('hidden');
                    $("#er-choreholder").val(result[2]);
                }
                var date = new Date(result[3]);
                $("#er-date").val(date.toJSON().substring(0, date.toJSON().length - 8));

                var num = result[4].match(/\d+/)[0]
                $("#er-fnum").val(num);
                if (result[4].includes("days")){
                    $("#er-fval").val("days");
                }
                else if(result[4].includes("weeks")){
                    $("#er-fval").val("weeks");
                }
                else if(result[4].includes("months")){
                    $("#er-fval").val("months");
                }
                else{
                    $("#er-fval").val("years");
                }
                
                // Can only adjust frequency if start date in future
                if (new Date < date){
                    $("er-fnum").prop("disabled", false);
                    $("er-fval").prop("disabled", false);
                }
                else{
                    $("er-fnum").prop("disabled", true);
                    $("er-fval").prop("disabled", true);
                }
            }
            
        }
    });

    // Bind function to form submission button using 'one' handler to prevent duplicate submissions
    $("#edit-repeating-chore").one('click', function(){
        hideAllErrors();
        var error = false;
        // Validate all inputs
        if ($("#er-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#er-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#er-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        if ($("#er-fnum").val() == ""){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if ($("#er-fnum").val() % 1 != 0 || $("#er-fnum").val() <= 0){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        // If no errors occured make ajax post request to process-editsinglechore with serialized
        // form data. If non "0" response received inform user of error.
        if (!error){
            $.ajax({
                url: 'php/process-editrepeatingchore.php',
                type: 'post',
                data: $("#edit-repeating").serialize()+"&id="+id,
                success:function(response){
                    if (response == "0"){
                        minimizeModals();
                    }
                    else{
                        alert("Error handling request, please try again later")
                    }
                }
            });
        }
    })
}

// Handles the create single modal and all relevant functionality
function createSingle(e){
    // Bind function to form submission button using 'one' handler to prevent duplicate submissions
    $("#add-single-chore").one('click', function(){
        $(".chore-error").addClass("hidden");
        $(".date-error").addClass("hidden");
        var error = false;
        // Validate form inputs
        if ($("#cs-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#cs-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#cs-date").val());
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        // If no errors occured make ajax post request to process-createsinglechore with serialized
        // form data. If non "0" response received inform user of error. Otherwise, update html,
        // to display new chore.
        if (!error){
            $.ajax({
                url: 'php/process-createsinglechore.php',
                type: 'post',
                data: $('#create-single').serialize(),
                success: function(data){
                    var result = $.parseJSON(data);
                    if (result[0] == "0"){
                        $("#single-chores").append(result[2]);
                        $("#single-chores tr:last-child").data("choreid",result[1]);
                        minimizeModals();
                    }
                    else{
                        alert("Error handling request, please try again later");
                    }
                },
            });
        }
    });
}

// Handles the create repeating modal and all relevant functionality
function createRepeating(e){
    // Bind function to form submission button using 'one' handler to prevent duplicate submissions
    $("#add-repeating-chore").one('click', function(){
        hideAllErrors()
        var error = false;
        // Validate form inputs
        if ($("#cr-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#cr-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#cr-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }
        console.log($("#cr-fnum").val());
        if ($("#cr-fnum").val() == ""){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if ($("#cr-fnum").val() % 1 != 0 || $("#cr-fnum").val() < 0){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        // If no errors occured make ajax post request to process-createrepeatingchore with serialized
        // form data. If non "0" response received inform user of error. Otherwise, update html,
        // to display new chore.
        if (!error){
            $.ajax({
                url: 'php/process-createrepeatingchore.php',
                type: 'post',
                data: $('#create-repeating').serialize(),
                success: function(data){
                    var result = $.parseJSON(data);
                    if (result[0] == "0"){
                        $("#repeating-chores").append(result[2]);
                        $("#repeating-chores tr:last-child").data("choreid",result[1]);
                        minimizeModals();
                    }
                    else{
                        alert("Error handling request, please try again later");
                    }
                },
            });
        }
    }); 
}

// Handles chore deletion
function deleteChore(e){
    // Retrieve chore id from parent data
    var id = $(e.currentTarget).parent().data("choreid");

    // Make ajax post request to process-deletechore, If non "0" response received inform user of 
    // error. Otherwise, update html, to remove deletedd chore.
    $.ajax({
        url: 'php/process-deletechore.php',
        type: 'post',
        data: {id: id},
        success:function(response){
            if (response == "0"){
                console.log("Deleted chore with id: "+id);
                $(e.currentTarget).parent().remove();
            }
            else{
                alert("Error handling request, please try again later");
            }
            choreMessageDisplay();
        }
    });
}