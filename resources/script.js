$(()=>{
    var step = 0;

    var steps = [
        $(".step_one"),
        $(".step_two"),
        $(".step_three"),
        $(".step_four"),
        $(".step_five")
    ];

    console.log(steps[0].children());

    $('.signup_form').on('submit', function(e){
        if(step < steps.length){ // Corrigido aqui
            e.preventDefault();
        }
    })

    $("#signup_submit").click(()=>{
        steps[step].hide();
        step += 1;
        steps[step].show();

        console.log(step);

        if(step+1 == steps.length){
            $("#signup_submit").val("Sign Up");
        }
        
        if(step > 0 && $(".signup_prev").hasClass('signup_prev_disabled'))
            $(".signup_prev").removeClass('signup_prev_disabled')

        $(".step_count").text(step+1);
    });


    $(".signup_prev").click(()=>{
        if(step > 0){
            steps[step].hide();
            step -= 1;
            steps[step].show();

            $(".step_count").text(step+1);
        }

        if($("#signup_submit").val() == 'Sign Up')
            $("#signup_submit").val("Next");

        if(step == 0 && !$(".signup_prev").hasClass('signup_prev_disabled'))
            $(".signup_prev").addClass("signup_prev_disabled");
        
    });

})

