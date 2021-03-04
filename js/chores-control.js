var rightDate = new Date();
rightDate.setDate(rightDate.getDate()+10);
var latestDate = new Date(rightDate);


$(function(){
    $(".chore-element").on('click', function(e){
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

    $(document).on('click', function(e){
        if( $(e.target).closest("#info-popup").length > 0 ) {
            return false;
        }
        $("#info-popup").addClass("hidden"); 
    })

    $('#left-scroll').on('click', doLeftScroll);
    $('#right-scroll').on('click', doRightScroll);
});

function updateChoreItems(){
    $.ajax({
        url:'./php/gen-choreitems.php',
        type:'post',
        data:{date: rightDate.toJSON().slice(0,10)},
        success:function(response){
            if (response == "0"){
                console.log("Update items successfully");
            }
            else{
                alert(response);
                console.log("Update items failed");
            }
        }
    })
}

function doLeftScroll(){
    rightDate.setDate(rightDate.getDate() - 10);
    var newLeftDate = new Date(rightDate);
    newLeftDate.setDate(newLeftDate.getDate() - 20);
    console.log(newLeftDate.toJSON().slice(0,10));

    $('#right-chores').html($('#mid-chores').html());
    $('#mid-chores').html($('#left-chores').html());

    $.ajax({
        url:'./php/get-choreitems.php',
        type:'post',
        data:{date: newLeftDate.toJSON().slice(0,10)},
        success:function(html){
            $("#left-chores").html(html);
        }
    });
}

function doRightScroll(){
    // Update chore items if highest date checked on current page visit
    rightDate.setDate(rightDate.getDate() + 10);
    if (rightDate.getTime() > latestDate.getTime()){
        updateChoreItems();
        latestDate.setDate(rightDate.getDate());
    }
    console.log(rightDate.toJSON().slice(0,10));

    var rightContent = $('#right-chores').html();
    var midContent = $('#mid-chores').html();

    $('#mid-chores').html(rightContent);
    $('#left-chores').html(midContent);

    $.ajax({
        url:'./php/get-choreitems.php',
        type:'post',
        data:{date: rightDate.toJSON().slice(0,10)},
        success:function(html){
            $("#right-chores").html(html);
        }
    });
}