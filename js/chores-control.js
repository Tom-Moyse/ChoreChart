var date = new Date();
date.setDate(date.getDate() + 10)
var formatDate = date.toJSON().slice(0,10);

$(function(){
    updateChoreItems();

    $(".chore-element").on('click', function(e){
        var pos = $(e.target).offset();
        var width = $(e.target).width();
        pos.left += width;
        $("#info-popup").removeClass("hidden");  
        $("#info-popup").css(pos);
        e.stopPropagation();
    });

    $(document).on('click', function(e){
        $("#info-popup").addClass("hidden"); 
    })
});

function updateChoreItems(){
    $.ajax({
        url:'./php/gen-choreitems.php',
        type:'post',
        data:{date: formatDate},
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