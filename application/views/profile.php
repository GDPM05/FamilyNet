<main class="profile text-center">
    <div class="profile_info">
        <h1 class="profile-name"><img class="profile-pict" src="<?php echo $path;?>"><?php echo $user['username'];?></h1>
    </div>
    <!-- Botões -->
    <button id="friendsButton" class="btn btn-primary">
        Ver amigos
    </button>
    <button id="editProfileButton" class="btn btn-secondary">
        Editar perfil
    </button>
    <a href="<?php echo base_url('logout')?>" class="btn btn-danger" style="margin-left: 100px">Logout</a>

    <div class="modal fade" id="friendsModal" tabindex="-1" aria-labelledby="friendsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendsModalLabel">Amigos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('profile/update_info')?>" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="file" class="col-sm-3 col-form-label">Foto de perfil</label>
                        <div class="col-sm-9">
                            <input type="file" name="pfp" id="pfp" title=""/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="username" class="col-sm-3 col-form-label">Nome de utilizador</label>
                        <div class="col-sm-9">
                            <input type="text" name="username" id="username" class="form-control" value="<?=$user['username'];?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="phone" class="col-sm-3 col-form-label">Número de Telefone</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" id="phone" class="form-control" value="<?=$user['phone'];?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</main>
