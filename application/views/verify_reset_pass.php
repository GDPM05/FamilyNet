<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    if(isset($formErrors))
        echo '<div class="alert alert-danger text-center">' . $formErrors . '</div>';
?>

<main class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
    <h1 class="vc_h1 text-center mb-4">Insira o seu email</h1>
    <form class="vc_form text-center" action="<?php echo base_url("signup/verify");?>" method="post">
        <div style="width: 250px;" class="justify-content-center">
            <p class="alert"></p>
        </div>
        <div class="justify-content-center">
            <input type="email" name="email" id="email" required="required">
        </div>
        <input type="submit" value="Submit" name="code_verify" class="submit btn btn-primary mt-4">
    </form>
</main>

<script>
    function nextIn(current, next) {
        if (document.getElementById(current).value.length >= 1) {
            document.getElementById(next).focus();
        }
    }

    const ajax = new AjaxHandler();

    $(".submit").click(function(event){
        $(".loading").toggle();
        event.preventDefault();
        var email = $("#email").val();
        if(email.trim() != ""){
            ajax.post(window.location.href, {email: $("#email").val()}, (data)=>{
                $(".loading").toggle();
                console.log(data);
                if(data.success){$(".alert").text('Verifique o seu email.').addClass('important')}
            });
        }else{
            $(".alert").text('Insira o seu email!').addClass('important');
        }
    });
</script>
