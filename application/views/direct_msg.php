<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    print_r($friends);
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
                
            }
        ?>
    </div>
</main>