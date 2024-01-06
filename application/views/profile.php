<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="profile">
    <div class="profile_info">
        <h1 class="profile-name"><img class="profile-pict" src="<?php echo $path;?>"><?php echo $username;?></h1>
    </div>
    <div class="edit_profile">
        <form action="<?php echo base_url('update_profile');?>" method="post">
            <label for="udapte_profile_pic">Alterar foto de perfil</label>
            <input type="file" name="udapte_profile_pic" id="udapte_profile_pic">
            <label for="update_name">Alterar nome</label>
            <input type="text" name="update_name" id="update_name">
        </form>
    </div>
    <a href="<?php echo base_url('logout')?>" style="margin-left: 100px">Logout</a>
</main>