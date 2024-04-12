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
    <!-- End of Bootstrap Modal -->

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
            <div class="no_notifications alert alert-info">
                <p>You don't have any notifications!</p>
            </div>
        <?php endif;?>
    </div>
</main>

<script>
    $(document).ready(function(){
        var ajax = new AjaxHandler();
        var modal = $('#friend_invitation_modal');
        var btn_add = $(".add_friend");
        var btn_decline = $(".deny_friend");

        btn_add.click(function(){
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/1', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade aceito!');
                modal.modal('show');
            });
        });

        btn_decline.click(function(){
            var notification_id = $(this).data("id"); 
            ajax.post('<?php echo base_url('invites');?>/'+$(this).data("indicator")+'/2', {notification_id:notification_id}, function() {
                $('#mensagemModal').text('Pedido de amizade recusado!');
                modal.modal('show');
            });
        });
    });
</script>
