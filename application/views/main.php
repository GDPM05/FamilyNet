<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main id="main-posts">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="posts col-lg-8 col-md-10 col-sm-12">
                <div class="user-posts mb-4">
                </div>
            </div>
            <div class="users_infos col-lg-2 col-md-10 col-sm-12">
                <div class="user-info text-center mb-4">
                    <img src="<?=$user['pfp']['path']?>" alt="Foto de perfil" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px;">
                    <h2><?=$user['username']?></h2>
                </div>
                <div class="user-friends">
                    <h3>Amigos</h3>
                    <?php foreach($friends as $friend): ?> 
                        <div class="friend d-flex align-items-center mb-2">
                            <img src="<?=$friend['pfp']['path']?>" alt="<?=$friend['user']?>" class="img-fluid rounded-circle" style="width: 30px; height: 30px;">
                            <h6 class="ms-2 mb-0"><?=$friend['username']?></h6>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Novo Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de novo post -->
                    <form action="<?php echo base_url('/new_post'); ?>" class="post-form" method="post" enctype="multipart/form-data">
                        <div id="preview" class="mb-3 d-flex flex-wrap"></div>
                        <div class="mb-3">
                            <input type="text" name="post-text" class="form-control" placeholder="O que estás a pensar?" required>
                        </div>
                        <div class="mb-3">
                            <label for="file-upload" class="btn btn-outline-secondary">
                                <i class="bi bi-paperclip"></i> Adicionar Imagens
                            </label>
                            <input id="file-upload" type="file" name="file-upload[]" multiple="multiple" style="display: none;">
                        </div>
                        <div class="mb-3">
                            <select name="privacy" id="privacy" class="form-select">
                                <?php foreach($privacy as $p):?>
                                    <option value="<?=$p['id']?>"><?=$p['level']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Publicar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary new_post" data-bs-toggle="modal" data-bs-target="#postModal">
        Criar Publicação
    </button>

</main>
<script>

</script>
<script>
    /*var modal = new bootstrap.Modal(document.getElementById('postModal'), {});
    modal.show();*/
    $(".new_post").click(function(){
        console.log("Botão clicado!"); // Adicione esta linha para verificar se a função está sendo chamada
        $('#postModal').modal('show');
    });
    var fileList = [];
    const max_imgs = 3;
    var fileUpload = document.getElementById('file-upload');
    var fileLabel = $("#file-label");

    fileUpload.onchange = function(e) {
        // Se o número de arquivos ultrapassa o limite, não adicionar mais arquivos
        if (fileList.length + e.target.files.length > max_imgs) {
            return;
        }

        // Adicionar os arquivos selecionados ao array fileList
        fileList = [...fileList, ...Array.from(e.target.files)];
        updatePreview();

        // Se o limite for atingido, adicionar a classe 'disabled' ao label
        if (fileList.length >= max_imgs) {
            fileLabel.addClass('disabled');
        }
    }

    function updatePreview() {
        var preview = document.getElementById('preview');
        preview.innerHTML = ''; // Limpar a pré-visualização anterior
        fileList.forEach(function(file, i) {
            var div = document.createElement('div');
            div.className = 'image-preview';
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    div.appendChild(img);
                    var closeButton = document.createElement('button');
                    closeButton.innerHTML = '×';
                    closeButton.className = 'btn btn-sm btn-danger';
                    closeButton.onclick = function() {
                        fileList.splice(i, 1);
                        updatePreview();
                        // Se o limite não for mais atingido, remover a classe 'disabled' do label
                        if (fileList.length < max_imgs) {
                            fileLabel.removeClass('disabled');
                        }
                    }
                    div.appendChild(closeButton);
                }
                reader.readAsDataURL(file);
            } else {
                var icon = document.createElement('i');
                icon.className = 'bi bi-file-earmark';
                icon.style.fontSize = '50px';
                div.appendChild(icon);
            }
            preview.appendChild(div);
        });
    }

    const ajax = new AjaxHandler();
    var page = 1;

    ajax.get('<?=base_url('get_posts/')?>'+page, function(data){
        console.log(data);
        Object.keys(data).forEach(function(key){
            console.log(data[key]);
            var post = data[key];
            var like_icon = post.already_like ? '<i class="bi bi-hand-thumbs-up-fill"></i>' : '<i class="bi bi-hand-thumbs-up"></i>';
            var div = `
                <div class="post-container">
                    <div class="post card mb-4 shadow-sm">
                        <div class="post-header card-header d-flex align-items-center">
                            <img src="${post.pfp.path}" alt="${post.pfp.alt}" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                            <div>
                                <strong>${post.username}</strong>
                                <p class="mb-0 text-muted" style="font-size: 12px;">${post.post.sent_date}</p>
                            </div>
                        </div>
                        <div class="post-content card-body">
                            <p>${post.post.text}</p>
                            ${generateMediaContent(post, key)}
                        </div>
                        <div class="like">
                            ${like_icon} <p class="num_likes" style="display: inline-block;">${post.post.likes}</p>
                        </div>
                        <p class="hidden post_id">${post.post.id}</p> 
                        <p class="hidden publisher_id">${post.id}</p> 
                    </div>
                </div>`;
            $(".user-posts").append(div);
        });
        changeLike();
    });

    function generateMediaContent(post, key) {
        if (post.post.media && post.post.media.length > 0) {
            var mediaDiv = `
                <div class="d-flex images overflow-auto">
                    ${generateMediaItems(post)}
                </div>`;
            return mediaDiv;
        }
        return '';
    }

    function generateMediaItems(post) {
        return post.post.media.map(function(media) {
            return `
                <div>
                    <img src="${media.path}" alt="${media.alt}" class="d-block" style="width: 300px; height: 300px;">
                </div>`;
        }).join('');
    }

    function changeLike(){
        $(".like").click(function(){
            console.log("ola");
            var postId = $(this).siblings('.post_id').text();
            var publisher = $(this).siblings('.publisher_id').text();
            var hollow = '<i class="bi bi-hand-thumbs-up"></i>';
            var filled = '<i class="bi bi-hand-thumbs-up-fill"></i>';
            var btn = ($(this).html() == '<i class="bi bi-hand-thumbs-up-fill"></i>') ? '<i class="bi bi-hand-thumbs-up"></i>' : '<i class="bi bi-hand-thumbs-up-fill"></i>';
            var self = this;
            if($(this).find('i').hasClass('bi-hand-thumbs-up')){
                console.log('ola2');
                $(this).find('i').remove();
                $(this).prepend(filled);
                ajax.get('<?=base_url('add_like/')?>'+postId+'/'+<?=$user['id']?>, function(data){
                    console.log(data);
                    console.log(data.current_likes[0].likes);
                    if(!data.success){
                        alert(data.message);
                    }
                    console.log(data.current_likes[0].likes);
                    $(self).find('.num_likes').html(data.current_likes[0].likes);

                });
                const notification_info = {
                    receiver_id: publisher,
                    sender_id: <?=$user['id']?>,
                    message: '<?=$user['username']?> deu like no seu post!',
                    type: 3,
                    post_id: postId
                }
                ajax.post('http://localhost:5910/send_notification', notification_info, function(data){
                    console.log(data);
                }, 'application/x-www-form-urlencoded; charset=UTF-8');
            }else{
                console.log('ola3');
                $(this).find('i').remove();
                $(this).prepend(hollow);
                ajax.get('<?=base_url('remove_like/')?>'+postId+'/'+<?=$user['id']?>, function(data){
                    console.log(data);
                    if(!data.success){
                        alert(data.message);
                    }
                    $(self).find('.num_likes').html(data.current_likes[0].likes);
                });
            }   
        });
    }
</script>