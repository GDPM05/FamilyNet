$(document).ready(function() {
    var step = 0;
    var steps = $(".form-group").toArray();
    var btnNext = $("#signup_submit");
    var totalSteps = steps.length;

    // Inicializa o primeiro passo como visível
    $(steps[step]).show();
    updateButton();

    // Atualiza o texto do botão com base no passo atual
    function updateButton() {
        if (step === totalSteps - 1) {
            btnNext.text("Criar Conta");
        } else {
            btnNext.text("Próximo");
        }
    }

    // Atualiza a visibilidade dos passos
    function updateStep() {
        $(".form-group").hide();
        $(steps[step]).show();
    }

    // Evento de clique para o botão "Next"
    btnNext.click(function(e) {
        e.preventDefault();
        if (step < totalSteps - 1) {
            step++;
            updateStep();
            updateButton();
        } else {
            // Submete o formulário no último passo
            $(".signup_form").submit();
        }
    });

    // Evento de clique para o botão "Previous"
    $(".btn-secondary").click(function() {
        if (step > 0) {
            step--;
            updateStep();
            updateButton();
        }
    });


    $(".create_group").click(function(){
        $("#create_group").css("display", "block");
        console.log("airflow");
      });
    
      $(".close").click(function(){
        $("#create_group").css("display", "none");
      });
    
      $(window).click(function(event) {
        if ($(event.target).is('#create_group')) {
          $("#create_group").css("display", "none");
        }
      });

})

