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
        var friend_id;
        $(".friend").click(function(){
            console.log(friend_id);
            var user_name = '<?php echo $user['user'];?>';
            var user_id = <?php echo $user['id'];?>;   
            var current_friend = $(this).children('#friend_id').text();  
            if(friend_id != null && this.friend_id != current_friend){
                cliente.change_friend(current_friend);
            }else{
                cliente.emit_userdata({user_name: user_name, user_id:user_id, friend_id: current_friend})
            }
            friend_id = current_friend;
            console.log(friend_id);
        });
    });
</script>