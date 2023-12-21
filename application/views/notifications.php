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
    <div class="notifications_container">
        <?php if(!empty($notifications)): ?>
            <?php foreach($notifications as $noti): ?>
                <div class="notification">
                    <p><?php echo $noti['sent_date'];?></p>
                    <p><?php echo $noti['message_text'];?></p>
                    <?php if($noti['type_id'] == 1):?>
                        <div class="add_friend" data-indicator="<?php echo $noti['sender']['id'];?>" data-id="<?php echo $noti['id'];?>">
                            <button>Add friend</button>
                        </div>
                        <div class="deny_friend" data-indicator="<?php echo $noti['sender']['id'];?>" data-id="<?php echo $noti['id'];?>">
                            <button>Deny invitation</button>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach; ?>
        <?php else:?>
            <div class="no_notifications">
                <p>You don't have any notifications!</p>
            </div>
        <?php endif;?>
    </div>
</main>
<script>
    $(document).ready(function(){
        var modal = $("#friend_invitation_modal");
        var btn_add = $(".add_friend");
        var btn_decline = $(".deny_friend");
        var span = $(".close");

        btn_add.click(function(){
            var notification_id = $(this).data("id"); 
            $.ajax({
                url: '<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/1',
                type: 'POST',
                data:{
                    notification_id:notification_id
                },
                success: function(){
                    $('#mensagemModal').text('Pedido de amizade aceito!');
                    modal.show();
                }
            })
        })

        btn_decline.click(function(){
            var notification_id = $(this).data("id"); 
            $.ajax({
                url: '<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/2',
                type: 'POST',
                data:{
                    notification_id:notification_id
                },
                success: function(){
                    $('#mensagemModal').text('Pedido de amizade recusado!');
                    modal.show();
                }
            })
        });

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
    });

</script>