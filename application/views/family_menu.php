<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="family_menu">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!-- Botões Open Chat e Settings -->
                <button class="btn open_chat btn-primary my-3">Open Chat</button>
                <button class="btn config btn-secondary my-3">Settings</button>
                <!-- Novo div abaixo dos botões -->
                <div class="family_chat hidden my-3">
                    
                </div>
                <div class="family_settings hidden my-3">
                <form action="<?=base_url('update_family_info')?>" method="post">
                    <!-- Seção para atualizar informações da família -->
                    <div class="form-group">
                        <label for="familyName">Nome da Família</label>
                        <input type="text" name="familyName" id="familyName" class="form-control" value="<?=$family_name?>">
                    </div>

                    <!-- Seção para gerenciar membros da família -->
                    <div class="manage-members mt-4">
                        <h5>Gerenciar Membros da Família</h5>
                        <ul class="list-group">
                            <?php foreach($family_members as $member): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?=$member['name']?>
                                    <!-- Adicionar botões ou links para editar ou remover um membro -->
                                    <span>
                                        <a href="<?=base_url('edit_member/'.$member['id'])?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="<?=base_url('remove_member/'.$member['id'])?>" class="btn btn-danger btn-sm">Remover</a>
                                    </span>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>

                    <!-- Adicionar outras seções necessárias -->

                    <!-- Botão para salvar alterações -->
                    <input type="submit" value="Salvar Alterações" class='btn btn-success mt-4'>
                </form> 
            </div> 

            </div>
            <div class="side-menus col-md-6 ml-auto">
                <!-- Código existente para os menus laterais -->
                <div class="card my-3">
                    <div class="card-header"><?=$family_name?></div>
                    <ul class="family_members list-group list-group-flush">
                        <!-- Membros da família serão adicionados aqui -->
                    </ul>
                </div>
                <div class="card my-3">
                    <div class="card-header">Create Child Account</div>
                    <div class="card-body">
                        <form action="<?=base_url('child_account')?>" method="post">
                            <p style="color: red"><?if(isset($error) && $error): echo $error_msg; endif;?></p>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" name="birthday" id="birthday" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <?php foreach($genders as $gender): ?>
                                        <option value="<?=$gender['id']?>"><?=$gender['title']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <input type="submit" value="Create" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(()=>{
        const ajax = new AjaxHandler();

        ajax.get('<?=base_url('get_family')?>', function(data){
            console.log(data);
            Object.values(data).forEach(user => {
                console.log(user);
                var li = "<li style='cursor: pointer;' class='family_member list-group-item'>" +
                         ((user.pfp && user.pfp.hasOwnProperty('path')) ? 
                         "<img style='width: 50px; border-radius: 50%; margin-right: 15px;' src='" + 
                         user.pfp.path + "' alt='" + user.pfp.alt + "'>" : "") + 
                         user.username + "<p class='hidden'>" + user.id + "</p></li>";
                $(".family_members").append(li);
            });

            $(".family_member").click(function(){
                window.location.href = 'http://localhost/FamilyNet/see_profile/' + $(this).find('.hidden').text();
            });
        });


        $(".config").click(function(){
            console.log("Aa");
            $(".family_settings").removeClass("hidden");
        });

    });
</script>
