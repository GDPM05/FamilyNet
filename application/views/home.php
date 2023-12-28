<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


?>

<main class="home">
    <div class="left-side">
        <h1><span>Family</span>Net</h1>
        <img src="<?php echo base_url('resources/Logo.png');?>" alt="Logo">
        <h2>Connecting <span>families</span></h2>
    </div>
    <div class="separation"></div>
    <div class="right-side">
        <h2>Login</h2>
        <?php echo (isset($login_error)) ? '<p class="login_error">'.$login_error.'</p>' : "" ?> 
        <p class="page_num"></p>
        <form action="" method="POST" class="login_form">
            <label for="email">Email</label>
            <input type="email" name="email" id="email_in" value="<?=set_value('email')?>">
            <label for="password">Password</label>
            <input type="password" name="password" id="password_in">
            <div class="account_help">
                <p>Don't have an <a href="<?php echo base_url('signup');?>">account</a>?</p>
                <p>Forgot your <a href="<?php echo base_url('reset_password');?>">password</a>?</p>
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</main> 
