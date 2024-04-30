<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


?>

<main class="new_family">
    <!-- FAZER FORM E TUDO PARA CRIAÇÃO DE FAMÍLIA -->
    <h2>Parece que ainda não tem uma família...</h2>
     <div class="create_family">
        <form action="<?=base_url('new_family')?>" method="post" class="new_family">
            <label for="family_name">Nome da Família</label>
            <input type="text" name="family_name" id="family_name">
            <label for="family_members">Familiares</label>
            <div class="family_members">
                <?php foreach ($friends as $friend):?>
                    <input type="checkbox" name="<?=$friend['user']?>"><p for="<?=$friend['user']?>"><?=$friend['username']?></p>
                <?php endforeach;?>
            </div>
            <input type="submit" value="Criar" name="submit">
        </form>
     </div>
</main>