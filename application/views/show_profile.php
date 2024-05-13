<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

?>

<main class="container">
    <div id="friend_invitation_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="mensagemModal"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-5">
        <h1 class="profile-name">
            <img class="profile-pict rounded-circle" src="<?php echo $user_pfp['path'];?>" alt="<?php echo $user_pfp['alt'];?>"><?php echo $user['username'];?>
        </h1>
    </div>
    <div class="user-options text-center mt-4">
        <?php if($already_friends === TRUE || $already_friends->status == 2):?>
            <div class="add_friend d-inline-block mr-2">
                <button class="btn btn-primary">Enviar pedido de amizade</button>
            </div>
        <?php elseif((int)$already_friends->status == 3): ?>
            <div class="add_friend d-inline-block mr-2">
                <button class="btn btn-primary" disabled>Pedido pendente</button>
            </div>
        <?php else:?>
            <div class="send_message d-inline-block mr-2">
                <button class="btn btn-primary"><a href="<?php echo base_url('send_message_private/').$user['id'];?>" class="text-white">Send message</a></button>
            </div>
            <div class="unfriend d-inline-block">
                <button class="btn btn-danger">Remover Amigo</button>
            </div>
        <?php endif;?>
    </div>
</main>

<script>
    $(document).ready(function(){
        var ajax = new AjaxHandler();
        var modal = $("#friend_invitation_modal");
        var btn_add = $(".add_friend");
        var btn_decline = $(".unfriend");
        var span = $(".close");

        btn_add.click(function(){
            ajax.post('<?php echo base_url('send_invite').'/'.$user['id'];?>', null, (data)=>{
                $('#mensagemModal').text("Pedido de amizade enviado com sucesso!");
                modal.show();
                console.log(data);
                document.location.reload();
            });
        });

        btn_decline.click(function(){
            ajax.post('<?php echo base_url('invites/'.$user['id'].'/'.'2');?>', {notification_id}, (data)=>{
                $('#mensagemModal').text(data.mensagem);
                modal.show();
                console.log(data);
            })

            $.ajax({
                url: '<?php echo base_url('invites/'.$user['id'].'/'.'2');?>',
                type: 'POST',
                success: function(data) {
                    $('#mensagemModal').text(data.mensagem);
                    modal.show();
                    console.log(data);
                },
                error: function(data) {
                    $('#mensagemModal').text('Internal Error. Try again later.');
                    modal.show();
                    console.log(data);
                }
            });
        });

        span.click(function(){
            modal.hide();
            setInterval(function(){
                location.reload();
            }, 200)
        });

        $(window).click(function(event){
            if (event.target == modal[0]) {
                modal.hide();
            }
        });
    });

</script>