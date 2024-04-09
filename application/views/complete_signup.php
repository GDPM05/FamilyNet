<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

?>

<main class="signup">
    <div class="left-side">
        <h1><span>Family</span>Net</h1>
        <img src="<?php echo base_url('resources/Logo.png');?>" alt="Logo">
        <h2>Connecting <span>families</span></h2>
    </div>
    <div class="separation"></div>
    <div class="right-side">
        <h2>Sign Up</h2>
        <p><span class="step_count">1</span>/5</p>
        <?php
            //echo validation_errors('<div class="alert">', '</div>');
            
            if(isset($formErrors))
                echo $formErrors;
            else
                if($this->session->flashdata('alert-success')){
                    ?>
                    <div class="alert-success">
                        <?=$this->session->flashdata('alert-success');?>
                    </div>
                    <?php
                }
            ?>

        <form action="<?=base_url('complete_signup_validation')?>" method="POST" enctype="multipart/form-data" class="signup_form_complete">
            <input type="hidden" name="email" value="<?=$email['email']?>">
            <label for="name_in">First and last name</label>
            <input type="text" name="name_in" id="name_in" value="<?=set_value('name_in')?>">
            <label for="username_in">Username</label>
            <input type="text" name="username_in" id="username_in" value="<?=set_value('username_in')?>">
        
        
            <label for="phone_in">Phone number(optional)</label>
            <input type="text" name="phone_in" id="phone_in" value="<?=set_value('phone_in')?>">
        
        
            <label for="birthday_in">Birthday</label>
            <input type="date" name="birthday_in" id="birthday_in" value="<?=set_value('birthday_in')?>">    
        
        
            <label for="gender_in">Gender</label>
            <select name="gender_in" id="gender_in">
                <?php
                    foreach($genders as $key){
                        echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                    }
                ?>
            </select>
            <label for="p_role">Parental Role</label>
            <select name="p_role" id="p_role">
                <?php
                    foreach($p_roles as $key){
                        echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                    }
                ?>
            </select>
            <div class="g-recaptcha" data-sitekey="6LegHdEoAAAAAFkJKNg0x3bMdsfW-59c2UqYpLHh"></div>
        
            <div class="account_help_signup">
                <p>Alreay have an account? <a href="<?php echo base_url();?>">Log In</a></p>
            </div>
            <input type="submit" value="Submit" name="signup_submit">
        </form>
    </div>
</main> 