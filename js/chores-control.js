// Store current date + 10 days
var rightDate = new Date();
rightDate.setDate(rightDate.getDate()+10);
var latestDate = new Date(rightDate);


$(function(){
    // Bind chore element to info popup and handle filling popup with relevant information as well
    // as positioning popup appropriately
    $("#chore-container").on('click', '.chores table tbody tr td ul .chore-element', function(e){
        var target = $(e.target);
        var pos = target.offset();
        var width = target.width();
        pos.left += width;
        $("#info-popup").removeClass("hidden");  
        $("#info-popup").css(pos);
        $("#cname").text(target.data("choreholder"));
        $("#cdesc").text(target.data("contents"));
        $("#cdate").text(target.data("deadline"));
        if (target.hasClass("complete")){
            $("#ccom").text("✓");
            $("#ccom").css("color","green");
        }
        else{
            $("#ccom").text("✖");
            $("#ccom").css("color","red");
        }
        
        e.stopPropagation();
    });

    // Mnimise popup when any non-popup content is clicked on
    $(document).on('click', function(e){
        if( $(e.target).closest("#info-popup").length > 0 ) {
            return false;
        }
        $("#info-popup").addClass("hidden"); 
    })

    // Bind appropriate functions to the left and right buttons
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
        url:'./php/get-choreitems.php',
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


    // Send post request to gen-choreitems with the given date on non "0" response inform user
    // of error otherwise make post request to get-choreitems with the given date and on success
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