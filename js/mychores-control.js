var rightDate = new Date();
rightDate.setDate(rightDate.getDate()+10);
var latestDate = new Date(rightDate);
var target;


$(function(){
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

    $("#toggle-complete").on('click', function(e){
        if (target.hasClass("complete")){
            target.removeClass("complete");
            $.ajax({
                url: 'php/mark-complete.php',
                type: 'post',
                data: {"ID":target.data("choreid"),"complete":0},
                success:function(response){
                    if (response == "0"){
                        console.log("Marked item incomplete");
                    }
                    else{
                        alert(response);
                        console.log("Failed to mark item incomplete");
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
                        console.log("Marked item complete");
                    }
                    else{
                        alert(response);
                        console.log("Failed to mark item complete");
                    }
                }
            });
        }
    });

    $(document).on('click', function(e){
        if( $(e.target).closest(".modal-content").length > 0 && !$(e.target).hasClass("button")) {
            return false;
        }

        $("#chore-modal").addClass("hidden"); 
    })

    $('#left-scroll').on('click', doLeftScroll);
    $('#right-scroll').on('click', doRightScroll);
});

function doLeftScroll(){
    rightDate.setDate(rightDate.getDate() - 10);
    var newLeftDate = new Date(rightDate);
    newLeftDate.setDate(newLeftDate.getDate() - 20);
    console.log(newLeftDate.toJSON().slice(0,10));

    $('#right-chores').html($('#mid-chores').html());
    $('#mid-chores').html($('#left-chores').html());

    $.ajax({
        url:'./php/get-mychoreitems.php',
        type:'post',
        data:{date: newLeftDate.toJSON().slice(0,10)},
        success:function(html){
            $("#left-chores").html(html);
        }
    });
}

function doRightScroll(){
    var rightContent = $('#right-chores').html();
    var midContent = $('#mid-chores').html();

    $('#mid-chores').html(rightContent);
    $('#left-chores').html(midContent);

    // Update chore items if highest date checked on current page visit
    rightDate.setDate(rightDate.getDate() + 10);
    if (rightDate > latestDate){
        $.ajax({
            url:'./php/gen-choreitems.php',
            type:'post',
            data:{date: rightDate.toJSON().slice(0,10)},
            success:function(response){
                if (response == "0"){
                    $.ajax({
                        url:'./php/get-mychoreitems.php',
                        type:'post',
                        data:{date: rightDate.toJSON().slice(0,10)},
                        success:function(html){
                            $("#right-chores").html(html);
                        }
                    });
                }
                else{
                    alert(response);
                }
            }
        })
        latestDate.setDate(rightDate.getDate());
    }
}