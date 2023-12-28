<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    //print_r($friends);
?>

<main class="direct_msg">
    <div class="left-side">
        <?php
            // Lista todas as mensagens trocadas pelos utilizadores, mas carrega por partes para evitar sobrecarga
            /**
             * Para carregar apenas determinadas quantidades, basta usar a função limit do sql, junto com ajax, para assim, carregar x número de mensagens e quando o utilizador chegar ao fim 
             * da página, carregar mais um x, isto serve tanto para as mensgaens, quanto para os "contactos"
             *
             */
        ?>


    </div>
    <div class="separation"></div>
    <div class="right-side">
        <?php
            // Lista todos os utilizadores que o utilizador já comunicou, mas carrega por partes para evitar sobrecargam, tal como em cima, usar o ajax + a função limit
            foreach($friends as $friend){
                $div = '<div class="friend"><img src="'.$friend['pfp']['path'].'" alt="'.$friend['pfp']['alt'].'"><p class="friend_name">'.$friend['username'].'</p><p class="friend-hidden" id="friend_user">'.$friend['user'].'</p><p class="friend-hidden" id="friend_id">'.$friend['id'].'</p></div>';

                echo $div;
            }
        ?>
    </div>
</main>
<script>
    $(()=>{
        var cliente = new Client(io);
        cliente.connect('http://localhost:6969');
        var ajax = new AjaxHandler();
        var friend_id;
        let offset = 0;
        var messages;
        let load = true;
        let scrolling = false;
        $(".friend").click(function(){
            console.log(friend_id);
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;   
            var current_friend = $(this).children('#friend_id').text();  

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
                            loadMessages();
                        }
                        alturaAtual = $(this)[0].scrollHeight;
                    }
                    //console.log("aa");
                });
                console.log(messages);
            });

            var user = ajax.get('<?php echo base_url('fetch_user');?>/'+friend_id, (data)=>{
                console.log("ai", data);
                $(".left-side > .dm-window > .top-bar > .user-info >.user-img").attr('src', data['pfp']['path']);
                $(".left-side > .dm-window > .top-bar > .user-info > .user-img").attr('alt', data['pfp']['alt']);
                $(".left-side > .dm-window > .top-bar > .user-info > .user-name").text(data['username']);
            });


            loadMessages();
            
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

        function loadMessages(){
            console.log("Scrolling ", scrolling);
            if(scrolling)
                return;
            console.log("Offset: ", offset);
            console.log("Oi?");
            scrolling = true;
            ajax.get('<?php echo base_url('get_messages');?>/'+offset+'/'+friend_id, function(data){    
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
            data['message']['id_receiver'] = friend_id;
            data['message']['message_text'] = encrypted_msg;
            data['message']['enc_method'] = JSON.stringify(cliente.getMethod());
            data['message']['send_date'] = dataHoraFormatada;
            data['message']['state'] = 0;
            data['message']['read_date'] = '0000-00-00 00:00:00';

            console.log(data['message']['enc_method']);

            ajax.post('<?php echo base_url('send_message');?>', data, (res)=>{
                console.log(res);
            });
        }

    });
</script>