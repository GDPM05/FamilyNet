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
            <div class="continue_with">
                <a href="<?=base_url('google_auth')?>" class="google">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                        <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"/>
                    </svg>
                </a>
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</main> 
