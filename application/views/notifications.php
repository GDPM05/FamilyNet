<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="container">
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="friend_invitation_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Friend Invitation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="mensagemModal"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="notifications_container">
                    <!-- Aqui serão renderizados os itens -->
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    var nextPage = 2; 
    var loading = false; 
    var totalNotificationsLoaded = 0;
    const ajax = new AjaxHandler();
    var limit = 20;
    execAjax();
    $(window).scroll(function() {
        ajax.post('http://localhost:5000/get_total_notifications', {receiver_id: <?=$user['id']?>}, (data)=>{
            if($(window).scrollTop() + $(window).height() == $(document).height() && !loading) {
                loading = true; 
                console.log(data[0]['num_noti']);
                if((totalNotificationsLoaded+limit) > data[0]['num_noti']);
                    limit = Number(data[0]['num_noti']) - totalNotificationsLoaded; 
                execAjax();
                console.log(totalNotificationsLoaded);
                console.log(limit);
            }
        })
    });

    function execAjax(){
        ajax.post('http://localhost:5000/load_notifications', {page: (nextPage == 2) ? 1 : nextPage, id: <?=$user['id']?>, limit: limit}, (data) => {
            console.log(data);
            renderItems(data);
            nextPage++;
            loading = false; 
            totalNotificationsLoaded += data.length;
        });
    }

    function renderItems(items) {
      items.forEach(function(item) {
        var buttonsHtml = '';
        if (item.type_id === 1) {
          buttonsHtml = `
            <div class="mt-3">
              <button type="button" data-indicator=${item.id} class="btn add_friend btn-success mr-2">Accept</button>
              <button type="button" data-indicator=${item.id} class="btn deny_friend btn-danger">Decline</button>
            </div>
          `;
        }
        var div = `
          <div class="notification alert alert-info alert-dismissible fade show" role="alert">
            <strong>${(new Date(item.sent_date).toLocaleString()).replace(',', '')}</strong><br>
            ${item.message_text}
            ${buttonsHtml}
          </div>`;
        $('.notifications_container').append(div);
      });
    }

</script>
<script>
    window.onload = ()=>{
        var modal = $('#friend_invitation_modal');
        $('.notifications_container').on('click', '.add_friend', function() {
            console.log("aa");
            var notification_id = $(this).attr("data-indicator"); 
            console.log(notification_id);
            ajax.post('http://localhost:5000/accept_invite', {id: notification_id}, function(data) {
                console.log(data);
                if(data.success){
                    $('#mensagemModal').text('Friend invitation accepted!');
                    modal.modal('show'); 
                }
            });
            window.location.reload();
        });
        
        $('.notifications_container').on('click', '.deny_friend', function() {
            var notification_id = $(this).attr("data-indicator"); 
            console.log(notification_id);
            ajax.post('http://localhost:5000/refuse_invite', {id: notification_id}, function(data) {
                console.log(data);
                if(data.success){
                    $('#mensagemModal').text('Friend invitation refused!');
                    modal.modal('show'); 
                }
            });
            window.location.reload();
        });
    }


</script>