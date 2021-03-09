// Store current date + 10 days
var rightDate = new Date();
rightDate.setDate(rightDate.getDate()+10);
var latestDate = new Date(rightDate);
var target;


$(function(){
    // Bind chore element to info popup and handle filling modal with relevant information as well
    // as positioning popup appropriately
    $("#chore-container").on('click', '.chores table tbody tr td ul .chore-element', function(e){
        target = $(e.target);

        $("#chore-modal").removeClass("hidden")

        $("#cdesc").text(target.data("contents"));
        $("#cdate").text(target.data("deadline"));

        if (target.hasClass("complete")){
            $("#toggle-complete").text("Mark as Incomplete");
        }
        else{
            $("#toggle-complete").text("Mark as Complete");
        }

        e.stopPropagation();
    });

    // Bind complete button to relevant functionality
    $("#toggle-complete").on('click', function(e){
        // Depending on current complete status make ajax post request to mark-complete with the
        // relevant complete variable. In case of non "0" response an error is displayed to the user
        // Otherwise, the updateActiveChore function is called
        if (target.hasClass("complete")){
            target.removeClass("complete");
            $.ajax({
                url: 'php/mark-complete.php',
                type: 'post',
                data: {"ID":target.data("choreid"),"complete":0},
                success:function(response){
                    if (response == "0"){
                        updateActiveChore();
                    }
                    else{
                        alert("Error toggling complete status, please try again later");
                    }
                }
            });
        }
        else{
            target.addClass("complete");
            $.ajax({
                url: 'php/mark-complete.php',
                type: 'post',
                data: {"ID":target.data("choreid"),"complete":1},
                success:function(response){
                    if (response == "0"){
                        updateActiveChore();
                    }
                    else{
                        alert("Error toggling complete status, please try again later");
                    }
                }
            });
        }
    });

    // Minimise and reset all modals when any non-modal content is clicked on
    $(document).on('click', function(e){
        if( $(e.target).closest(".modal-content").length > 0 && !$(e.target).hasClass("button")) {
            return false;
        }

        $("#chore-modal").addClass("hidden"); 
    })

    $('#left-scroll').on('click', doLeftScroll);
    $('#right-scroll').on('click', doRightScroll);
});

// Do left scroll cycles contents of divs to the right and requests contents of left div
function doLeftScroll(){
    // Update global date varaiables and get new start date for left table
    rightDate.setDate(rightDate.getDate() - 10);
    var newLeftDate = new Date(rightDate);
    newLeftDate.setDate(newLeftDate.getDate() - 20);
    console.log(newLeftDate.toJSON().slice(0,10));

    $('#right-chores').html($('#mid-chores').html());
    $('#mid-chores').html($('#left-chores').html());

    // Request choreitems for the left table and set to the response value
    $.ajax({
        url:'./php/get-mychoreitems.php',
        type:'post',
        data:{date: newLeftDate.toJSON().slice(0,10)},
        success:function(html){
            $("#left-chores").html(html);
        }
    });
}

// Do right scroll cycles contents of divs to the left and requests contents of right div
function doRightScroll(){
    var rightContent = $('#right-chores').html();
    var midContent = $('#mid-chores').html();

    $('#mid-chores').html(rightContent);
    $('#left-chores').html(midContent);

    // Update chore items if highest date checked on current page visit
    rightDate.setDate(rightDate.getDate() + 10);


    // Send post request to gen-mychoreitems with the given date on non "0" response inform user
    // of error otherwise make post request to get-mychoreitems with the given date and on success
    // Set right table
    $.ajax({
        url:'./php/gen-choreitems.php',
        type:'post',
        data:{date: rightDate.toJSON().slice(0,10)},
        success:function(response){
            if (response != "0"){
                alert("Error retrieving chore items, please try again later");
            }
            else{
                $.ajax({
                    url:'./php/get-choreitems.php',
                    type:'post',
                    data:{date: rightDate.toJSON().slice(0,10)},
                    success:function(html){
                        $("#right-chores").html(html);
                    }
                });
            }
        }
    })
    latestDate.setDate(rightDate.getDate());
}

// Function makes ajax get request to get-activechore and can subsequently set the active chore
// on the webpage to the html response.
function updateActiveChore(){
    $.ajax({
        url: 'php/get-activechore.php',
        type: 'get',
        success:function(html){
            $("#chore-notif").html(html);
        }
    });
}