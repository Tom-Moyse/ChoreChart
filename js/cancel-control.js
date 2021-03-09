$(function (){
    // Bind function to cancel button and also get join message string via ajax
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

// Handles cancelling a join request from a user
function cancelRequest(){
    // Sends post request to process-canceljoin and in case of "0" response redirects to the routepage
    // Otherwise informs user that their was an error handling the request
    $.ajax({
        url:'./php/process-canceljoin.php',
        type:'post',
        success:function(response){
            switch (response){
                case '0':
                    window.location.href = "./php/routepage.php";
                    break;
                default:
                    alert("Error cancelling request, please try again later");
            }
        }
    })
}
