<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="container-fluid home">
    <div class="row vh-100 align-items-center">
        <div class="col-md-6 text-center left-side">
            <!-- Conteúdo do lado esquerdo -->
            <h1 style="color: #fa81b4"><span style="color: #005dd6">Family</span>Net</h1>
            <img src="<?php echo base_url('resources/Logo.png');?>" alt="Logo" class="img-fluid">
            <h2 style="color: #fa81b4">Connecting <span style="color: #005dd6">families</span></h2>
        </div>
        <div class="separation"></div>
        <div class="col-md-6 text-center right-side">
            <!-- Conteúdo do lado direito -->
            <h2>Login</h2>
            <?php echo (isset($login_error)) ? '<p class="login_error">'.$login_error.'</p>' : "" ?> 
            <form action="" method="POST" class="text-center">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email_in" class="form-control mx-auto" value="<?=set_value('email')?>">
                </div>
                <div class="form-group">
                    <label for="password">Palavra Passe</label>
                    <input type="password" name="password" id="password_in" class="form-control mx-auto" >
                </div>
                <div class="form-group">
                    <label for="keep_login">Manter o Login</label>
                    <input type="checkbox" name="keep_login" id="keep_login">
                </div>
                <div class="account_help">
                    <p>Não tens uma <a href="<?php echo base_url('signup');?>">conta</a>?</p>
                    <p>Esqueceste a tua <a href="<?php echo base_url('reset_password');?>">palavra passe</a>?</p>
                </div>
                <div class="continue_with">
                    <a href="<?=base_url('google_auth')?>" class="google">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                            <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"/>
                    </svg>
                </a>
            </div>
            <input type="submit" value="Login" class="btn btn-primary">
        </form>
    </div>
</main>
