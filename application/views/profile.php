<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main>
    <h1 class="profile-name"><img class="profile-pict" src="<?php echo $path;?>"><?php echo $username;?></h1>

    <a href="<?php echo base_url('logout')?>" style="margin-left: 100px">Logout</a>
</main>