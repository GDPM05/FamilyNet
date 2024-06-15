<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    if(isset($formErrors))
        echo '<div class="alert alert-danger text-center">' . $formErrors . '</div>';
?>

<main class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
    <h1 class="vc_h1 text-center mb-4">Insira o seu email</h1>
    <form class="vc_form text-center" action="<?php echo base_url("signup/verify");?>" method="post">
    <div style="width: 300px;" class="justify-content-center">
            <p class="alert"></p>
        </div>
        <div class="justify-content-center">
            <label for="new_password">Nova Palavra Passe</label><br/>
            <input type="password" name="new_pass" id="new_pass" required="required">
        </div>
        <div class="justify-content-center">
            <label for="new_password">Repetir Palavra Passe</label><br/>
            <input type="password" name="pass_repeat" id="pass_repeat" required="required">
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
        event.preventDefault();
        var pass = $("#new_pass").val().trim();
        var pass_repeat = $("#pass_repeat").val().trim();

        // Expressão regular para verificar a segurança da senha
        var regex = /^(?=.*[A-Z])(?=.*[^\w\s]).*$/;

        if(pass == "" || pass_repeat == "")
            $(".alert").text('Preencha todos os campos').addClass('important');
        else if(pass !== pass_repeat)
            $(".alert").text('Ambas palavras passes têm de ser iguais.').addClass('important');
        else if(!regex.test(pass))
            $(".alert").text('A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.').addClass('important');
        else{
            ajax.post(window.location.href, {password: pass, password_repeat: pass_repeat}, (data)=>{
                console.log(data);
                if(data.success){$(".alert").text('A sua palavra passe foi alterada com sucesso!').addClass('important')}
                setTimeout(()=>{
                    window.location.href = '<?php echo base_url();?>';
                }, 5000);
            });
        }
    });
</script>
