<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    if(isset($formErrors))
        echo $formErrors;
?>

<main>
    <h1 class="vc_h1">Verify Code</h1>
    <form class="vc_form" action="<?php echo base_url("signup/verify");?>" method="post">
        <input type="text" name="input1" id="input1" maxlength="1" onkeyup="nextIn('input1, input2)">
        <input type="text" name="input2" id="input2" maxlength="1" onkeyup="nextIn('input2, input3)">
        <input type="text" name="input3" id="input3" maxlength="1" onkeyup="nextIn('input3, input4)">
        <input type="text" name="input4" id="input4" maxlength="1" onkeyup="nextIn('input4, input5)">
        <input type="text" name="input5" id="input5" maxlength="1" onkeyup="nextIn('input5, input6)">
        <input type="text" name="input6" id="input6" maxlength="1">
        <input type="submit" value="Submit" name="code_verify">
    </form>
    
    <script>
        $('input').on('keyup', function() {
                
            if($(this).val().length >= 1) {
                $(this).next('input').focus();
            }
        });

        $('input').on('paste', function(e) {
            // Obtenha o texto que está sendo colado
            var pasteText = e.originalEvent.clipboardData.getData('text');

            // Se o texto colado tiver mais de um caractere
            if (pasteText.length > 1) {
                // Evite a ação de colar padrão
                e.preventDefault();

                // Distribua cada caractere do texto colado entre os campos de entrada
                for (var i = 0; i < pasteText.length; i++) {
                // Coloque o caractere no campo de entrada correspondente
                $('input').eq(i).val(pasteText[i]);
                }
            }
        });

    </script>
</main>