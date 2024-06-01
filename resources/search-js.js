$(document).ready(function(){
    var ajax = new AjaxHandler();
    execAjax();

    $(document).on('keyup', '#search', function(event){
        var keyCode = event.which || event.keyCode;
        if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 48 && keyCode <= 57) || keyCode == 8) {
            execAjax();
        }
    });

    function createDiv(user){
        //console.log(user);
        var div = '<div class="col-md-4 d-flex flex-column align-items-center text-center mb-3"><div class="user_list"><input type="hidden" name="user_id" value="'+user.id+'"><img src="'+user.pfp+'" alt="'+user.user+'" title="'+user.user+'"><div class="infos"><p>'+user.username+'</p><p>'+user.p_role+'</p></div><button><a href="http://192.168.1.70/FamilyNet/see_profile/'+user.user+'">See Profile</a></button></div></div>';
        return div;
    }

    function execAjax(){
        var debounceTimeout;
        clearTimeout(debounceTimeout);
        $(".results").html("");
        var query = $('#search').val();
        $(".results").html("");
        var secondSegment = (window.location.pathname.split('/')[3] != undefined) ? window.location.pathname.split('/')[3] : 0;
        console.log("SEGMENT " + secondSegment);
        var page = secondSegment;
        console.log(query, page);
        debounceTimeout = setTimeout(function(){
            if(query != "") {
                ajax.post("http://192.168.1.70/FamilyNet/fetch", {query:query, page:page}, function(results) {
                    console.log(results);
                    $(".results").html('<div class="row">');
                    console.log(results.query);
                    Object.values(results.query).forEach(user => {
                        console.log(results.query.user);
                        var div = createDiv(user);
                        $(".results").append(div);
                    });
                    $(".results").append('</div>');
                    $(".users_page").html(results.links);
                });
            }
        }, 200);
    }
});