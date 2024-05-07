<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


?>

<main class="new_family container">
    <!-- FAZER FORM E TUDO PARA CRIAÇÃO DE FAMÍLIA -->
    <h2 class="text-center my-4">Parece que ainda não tem uma família...</h2>
     <div class="create_family">
        <form action="<?=base_url('new_family')?>" method="post" class="new_family">
            <div class="form-group">
                <label for="family_name">Nome da Família</label>
                <input type="text" name="family_name" id="family_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="family_members">Familiares</label>
                <div class="family_members">
                    <?php foreach ($friends as $friend):?>
                        <div class="form-check">
                            <input type="checkbox" name="<?=$friend['user']?>" class="form-check-input" id="<?=$friend['user']?>">
                            <label class="form-check-label" for="<?=$friend['user']?>"><?=$friend['username']?></label>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
            <input type="submit" value="Criar" name="submit" class="btn btn-primary">
        </form>
     </div>
</main>
