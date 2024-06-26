<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="new_family_main container">
    <h2 class="text-center my-4">Parece que ainda não tem uma família...</h2>
    <div class="alert"></div>
     <div class="create_family">
        <form action="<?=base_url('new_family')?>" method="post" class="new_family">
            <div class="form-group">
                <label for="family_name">Nome da Família</label>
                <input type="text" name="family_name" id="family_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="family_members">Familiares</label>
                <div class="family_members">
                    <?php if(!isset($friends)):
                        echo '<p>Adicione os seus familiares como amigos para os adicionar à sua família.</p>';
                        else:
                         foreach ($friends as $friend):?>
                        <div class="form-check">
                            <input type="checkbox" name="<?=$friend['user']?>" class="form-check-input" id="<?=$friend['user']?>">
                            <label class="form-check-label" for="<?=$friend['user']?>"><?=$friend['username']?></label>
                        </div>
                    <?php endforeach;
                    endif;?>
                </div>
            </div>
            <input type="submit" value="Criar" name="submit" class="btn btn-primary">
        </form>
     </div>
</main>
<script>
    $(document).ready(function() {
        $('.new_family').on('submit', function(event) {
            // Seleciona o campo "Nome da Família"
            var familyNameInput = $('#family_name');

            // Verifica se o campo está vazio
            if (familyNameInput.val().trim() === '') {
                // Impede o envio do formulário
                event.preventDefault();
                // Alerta o usuário
                $(".alert").html("Por favor, preencha todos os campos.").addClass("important");
            }
        });
    });
</script>