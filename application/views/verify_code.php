<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    if(isset($formErrors))
        echo '<div class="alert alert-danger text-center">' . $formErrors . '</div>';
?>

<main class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
    <h1 class="vc_h1 text-center mb-4">Verificar código</h1>
    <form class="vc_form text-center" action="<?php echo base_url("signup/verify");?>" method="post">
        <div class="d-flex justify-content-center">
            <input type="text" name="input1" id="input1" maxlength="1" onkeyup="nextIn('input1', 'input2')" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
            <input type="text" name="input2" id="input2" maxlength="1" onkeyup="nextIn('input2', 'input3')" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
            <input type="text" name="input3" id="input3" maxlength="1" onkeyup="nextIn('input3', 'input4')" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
            <input type="text" name="input4" id="input4" maxlength="1" onkeyup="nextIn('input4', 'input5')" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
            <input type="text" name="input5" id="input5" maxlength="1" onkeyup="nextIn('input5', 'input6')" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
            <input type="text" name="input6" id="input6" maxlength="1" class="form-control form-control-lg text-center mx-1" style="width: 50px;">
        </div>
        <input type="submit" value="Submit" name="code_verify" class="btn btn-primary mt-4">
    </form>
</main>

<script>
    function nextIn(current, next) {
        if (document.getElementById(current).value.length >= 1) {
            document.getElementById(next).focus();
        }
    }
</script>
