$(function(){
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

    $("#cs-check").on('change', function(e){
        $("#cs-chorehold").toggleClass("hidden");
    });


    $(document).on('click', function(e){
        if ($(e.target).closest(".modal-content").length > 0){
            return;
        }

        $(".modal").addClass("hidden");
        $(".modal-form").trigger("reset");
        hideAllErrors();
    });
});

function hideAllErrors(){
    $(".chore-error").addClass("hidden");
    $(".date-error").addClass("hidden");
    $(".frequency-error").addClass("hidden");
}

function editSingle(e){
    var id = $(e.currentTarget).parent().data("choreid");

    //Get all values via ajax
    $.ajax({

        success:function(data){
            var result = $.parseJSON(data);
            if (result[0] != "0"){
                alert("Error occured");
            }
            else{
                $("#es-chore").val(result[1]);
                if (result[1] == "0"){
                    $("es-check").prop('checked', true);
                    $("es-chorehold").addClass('hidden');
                }
                else{
                    $("es-check").prop('checked', false);
                    $("es-chorehold").removeClass('hidden');
                    $("es-choreholder").val(result[2]);
                }
                var date = new Date(result[3]);
                $("es-date").val(date);
            }
            
        }
    });

    $("#edit-single-chore").on('click', function(){
        $(".chore-error").addClass("hidden");
        $(".date-error").addClass("hidden");
        $(".frequency-error").addClass("hidden");
        var error = false;
        // Validate inputs
        if ($("#es-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#cr-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-fnum").val() == ""){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-fnum").val() % 1 != 0 || $("#es-fnum").val() < 0){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if (!error){
            $.ajax({
        
            });
            console.log("Editing fixed chore");
        }
    })


}

function editRepeating(e){
    var id = $(e.currentTarget).parent().data("choreid");

    //Get all values via ajax
    $.ajax({

        success:function(data){
            var result = $.parseJSON(data);
            if (result[0] != "0"){
                alert("Error occured");
            }
            else{
                $("#er-chore").val(result[1]);
                if (result[1] == "0"){
                    $("er-check").prop('checked', true);
                    $("er-chorehold").addClass('hidden');
                }
                else{
                    $("er-check").prop('checked', false);
                    $("er-chorehold").removeClass('hidden');
                    $("er-choreholder").val(result[2]);
                }
                var date = new Date(result[3]);
                $("er-date").val(date);

                $("er-fnum").val(result[4]);
                $("er-fval").val(result[5]);
            }
            
        }
    });

    $("#edit-repeating-chore").on('click', function(){
        $(".chore-error").addClass("hidden");
        $(".date-error").addClass("hidden");
        $(".frequency-error").addClass("hidden");
        var error = false;
        // Validate inputs
        if ($("#es-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#cr-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-fnum").val() == ""){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if ($("#es-fnum").val() % 1 != 0 || $("#es-fnum").val() < 0){
            $(".frequency-error").removeClass("hidden");
            error = true;
        }

        if (!error){
            $.ajax({
        
            });
            console.log("Editing repeating chore");
        }
    })
}

function createSingle(e){
    $("#add-single-chore").on('click', function(){
        $(".chore-error").addClass("hidden");
        $(".date-error").addClass("hidden");
        var error = false;
        // Validate inputs
        if ($("#cs-chore").val().trim().length == 0){
            $(".chore-error").removeClass("hidden");
            error = true;
        }

        if ($("#cs-date").val() == ""){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        var date = new Date($("#cs-date").val())
        if (date < Date.now()){
            $(".date-error").removeClass("hidden");
            error = true;
        }

        if (!error){
            $.ajax({
        
            });
            console.log("Adding Single chore");
        }
    });
}

function createRepeating(e){
    $("#add-repeating-chore").on('click', function(){
        $(".chore-error").addClass("hidden");
        $(".date-error").addClass("hidden");
        $(".frequency-error").addClass("hidden");
        var error = false;
        // Validate inputs
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

        if (!error){
            $.ajax({
        
            });
            console.log("Adding Repeating chore");
        }
    });
}

function deleteChore(e){
    var id = $(e.currentTarget).parent().data("choreid");

    // Delete chore via ajax
    $.ajax({
        
    });
    console.log("Deleting chore with id: "+id);
}