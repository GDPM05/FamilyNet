<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="family_menu">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex justify-content-between">
                <button class="btn open_chat btn-primary my-3 flex-grow-1 mr-2">Open Chat</button>
                <button class="btn config btn-secondary my-3 flex-grow-1 ml-2">Settings</button>
            </div>
            <div class="side-menus col-md-6 ml-auto">
                <div class="card my-3">
                    <div class="card-header">Family</div>
                    <ul class="family_members list-group list-group-flush">
                    </ul>
                </div>
                <div class="card my-3">
                    <div class="card-header">Create Child Account</div>
                    <div class="card-body">
                        <form action="<?=base_url('child_account')?>" method="post">
                            <p style="color: red"><?if(isset($error) && $error): echo $error_msg; endif;?></p>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" name="birthday" id="birthday" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <?php foreach($genders as $gender): ?>
                                        <option value="<?=$gender['id']?>"><?=$gender['title']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <input type="submit" value="Create" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(()=>{
        const ajax = new AjaxHandler();

        ajax.get('<?=base_url('get_family')?>', function(data){
            console.log(data);
            Object.values(data).forEach(user => {
                console.log(user);
                var li = "<li style='cursor: pointer; ' class='family_member list-group-item'>"+((user.pfp && user.pfp.hasOwnProperty('path')) ? "<img style='width: 50px; border-radius: 50%; margin-right: 15px;' src='"+user.pfp.path+"' alt='"+user.pfp.alt+"'>" : "")+""+user.username+"<p class='hidden'>"+user.id+"</p></li>";
                $(".family_members").append(li)
            })

            
            $(".family_member").click(function(){
                console.log('aaa');
                window.location.href = 'http://localhost/FamilyNet/see_profile/'+$(this).find('.hidden').text();
            });
        });
    });
</script>
