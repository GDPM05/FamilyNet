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
    <h1 class="profile-name"><img class="profile-pict" src="<?php echo $user_pfp['path'];?>" alt="<?php echo $user_pfp['alt'];?>"><?php echo $user['username'];?></h1>

    <div class="user-options">
        <?php if($already_friends === TRUE || $already_friends->status == 2):?>
            <div class="add_friend">
                <button>Add friend</button>
            </div>
        <?php elseif((int)$already_friends->status == 3): ?>
            <div class="add_friend">
                <button disabled>Invitation pending</button>
            </div>
        <?php else:?>
            <div class="send_message">
                <button><a href="<?php echo base_url('send_message_private/').$user['id'];?>">Send message</a></button>
            </div>
            <div class="unfriend">
                <button>Unfriend</button>
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