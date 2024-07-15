$(document).ready(function() {
    $('.loading').toggle();

    var step = 0;
    var steps = $(".form-group").toArray();
    var btnNext = $("#signup_submit");
    var totalSteps = steps.length;

    // window.addEventListener('scroll', function() {
    //     window.scrollTo(0, 0);
    // });

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

    function checkFieldRule() {
        const rules = {
            name_in: {
                'min_size': 5,
                'empty': false,
                'name': 'Nome',
            },
            username_in: {
                'empty': false,
                'name': 'Nome de Utilizador',
            },
            phone_in: {
                'only_numbers': true,
                'max_size': 15,
                'empty': true,
                'name': 'Número de telefone',
            },
            birthday_in: {
                'empty': false,
                'name': 'Data de Nascimento',
                'date': true, // nova regra para verificar data
            },
            gender_in: {
                'empty': false,
                'name': 'Género',
            },
            p_role: {
                'empty': false,
                'name': 'Papel Parental',
            },
            email_in: {
                'empty': false,
                'min_size': 6, 
                'regex': /^[a-zA-Z0-9._%+\-\.\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                'name': 'Email', 
            },
            password_in: {
                'empty': false,
                'min_size': 8,
                'regex': /^(?=.*[A-Z])(?=.*[^\w\s]).*$/,
                'name': 'Palavra passe',
            },
            password_repeat: {
                'empty': false,
                'equal': 'password_in',
                'name': 'Repetir palavra passe', 
            },
        }
    
        var inputs = $(steps[step]).find('input');
        for(var i = 0; i < inputs.length; i++) {
            var id = $(inputs[i]).attr('id');
            var value = $(inputs[i]).val();
            var rule = rules[id];
    
            if (rule) {
                if (rule.empty === false && value === '') {
                    sendNotification('O campo '+rule.name+" tem de ser preenchido.");
                    return false;
                }
                if (rule.min_size && value.length < rule.min_size) {
                    sendNotification('O campo '+rule.name+" deve ter pelo menos "+rule.min_size+" caracteres.");
                    return false;
                }
                if (rule.max_size && value.length > rule.max_size) {
                    sendNotification('O campo '+rule.name+" deve ter no máximo "+rule.max_size+" caracteres.");
                    return false;
                }
                if (rule.only_numbers && isNaN(value)) {
                    sendNotification('O campo '+rule.name+" deve conter apenas números.");
                    return false;
                }
                if (rule.regex && !rule.regex.test(value)) {
                    if(id == "password_in")
                        sendNotification('O campo '+rule.name+" deve conter pelo menos 1 letra maíscula e 1 caractere especial.");
                    else if(id == "email_in")
                        sendNotification('O '+rule.name+" deve estar no formato correto.");
                    return false;
                }
                if (rule.equal && value !== $('#' + rule.equal).val()) {
                    sendNotification("As palavras passes não coincidem.");
                    return false;
                }
                if (rule.date && !isValidDate(value)) {
                    sendNotification('A data de nascimento deve ser válida.');
                    return false;
                }
            }
        }
        return true;
    }
    
    function isValidDate(dateString) {
        // Verifica o formato dd/mm/yyyy
        var regex = /^\d{2}\/\d{2}\/\d{4}$/;
        if (!regex.test(dateString)) {
            return false;
        }
    
        // Converte para um objeto Date
        var parts = dateString.split('/');
        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // Mês começa do 0
        var year = parseInt(parts[2], 10);
    
        var date = new Date(year, month - 1, day);
    
        // Verifica a validade da data
        if (date.getFullYear() !== year || date.getMonth() !== month || date.getDate() !== day) {
            return false;
        }
    
        // Verifica se a data não está no futuro
        var now = new Date();
        if (date > now) {
            return false;
        }
    
        // Verifica se a data não está antes de 1920
        var earliestDate = new Date(1920, 0, 1); // 1 de janeiro de 1920
        if (date < earliestDate) {
            return false;
        }
    
        return true;
    }
    
    

    // Verifica se o campo está vazio
    function isFieldEmpty() {
        var inputs = $(steps[step]).find('input');
        for (var i = 0; i < inputs.length; i++) {
            if ($(inputs[i]).val() === '') {
                return true;
            }
        }
        return false;
    }

    function sendNotification(message){
        $(".signup_error").text(message);
        return;
    }

    // Evento de clique para o botão "Next"
    btnNext.click(function(e) {
        e.preventDefault();
        $(".signup_error").text("");
        if (!checkFieldRule()) {
            //checkFieldRule();
            //$(".signup_error").text("Porfavor, preencha todos os campos antes de avançar.");
            return;
        }
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
