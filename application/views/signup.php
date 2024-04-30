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
        <div class="separation col-md-1"></div>
        <div class="right-side col-md-5 d-flex flex-column justify-content-center align-items-center">
            <h2 class="text-center mb-4" style="font-size: 2.5rem;">Sign Up</h2>
            <form action="" method="POST" enctype="multipart/form-data" class="signup_form w-100">
                <div class="form-group step_one mb-3 text-center">
                    <input type="text" class="form-control form-control-lg mx-auto" style="width: 80%;" placeholder="Name" name="name_in" id="name_in" value="<?=set_value('name_in')?>">
                    <input type="text" class="form-control form-control-lg mx-auto" style="width: 80%;" placeholder="Username" name="user_in" id="user_in" value="<?=set_value('user_in')?>">
                </div>
                <div class="form-group step_two mb-3 text-center" style="display: none">
                    <input type="email" class="form-control form-control-lg mx-auto" style="width: 80%;" placeholder="Email" name="email_in" id="email_in" value="<?=set_value('email_in')?>">
                    <input type="text" class="form-control form-control-lg mx-auto mt-2" style="width: 80%;" placeholder="Phone Number (optional)" name="phone_in" id="phone_in" value="<?=set_value('phone_in')?>">
                </div>
                <div class="form-group step_three mb-3 text-center" style="display: none">
                    <input type="password" class="form-control form-control-lg mx-auto" style="width: 80%;" placeholder="Password" name="password_in" id="password_in">
                    <input type="password" class="form-control form-control-lg mx-auto mt-2" style="width: 80%;" placeholder="Repeat Password" name="password_repeat" id="password_repeat"> 
                </div>
                <div class="form-group step_four mb-3 text-center" style="display: none">
                    <input type="date" class="form-control form-control-lg mx-auto" style="width: 80%;" name="birthday_in" id="birthday_in" value="<?=set_value('birthday_in')?>">
                    <input type="file" class="form-control-file mx-auto mt-2" style="width: 80%;" name="pfp_in" id="pfp_in" value="<?=set_value('pfp_in')?>">     
                </div>
                <div class="form-group step_five mb-3 text-center" style="display: none">
                    <select class="form-control form-control-lg mx-auto" style="width: 60%;" name="gender_in" id="gender_in">
                        <?php
                            foreach($genders as $key){
                                echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                            }
                        ?>
                    </select>
                    <select class="form-control form-control-lg mx-auto mt-2" style="width: 60%;" name="p_role" id="p_role">
                        <?php
                            foreach($p_roles as $key){
                                echo '<option value="'.$key['id'].'">'.$key['title'].'</option>';
                            }
                        ?>
                    </select>
                    <div class="g-recaptcha mt-4 mb-3 mx-auto text-center" style="width: 60%;" data-sitekey="6LegHdEoAAAAAFkJKNg0x3bMdsfW-59c2UqYpLHh"></div>
                </div> 
                <div class="account_help_signup text-center mb-3">
                    <p>Already have an account? <a href="<?php echo base_url();?>">Sign In</a></p>
                </div>
                <div class="next_prev d-flex justify-content-between mt-3 w-100">
                    <button type="button" class="btn btn-secondary btn-lg w-50">Previous</button>
                    <button type="submit" class="btn btn-primary btn-lg w-50" id="signup_submit">Next</button>
                </div>
            </form>
        </div>





    </div>
</main>
