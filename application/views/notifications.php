<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="container">
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="friend_invitation_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pedido de amizade</h5>
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
<<<<<<< HEAD

    <div class="container">
        <div class="row">
            <div class="col">
            <div class="notifications_container">
                <!-- Aqui serão renderizados os itens -->
=======
    <div class="notifications_container">
        <?php if(!empty($notifications)): ?>
            <?php foreach($notifications as $noti): ?>
                <div class="notification card mb-3">
                    <div class="card-body">
                        <p class="card-text"><?php echo $noti['sent_date'];?></p>
                        <p class="card-text"><?php echo $noti['message_text'];?></p>
                        <?php if($noti['type_id'] == 1):?>
                            <div class="add_friend" data-indicator="<?php echo $noti['sender']['id'];?>" data-id="<?php echo $noti['id'];?>">
                                <button class="btn btn-primary">Add friend</button>
                            </div>
                            <div class="deny_friend" data-indicator="<?php echo $noti['sender']['id'];?>" data-id="<?php echo $noti['id'];?>">
                                <button class="btn btn-danger">Deny invitation</button>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else:?>
            <div class="no_notifications alert alert-info notification">
                <p>You don't have any notifications!</p>
>>>>>>> main
            </div>
            </div>
        </div>
    </div>

</main>

<script>
    var nextPage = 2; // Inicialize nextPage com o número da próxima página a ser carregada
    const ajax = new AjaxHandler();
    execAjax();
    $(window).scroll(function() {
        // Verifique se o usuário chegou ao final da página
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            // Faça uma solicitação AJAX para carregar mais itens
            execAjax();
        }
    });

    function execAjax(){
        ajax.post('http://localhost:5000/load_notifications', {page: nextPage, id: <?=$user['id']?>}, (data) => {
            console.log(data);
            renderItems(data);
            nextPage++;
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
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>${item.sent_date}</strong><br>
            ${item.message_text}
            ${buttonsHtml}
          </div>`;
        $('.notifications_container').append(div);
      });
    }

</script>
<script>
    $('.alert').ready(function(){
        var ajax = new AjaxHandler();
<<<<<<< HEAD
        var btn_add = $(".add_friend");
        var btn_decline = $(".deny_friend");
        var span = $(".close");
        var modal = $("#friend_invitation_modal");
        span.click(function(){
            modal.hide();
            setInterval(function(){
                location.reload();
            }, 300);
        });

        $(window).click(function(event){
            if (event.target == modal[0]) {
                modal.hide();
            }
=======
        var modal = $('#friend_invitation_modal');
        var btn_add = $(".add_friend");
        var btn_decline = $(".deny_friend");

        btn_add.click(function(){
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/1', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade aceito!');
                modal.modal('show');
            });
            location.reload();
        });

        btn_decline.click(function(){
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/2', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade recusado!');
                modal.modal('show');
            });
            location.reload();
>>>>>>> main
        });

        /**
         * MUDAR O ACEITE/RECUSANÇO DOS PEDIDOS PARA NODEJS
         */

        $('.notifications_container').on('click', '.add_friend', function() {
            console.log("aa");
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/1', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade aceito!');
                modal.show();
            });
        });

        btn_add.click(function(){
            
        });

        btn_decline.click(function(){
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/2', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade recusado!');
                modal.show();
            });
        });
    });
<<<<<<< HEAD
    

</script>
=======
</script>
>>>>>>> main
