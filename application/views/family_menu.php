<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="family_menu">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!-- Botões Open Chat e Settings -->
                <button class="activity-btn btn my-3">Atividades</button>
                <?if($admin == 1): ?>
                    <button class="config btn btn-secondary my-3">Definições</button>
                <?endif;?>
                <!-- Novo div abaixo dos botões -->
                <div class="activities hidden my-3">
                    <h2>As nossas <span class="c_p">sugestões</span> de <span class="c_s">atividades</span>.</h2>
                    <div class="activities-list">

                    </div>
                </div>
                <div class="family_settings hidden my-3">
                    <form action="<?=base_url('update_family_info')?>" method="post">
                        <!-- Seção para atualizar informações da família -->
                        <div class="form-group">
                            <label for="familyName">Nome da Família</label>
                            <input type="text" name="familyName" id="familyName" class="form-control" value="<?=$family_name?>">
                        </div>
                        <div class="form-group">
                            <button type="button" class="add_member btn btn-primary" name="add_member">Adicionar Membros</button>
                        </div>
                        <div class="manage-members mt-4">
                            <h5>Gerir Membros da Família</h5>
                            <ul class="list-group">
                                <?php foreach($family_members as $member): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center w-100">
                                        <? echo ($member['id'] == $family_creator || $member['admin'] == 1) ? $member['username']." <small>Administrador</small>" : $member['username'];?>
                                        <span>
                                            <a href="<?=base_url('promote_member/'.$member['id'])?>" class="btn btn-warning btn-sm">Promover</a>
                                            <a href="<?=base_url('remove_member/'.$member['id'])?>" class="btn btn-danger btn-sm">Remover</a>
                                        </span>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <input type="submit" value="Guardar Alterações" class='btn btn-success mt-4'>
                    </form> 
                </div> 
            </div>
            <div class="side-menus col-md-6 ml-auto">
                <!-- Código existente para os menus laterais -->
                <div class="card my-3">
                    <div class="card-header"><?=$family_name?></div>
                    <ul class="family_members list-group list-group-flush">

                    </ul>
                </div>
                <?if($admin == 1): ?>
                    <div class="card my-3">
                        <div class="card-header">Criar conta-criança</div>
                        <div class="card-body">
                            <form action="<?=base_url('child_account')?>" method="post">
                                <p style="color: red"><?if(isset($error) && $error): echo $error_msg; endif;?></p>
                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <input type="text" name="name" id="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="birthday">Data de Nascimento</label>
                                    <input type="date" name="birthday" id="birthday" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <?php foreach($genders as $gender): ?>
                                            <option value="<?=$gender['id']?>"><?=$gender['title']?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <input type="submit" value="Criar" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                <?endif;?>
            </div>
            <div class="modal fade" id="add_member_modal" tabindex="-1" aria-labelledby="add_member_modal_label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add_member_modal_label">Adicionar Membro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="add_members_form" method="post">
                        <div class="mb-3">
                            <label for="select_member" class="form-label">Selecione um Membro</label>
                            <select class="form-select" id="select_member" name="select_member">
                                <?php foreach($friends as $friend): ?>
                                    <option value="<?=$friend['id']?>"><?=$friend['username']?></option>
                                <?php endforeach;?>
                            </select>
                            <div class="error"></div>
                        </div>
                        <button type="submit" class="add_member_submit btn btn-primary">Adicionar</button>
                        </form>
                    </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</main>

<script>
    $(() => {
        const ajax = new AjaxHandler();
        let page = 0;

        ajax.get('<?=base_url('get_family')?>', function(data) {
            console.log(data);
            Object.values(data).forEach(user => {
                var li = "<li style='cursor: pointer;' class='family_member list-group-item'>" +
                         ((user.pfp && user.pfp.hasOwnProperty('path')) ? 
                         "<img style='width: 50px; border-radius: 50%; margin-right: 15px;' src='" + 
                         user.pfp.path + "' alt='" + user.pfp.alt + "'>" : "") + 
                         ((user.id == <?=$family_creator?> || user.admin == 1) ? user.username + "<small style='margin-left: 20px;'>Administrador</small>" : user.username) + 
                         "<p class='hidden'>" + user.id + "</p></li>";
                $(".family_members").append(li);
            });

            $(".family_member").click(function() {
                console.log("1 ", Number($(this).find('.hidden').text()), "2 ",Number(<?=$user['id']?>));
                if(Number($(this).find('hidden').text()) != Number(<?=$user['id']?>)){
                    window.location.href = 'http://localhost/FamilyNet/see_profile/' + $(this).find('.hidden').text();
                }
            });
        });

        $('#add_members_form').on('submit', function(event) {
            event.preventDefault();
            $('.loading').toggle();
            console.log("olá?");
            const member = $("#select_member").val();
            const url = "<?=base_url('add_member')?>";
            
            $.post(url, {member: member}, function(data) {
                console.log(data);
                if (data.error) {
                    $(".error").text(data.message).addClass('important');
                    $('.loading').toggle();
                }

                if(data.success){
                    $('.loading').toggle();
                    setTimeout(()=>{
                        window.location.reload();
                    }, 300);
                }
            });
        });

        $(".config").click(function() {
            console.log("olá?");
            $(".family_settings").toggle();
        });

        $('.add_member').click(function() {
            $('#add_member_modal').modal('show');
        });

        $('#close_modal').click(function() {
            $('#add_member_modal').modal('hide');
        });

        $('.activity-btn').click(function() {
            $(".activities").toggle();
            $(".loading").toggle();
            console.log('aberto 1');
            $(".activity").remove();

            ajax.get('<?=base_url('get_family_activities')?>/'+page, function(data) {
                Object.keys(data.data).forEach(activity => {
                    var images = '';
                    Object.keys(data.data[activity].images).forEach(img => {
                        images += `<div class="image-container">
                                    <img src="${data.data[activity].images[img]['path']}" class="activity-image" alt="Activity Image">
                                   </div>`;
                    });

                    var likeElement = (data.data[activity].liked) ? 
                        ('<i class="bi like bi-hand-thumbs-up-fill permanent-like"></i>') : 
                        (`<i class="bi like ${(data.data[activity].participant) ? '' : 'hidden'} bi-hand-thumbs-up"></i>`);
                    console.log(likeElement);
                    const activityHtml = 
                    `<div class="activity col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${data.data[activity].name}</h5>
                                <p class="card-text">${data.data[activity].description}</p>
                                <div class="d-flex flex-row flex-wrap image-row">
                                    ${images}
                                </div>        
                                <p class="card-text mt-2"><small class="text-muted">${data.data[activity].n_participants} participações.</small></p>
                                <p class="card-text">${likeElement}<small class="n_likes text-muted">Número de likes: <span>${data.data[activity].n_likes}</span></small></p>
                                <button type="button" class="participate btn btn-success" ${(data.data[activity].participant) ? 'disabled' : ''}>Participei</button>
                                <p class="hidden activity_id">${data.data[activity].id}</p>
                            </div>
                        </div>
                    </div>`;

                    $(".activities-list").append(activityHtml);
                });
                $(".loading").toggle();
                console.log('fechado 1');
                $(".participate").click(function() {
                    $(".loading").toggle();
                    var activity_id = $(this).siblings('.activity_id').text();
                    console.log(activity_id);
                    ajax.get('<?=base_url('new_participation')?>/'+activity_id, function(data){
                        if(data.success){
                            $(this).siblings("p").find(".like").toggle();
                            $(".loading").toggle();
                        }else{
                            $(".loading").addClass('loading-failed');
                            setTimeout(()=>{
                                window.location.reload();
                            }, 2000);
                        }
                    })
                });

                $(document).on('mouseenter', '.like.bi-hand-thumbs-up', function() {
                    $(this).removeClass('bi-hand-thumbs-up').addClass('bi-hand-thumbs-up-fill');
                });

                $(document).on('mouseleave', '.like.bi-hand-thumbs-up-fill', function() {
                    if (!$(this).hasClass('permanent-like')) {
                        $(this).removeClass('bi-hand-thumbs-up-fill').addClass('bi-hand-thumbs-up');
                    }
                });

                $(".like").click(function() {
                    console.log("aaa");
                    $(".loading").toggle();
                    var activity_id = $(this).parent().siblings('.activity_id').text();
                    console.log(activity_id);
                    var self = this;
                    var likeElement = $(this);
                    ajax.get('<?=base_url('new_like')?>/'+activity_id, function(data){
                        console.log(data);
                        if(data.success){
                            $(".loading").toggle();
                            console.log($(this).siblings().find('span'));
                            $(this).siblings(".n_likes").find('span').html($(this).siblings(".n_likes").find('span').text() + 1);
                            setTimeout(()=>{
                                likeElement.removeClass('bi-hand-thumbs-up').addClass('bi-hand-thumbs-up-fill permanent-like');
                            }, 100);
                        }else{
                            $(".loading").addClass('loading-failed');
                            setTimeout(()=>{
                                window.location.reload();
                            }, 2000);
                        }
                    });
                });
            });
        });
    });
</script>
