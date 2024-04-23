<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    //print_r($friends);
?>

<main class="container-fluid d-flex flex-row direct_msg">
    <div class="col-9 left-side">
        <!-- Aqui vai o código do chat -->
    </div>
    <div class="col-3 right-side">
        <button class="btn btn-primary create_group">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>
        </button>
        <div class="list-group">
            <?php foreach ($conversations as $conv): 
                if(!empty($conv)): ?>
                <div class="list-group-item list-group-item-action friend">
                    <img class="rounded-circle mr-2" src="<?=$conv['pfp']['path']?>" alt="<?=$conv['pfp']['alt']?>" style="width: 30px; height: 30px;">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 friend_name"><?=$conv['user']['username']?></h5>
                        <small class="text-muted friend-hidden" id="conv_id"><?=$conv['id']?></small>
                    </div>
                    <p class="mb-1 friend-hidden" id="friend_id"><?=$conv['user']['id']?></p>
                </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
    <div id="create_group" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <form action="<?=base_url("create_group")?>" method="post" class="create_group_form" enctype="multipart/form-data">
                <label for="gname">Nome do Grupo</label>
                <input type="text" name="gname" id="gname">
                <label for="gpic">Foto do grupo</label>
                <input type="file" name="gpic" id="gpic">
                <label for="gdesc">Descrição</label>
                <textarea name="gdesc" id="gdesc" cols="20" rows="2" maxlength="400" oninput="updateCharacterCount()"></textarea>
                <p id="characterCount">0 / 400</p>
                <label for="friend_list">Amigos: </label>
                <select name="friend_list[]" id="friend_list" multiple>
                    <?php foreach ($friends as $friend): ?>
                        <option value="<?=$friend['id']?>"><?=$friend['username']?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Criar" name="gcreate">
            </form>
        </div>
    </div>
</main>
<script>
    $(()=>{
        var cliente = new Client(io);
        cliente.connect('http://localhost:3000');
        var ajax = new AjaxHandler();
        var friend_id;
        var conv_id;
        let offset = 0;
        var messages;
        let load = true;
        let scrolling = false;
        $(".friend").click(function(){
            console.log($(this).children(".conv_id").text());
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;   
            var current_friend = $(this).find('#friend_id').text();  
            console.log("Amigo: "+current_friend);
            conv_id = $(this).find('#conv_id').text();
            if(friend_id != null && this.friend_id != current_friend)
                cliente.change_friend(current_friend);
            else
                cliente.emit_userdata({user_name: user_name, user_id:user_id, friend_id: current_friend});

            friend_id = current_friend;
            console.log(friend_id);

            ajax.get('<?php echo base_url('resources/html/dm-mode.html');?>', (data)=>{
                $(".left-side").html(data);
                messages = $(".messages");
                messages.on('scroll', function(){
                    console.log("Load 1", load);
                    //console.log(-($(this).scrollTop()) + $(this).innerHeight());
                    var alturaAtual = $(this)[0].scrollHeight;

                    if ((-($(this).scrollTop()) + $(this).innerHeight() >= alturaAtual - 5)) {
                        console.log('Scroll máximo atingido');
                        console.log("Load: ", load);
                        if(load){
                            load = false;
                            loadMessages(conv_id);
                        }
                        alturaAtual = $(this)[0].scrollHeight;
                    }
                    //console.log("aa");
                });
                console.log(messages);
            });

            
            var user = ajax.get('<?php echo base_url('fetch_user');?>/'+conv_id, (data)=>{
                console.log("ai", data);
                $(".left-side > .dm-window > .top-bar > .user-info >.user-img").attr('src', data['pfp']['path']);
                $(".left-side > .dm-window > .top-bar > .user-info > .user-img").attr('alt', data['pfp']['alt']);
                $(".left-side > .dm-window > .top-bar > .user-info > .user-name").text(data['username']);
            });


            loadMessages(conv_id);
            
        });

        $(document).on('keypress', function(e) {
            if(e.which == 13) {
                sendMessage();
            }
        });


        $(document).on('click', '#message_send', function() {
            console.log("Oi?");
            sendMessage();
        });

        function loadMessages(conv_id){
            console.log("Scrolling ", scrolling);
            console.log("conv_id", conv_id)
            if(scrolling)
                return;
            console.log("Offset: ", offset);
            console.log("Oi?");
            scrolling = true;
            ajax.get('<?php echo base_url('get_messages');?>/'+offset+'/'+conv_id, function(data){    
                var div_class;
                if(data){ 
                    Object.values(data).forEach(message => {

                        if(String(message.id_sender) == '<?php echo $user['id'];?>')
                            div_class = 'self';
                        else
                            div_class = 'friend_msg';

                        message.message_text = cliente.decrypt_message(message.message_text, JSON.parse(message.enc_method));

                        //console.log(div_class);
                        var msg_div = '<div class="message '+div_class+'"><p class="msg_text">'+message.message_text+'</p><p class="msg_date">'+message.send_date+'</p></div>';
                        
                        $(".messages").append(msg_div);
                        
                    });
                    
                }
                setTimeout(()=>{
                    load = true;
                    offset += 15;
                    console.log("offset: ", offset);
                    scrolling = false;
                }, 100)
            });

            console.log("Offset: ", offset);
        }

        function sendMessage(){
            var message_text = $('#message_in').val();
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;

            if(message_text == null || message_text == '')
                return;

            $("#message_in").val('');
            
            var encrypted_msg = cliente.send_message(message_text);
            $('.msg_text').val('');
            var msg_div = '<div class="message self"><p class="msg_text">'+message_text+'</p><p class="msg_date">'+'<?php echo date("Y-m-d H:i:s");?>'+'</p></div>';
            $('.messages').prepend(msg_div);
            console.log(cliente.getMethod());

            var dataAtual = new Date();
            var dataHoraFormatada = dataAtual.toISOString().replace('T', ' ').substring(0, 19);
        
            var data = {};
            data['message'] = {};
            data['message']['id_sender'] = '<?php echo $user['id'];?>';
            data['message']['id_conversation'] = conv_id;
            data['message']['message_text'] = encrypted_msg;
            data['message']['enc_method'] = JSON.stringify(cliente.getMethod());
            data['message']['send_date'] = dataHoraFormatada;
            data['message']['state'] = 0;
            data['message']['read_date'] = '0000-00-00 00:00:00';


            console.log(data);

            ajax.post('<?php echo base_url('send_message');?>', data, (res)=>{
                console.log(res);
            });
        }

    });
</script>
