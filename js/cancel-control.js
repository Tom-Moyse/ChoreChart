$(document).ready(function (){
    getRequestMessage();
    $('#cancel').on('click', cancelRequest);
})

function getRequestMessage(){
    // Retrieve group name and subsequently get message
    $.ajax({
        url:'./php/fetch-joingroup.php',
        type:'post',
        data:{},
        success:function(response){
            var string =  "You have requested to join the group: ".concat(response,", currently waiting to be accepted!");
            $('#join-info').text(string);
        }
    })
}

function cancelRequest(){
    $.ajax({
        url:'./php/process-canceljoin.php',
        type:'post',
        data:{},
        success:function(response){
            switch (response){
                case '0':
                    window.location.href = "./php/routepage.php";
                    break;
                default:
                    alert("Error cancelling request - Sorry!");
            }
        }
    })
}
