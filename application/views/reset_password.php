<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


?>

<main class="reset_pass">
    <div class="left-side">
        <h1><span>Family</span>Net</h1>
        <img src="<?php echo base_url('resources/Logo.png');?>" alt="Logo">
        <h2>Connecting <span>families</span></h2>
    </div>
    <div class="separation"></div>
    <div class="right-side">
        <h2>Reset Password</h2>
        <p class="page_num"></p>
        <form action="" method="POST" class="reset_pass_form">
            <label for="account_info">Email or Phone Number</label>
            <input type="text" name="account_info" id="account_info">
            <input type="submit" value="Reset Password">
        </form>
    </div>
</main> 
