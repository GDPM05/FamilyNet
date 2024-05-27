<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Coluna central para posts -->
            <div class="posts col-lg-8 col-md-10 col-sm-12">
                <div class="user-posts mb-4">
                    <!-- Exemplo de um post -->
                </div>
            </div>
            <div class="users_infos col-lg-2 col-md-10 col-sm-12">
                <div class="user-info text-center mb-4">
                    <img src="<?=$user['pfp']['path']?>" alt="Foto de perfil" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px;">
                    <h2><?=$user['username']?></h2>
                </div>
                <div class="user-friends">
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

    <!-- Formulário fixo no fundo da página -->
    <div class="post-creation-container fixed-bottom w-100 p-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-sm-12">
                    <form action="<?php echo base_url('/new_post'); ?>" class="post-form" method="post" enctype="multipart/form-data">
                        <div id="preview" class="mb-3 d-flex flex-wrap"></div>
                        <div class="d-flex align-items-center">
                            <input type="text" name="post-text" class="form-control me-2" placeholder="O que estás a pensar?" style="flex-grow: 1;" required>
                            <div id="preview" class="mb-3"></div>
                            <label for="file-upload" id="file-label" class="btn btn-outline-secondary me-2 mb-0 d-flex align-items-center justify-content-center">
                                <i class="bi bi-paperclip"></i>
                            </label>
                            <input id="file-upload" type="file" name="file-upload[]" multiple="multiple" style="display: none;">
                            <select name="privacy" id="privacy">
                                <?php foreach($privacy as $p):?>
                                    <option value="<?=$p['id']?>"><?=$p['level']?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Publicar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
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
                    </div>
                </div>`;
            $(".user-posts").append(div);
        });
    });

    function generateMediaContent(post, key) {
        if (post.post.media && post.post.media.length > 1) {
            var carouselId = 'carousel-' + key;
            var carouselDiv = `
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        ${generateCarouselItems(post)}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>`;
            return carouselDiv;
        } else if (post.post.media && post.post.media.length === 1) {
            return `
                <div>
                    <img src="${post.post.media[0].path}" alt="${post.post.media[0].alt}" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto;">
                </div>`;
        }
        return '';
    }

    function generateCarouselItems(post) {
        return post.post.media.map(function(media, index) {
            return `
                <div class="carousel-item${index === 0 ? ' active' : ''}">
                    <img src="${media.path}" alt="${media.alt}" class="d-block w-100">
                </div>`;
        }).join('');
    }

</script>