<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="container-fluid home">
    <div class="row vh-100 align-items-center">
        <div class="col-md-6 text-center left-side">
            <h1 style="color: #fa81b4"><span style="color: #005dd6">Family</span>Net</h1>
            <img src="<?php echo base_url('resources/Logo.png');?>" alt="Logo" class="img-fluid">
            <h2 style="color: #fa81b4">Connecting <span style="color: #005dd6">families</span></h2>
        </div>
        <div class="separation"></div>
        <div class="col-md-6 text-center right-side">
            <h2>Sign Up</h2>
            <p><span class="step_count">1</span>/5</p>
            <?php
            if(isset($formErrors))
                echo $formErrors;
            else
                if($this->session->flashdata('alert-success')){
                    ?>
                    <div class="alert alert-success">
                        <?=$this->session->flashdata('alert-success');?>
                    </div>
                    <?php
                }
            ?>
            <form action="<?=base_url('complete_signup_validation')?>" method="POST" enctype="multipart/form-data" class="text-center">
                <input type="hidden" name="email" value="<?=$email['email']?>">
                <div class="form-group">
                    <label for="name_in">Primeiro e último nome</label>
                    <input type="text" name="name_in" id="name_in" class="form-control mx-auto" style="max-width: 50%;" value="<?=set_value('name_in')?>">
                </div>
                <div class="form-group">
                    <label for="username_in">Nome de utilizador</label>
                    <input type="text" name="username_in" id="username_in" class="form-control mx-auto" style="max-width: 50%;" value="<?=set_value('username_in')?>">
                </div>
                <div class="form-group">
                    <label for="phone_in">Número de telefone (opcional)</label>
                    <input type="text" name="phone_in" id="phone_in" class="form-control mx-auto" style="max-width: 50%;" value="<?=set_value('phone_in')?>">
                </div>
                <div class="form-group">
                    <label for="birthday_in">Data de Nascimento</label>
                    <input type="date" name="birthday_in" id="birthday_in" class="form-control mx-auto" style="max-width: 50%;" value="<?=set_value('birthday_in')?>">
                </div>
                <div class="form-group">
                    <label for="gender_in">Género</label>
                    <select name="gender_in" id="gender_in" class="form-control mx-auto" style="max-width: 50%;">
                        <?php
                            foreach($genders as $key){
                                echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="p_role">Papel parental</label>
                    <select name="p_role" id="p_role" class="form-control mx-auto" style="max-width: 50%;">
                        <?php
                            foreach($p_roles as $key){
                                echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group d-flex justify-content-center">
                    <div class="d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="6LegHdEoAAAAAFkJKNg0x3bMdsfW-59c2UqYpLHh"></div>
                    </div>
                </div>
                <div class="account_help">
                    <p>Já tem uma conta? <a href="<?php echo base_url();?>">Entre</a></p>
                </div>
                <input type="submit" value="Criar conta" name="signup_submit" class="btn btn-primary">
            </form>
        </div>
    </div>
</main>
