<main class="profile text-center">
    <div class="profile_info">
        <h1 class="profile-name"><img class="profile-pict" src="<?php echo $path;?>"><?php echo $username;?></h1>
    </div>
    <!-- Botões -->
    <button id="friendsButton" class="btn btn-primary">
        Ver amigos
    </button>
    <button id="editProfileButton" class="btn btn-secondary">
        Editar perfil
    </button>
    <a href="<?php echo base_url('logout')?>" class="btn btn-danger" style="margin-left: 100px">Logout</a>

    <!-- Modal Ver Amigos -->
    <div class="modal fade" id="friendsModal" tabindex="-1" aria-labelledby="friendsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendsModalLabel">Amigos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <?php foreach($friends as $friend): ?>
                        <div class="friend d-flex align-items-center mb-3">
                            <img class="friend_pfp rounded-circle me-2" src="<?=$friend['pfp']['path']?>" alt="<?=$friend['username']?>">
                            <p class="friend_name flex-grow-1 mb-0"><?=$friend['username']?></p>
                            <button class="see_profile btn btn-primary">See profile</button>  
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Formulário para editar o perfil aqui -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>

    var ajax = new AjaxHandler();

    ajax.get(<?=base_url('get_friends');?>, (friends)=>{
        
    });

    // Get the modal
    var friendsModal = new bootstrap.Modal(document.getElementById('friendsModal'), {});
    var editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'), {});

    // Get the button that opens the modal
    var friendsBtn = document.getElementById("friendsButton");
    var editProfileBtn = document.getElementById("editProfileButton");

    // When the user clicks the button, open the modal 
    friendsBtn.onclick = function() {
        friendsModal.show();
    }

    editProfileBtn.onclick = function() {
        editProfileModal.show();
    }

    // Get the element that closes the modal
    var closeFriendsBtn = document.querySelector("#friendsModal .btn-close");
    var closeEditProfileBtn = document.querySelector("#editProfileModal .btn-close");

    // When the user clicks on <span> (x), close the modal
    closeFriendsBtn.onclick = function() {
        friendsModal.hide();
    }

    closeEditProfileBtn.onclick = function() {
        editProfileModal.hide();
    }
</script>
