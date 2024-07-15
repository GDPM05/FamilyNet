$(document).ready(function(){
    var ajax = new AjaxHandler();


    $(document).on('keyup', '#search', function(event){
        var keyCode = event.which || event.keyCode;
        if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 48 && keyCode <= 57) || keyCode == 8) {
            execAjax();
        }
    });

    function createDiv(user){
        var div = `<div class="col-md-4 d-flex flex-column search-items align-items-center text-center mb-3">
        <div class="card user_list">
            <input type="hidden" name="user_id" value="${user.id}">
            <img style="width: 100px" src="${user.pfp.path}" alt="${user.pfp.alt}" class="profile-pict">
            <div class="card-body">
                <h5 class="card-title">${user.username}</h5>
                <p class="card-text">${user.p_role.title}</p>
                <a href="http://localhost/FamilyNet/see_profile/${user.user}" class="btn see_profile btn-primary">Ver Perfil</a>
            </div>
        </div>
    </div>
    `;
        return div;
    }

    function execAjax(){
        var debounceTimeout;
        clearTimeout(debounceTimeout);
        $(".results").html("");
        var query = $('#search').val();
        $(".results").html("");
        var secondSegment = (window.location.pathname.split('/')[3] != undefined) ? window.location.pathname.split('/')[3] : 0;
        var page = secondSegment;
        debounceTimeout = setTimeout(function(){
            if(query != "") {
                ajax.post("http://localhost/FamilyNet/fetch", {query:query, page:page}, function(results) {
                    $(".results").html('<div class="row">');
                    Object.values(results.query).forEach(user => {
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