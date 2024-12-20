<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    //print_r($friends);
?>

<main class="container-fluid d-flex flex-row direct_msg">
    <div class="col-9 left-side">
    </div>
    <div class="col-3 right-side">
        <button class="btn btn-primary create_group" data-toggle="modal" data-target="#create_group">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>
        </button>
        <div class="search_friends">
            <input type="text" class="col-12" id="dm_search_friends">
            <div class="suggestions">
            </div>
        </div>
        <div class="row-12 friends">
            <h3>Amigos</h3>
            <div class="list-group">
                <?php foreach ($conversations as $conv):
                    if(!empty($conv)): ?>
                    <div class="list-group-item list-group-item-action friend">
                        <img class="rounded-circle mr-2" src="<?=$conv['pfp']['path']?>" alt="<?=$conv['pfp']['alt']?>" style="width: 30px; height: 30px;">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 friend_name"><?=$conv['user']['username']?></h5>
                            <small class="friend-hidden" id="conv_id"><?=$conv['id']?></small>
                        </div>
                        <p class="mb-1 friend-hidden" id="friend_id"><?=$conv['user']['id']?></p>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <hr>
        <div class="row-12 groups">
            <h3>Grupos</h3>
            <?php
                foreach($groups as $group): ?>
                    <div class="list-group-item list-group-item-action group">
                        <img src="<?=$group['picture']['path']?>" alt="<?=$group['name']?>" class="rounded-circle mr-2" style="width: 30px; height: 30px;">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 group_name"><?=$group['name']?></h5>
                            <small class="group-hidden" id="n_members"><?=$group['n_members']?></small>
                        </div>
                        <p class="mb-1 group-hidden" id="group_desc"><?=$group['description']?></p>
                        <p class="mb-1 hidden" id="conv_id"><?=$group['id_conversation']?></p>
                    </div>
            <?php endforeach;?>
        </div>
    </div>
    <div class="modal fade" id="create_group" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Criar Grupo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url("create_group")?>" method="post" class="create_group_form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="gname">Nome do Grupo</label>
                        <input type="text" class="form-control" name="gname" id="gname">
                    </div>
                    <div class="form-group">
                        <label for="gpic">Imagem do Grupo</label>
                        <input type="file" class="form-control-file" name="gpic" id="gpic">
                    </div>
                    <div class="form-group">
                        <label for="gdesc">Descrição</label>
                        <textarea class="form-control" name="gdesc" id="gdesc" rows="2" maxlength="400" oninput="updateCharacterCount()"></textarea>
                        <small id="characterCount" class="form-text text-muted">0 / 400</small>
                    </div>
                    <div class="form-group">
                        <label for="friend_list">Amigos: </label>
                        <select class="form-control" name="friend_list[]" id="friend_list" multiple>
                            <?php foreach ($friends as $friend):?>
                                <option value="<?=$friend['id']?>"><?=$friend['username']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gprivacy">Privado</label>
                        <input type="checkbox" name="gprivacy" id="gprivacy">
                        <small>Caso queira que o grupo seja público, deixe a caixa desmarcada.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </form>
            </div>
        </div>
    </div>
</div>

</main>
<script>
    $(()=>{
        $(".loading").toggle();
        var cliente = new Client(io);
        cliente.connect('http://localhost:9014');
        var ajax = new AjaxHandler();
        var friend_id;
        var conv_id;
        let offset = 0;
        var messages;
        let load = true;
        let scrolling = false;
        window.conv_type = null;

        $(".friend").click(function(){
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;   
            var current_friend = $(this).find('#friend_id').text(); 

            if(current_friend == friend_id){
                return;
            }

            conv_id = $(this).find('#conv_id').text();
            if((window.conv_type != null)){
                cliente.change_friend({friends: current_friend, conv_id: conv_id});
            }else
                cliente.emit_userdata({user_name: user_name, user_id:user_id, friend_id: current_friend, id_conv: conv_id});

            friend_id = current_friend;
            window.conv_type = 1;
            setTimeout(()=>{
                $(".loading").toggle();
            }, 200);
            ajax.get('<?php echo base_url('resources/html/dm-mode.html');?>', (data)=>{
                $(".loading").toggle();
                $(".left-side").html(data);
                messages = $(".messages");
                messages.on('scroll', function(){
                    var alturaAtual = $(this)[0].scrollHeight;
                    
                    if ((-($(this).scrollTop()) + $(this).innerHeight() >= alturaAtual - 5)) {
                        if(load){
                            load = false;
                            loadMessages(conv_id);
                        }
                        alturaAtual = $(this)[0].scrollHeight;
                    }
                });
            });

            window.conv_id = conv_id;

            var user = ajax.get('<?php echo base_url('fetch_user');?>/'+conv_id, (data)=>{
                $(".card-header > .user-img").attr('src', data.pfp.path);
                $(".card-header > .user-img").attr('alt', data.user);
                $(".card-header > div > .user-name").text(data.username);
            });



            loadMessages(conv_id);
            $(".loading").toggle();
        });

        $('.group').click(function(){
            const conv_id = $(this).find('#conv_id').text();
            var members_ids = [];
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;   

            ajax.get('<?=base_url('/get_group_members/')?>'+conv_id, (data)=>{

                Object.values(data.data).forEach(member => {
                    members_ids.push(member.id);
                });


                if(window.conv_type != null)
                    cliente.change_friend({friends: members_ids, conv_id: conv_id});
                else
                    cliente.emit_userdata({user_name: user_name, user_id:user_id, friend_id: members_ids, id_conv: conv_id});

                window.conv_type = 0;
            });
            $(".loading").toggle();
            ajax.get('<?php echo base_url('resources/html/dm-mode.html');?>', (data)=>{
                $(".loading").toggle();
                $(".left-side").html(data);
                messages = $(".messages");
                messages.on('scroll', function(){
                    var alturaAtual = $(this)[0].scrollHeight;

                    if ((-($(this).scrollTop()) + $(this).innerHeight() >= alturaAtual - 5)) {
                        if(load){
                            load = false;
                            loadMessages(conv_id);
                        }
                        alturaAtual = $(this)[0].scrollHeight;
                    }
                });
            });

            window.conv_id = conv_id;

            var user = ajax.get('<?php echo base_url('fetch_conv');?>/'+conv_id, (data)=>{
                $(".card-header > .user-img").attr('src', data.data['picture']['path']);
                $(".card-header > .user-img").attr('alt', data.data['picture']['alt']);
                $(".card-header > div > .user-name").text(data.data['name']);
            });

            loadMessages(conv_id);
            
        });

        $(document).on('keypress', function(e) {
            if(e.which == 13) {
                sendMessage();
            }
        });


        $(document).on('click', '#message_send', function() {
            sendMessage();
        });

        function loadMessages(conv_id){
            if(scrolling)
                return;
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

                        var msg_div = '<div class="message '+div_class+'"><p class="msg_text">'+message.message_text+'</p><p class="msg_date">'+message.send_date+'</p></div>';
                        
                        $(".messages").append(msg_div);
                        
                    });
                    
                }
                setTimeout(()=>{
                    load = true;
                    offset += 15;
                    scrolling = false;
                }, 100)
            });

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

            var dataAtual = new Date();
            var dataHoraFormatada = dataAtual.toISOString().replace('T', ' ').substring(0, 19);
        
            var data = {};
            data['message'] = {};
            data['message']['id_sender'] = '<?php echo $user['id'];?>';
            data['message']['id_conversation'] = window.conv_id;
            data['message']['message_text'] = encrypted_msg;
            data['message']['enc_method'] = JSON.stringify(cliente.getMethod());
            data['message']['send_date'] = dataHoraFormatada;
            data['message']['state'] = 0;
            data['message']['read_date'] = '0000-00-00 00:00:00';


            ajax.post('<?php echo base_url('send_message');?>', data, (res)=>{
            });
        }

        /** Pesquisa de amigos */


        var search = "";
        var debounceTimeout;

        $("#dm_search_friends").keyup((e) => {
            clearTimeout(debounceTimeout);

            debounceTimeout = setTimeout(() => {
                $(".suggestions .friend_suggest").remove();

                if ((e.keyCode == 8) && ($("#dm_search_friends").val() == "")) {
                    $(".suggestions").css({ "display": "none" })
                }

                if ($(".suggestions").css('display') == "none" && ($("#dm_search_friends").val() != ""))
                    $(".suggestions").css({ "display": "block" })

                $(".suggestions").off('click').on('click', '.friend_suggest', function () {
                    var id = $(this).find("#user_id").text();
                    ajax.get("<?=base_url('new_conversation')?>/" + id, (data) => {
                        if(!data.error){
                            window.location.reload();
                        }
                    });
                });

                search = $("#dm_search_friends").val();
                if ((e.keyCode != 8 && $("#dm_search_friends").val() != "")) {
                    ajax.get("<?=base_url('search_friends')?>/" + search, (data) => {
                        Object.values(data).forEach(user => {
                            var div = "<div class='friend_suggest d-flex justify-content-between align-items-center w-100 p-2'>";
                            div += "<img class='friend_suggest_img rounded-circle' src='" + user.pfp.path + "' alt='Foto de perfil' style='width: 50px; height: 50px;'>";
                            div += "<p class='friend_suggest_name ml-3 flex-grow-1'>" + user.username + "</p><p id='user_id' class='friend_hidden'>" + user.id + "</p>";
                            div += "</div>";
                            $(".suggestions").append(div);
                        })
                    });
                }
            }, 300); // 300ms debounce
        });


    });
</script>
