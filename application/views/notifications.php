<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main>
    <div id="friend_invitation_modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="mensagemModal"></p>
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
    

</script>