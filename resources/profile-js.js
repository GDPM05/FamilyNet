$(()=>{
    
    var ajax = new AjaxHandler();
    // Get the modal
    var friendsModal = new bootstrap.Modal(document.getElementById('friendsModal'), {});
    var editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'), {});

    // Get the button that opens the modal
    var friendsBtn = $("#friendsButton");
    var editProfileBtn = document.getElementById("editProfileButton");

    // When the user clicks the button, open the modal 
    $("#friendsButton").click(function(){
        console.log("aa");
        friendsModal.show();
        ajax.get("http://192.168.1.70/FamilyNet/get_friends_pr", (friends)=>{
            Object.values(friends).forEach(function(friend){
                console.log(friend);
                var div = createDiv(friend);
                console.log(div);
                $("#friendsModal .modal-body").append(div); // Corrigido aqui
            });
        });
    });

    function createDiv(friend){
        var div = "<div class='friend d-flex align-items-center mb-3 w-100'><img class='friend_pfp rounded-circle me-2' src='"+friend.pfp['path']+"' alt='"+friend['username']+"'><p class='friend_name flex-grow-1 mb-0'>"+friend['username']+"</p><button class='see_profile btn btn-primary'><a class='see_friend' href='http://192.168.1.70/FamilyNet/see_profile"+'/'+friend['id']+"'>See Profile</a></button></div>";

        return div;
    }

    editProfileBtn.onclick = function() {
        editProfileModal.show();
    }

    // Get the element that closes the modal
    var closeFriendsBtn = document.querySelector("#friendsModal .btn-close");
    var closeEditProfileBtn = document.querySelector("#editProfileModal .btn-close");

    // When the user clicks on <span> (x), close the modal
    closeFriendsBtn.onclick = function() {
        friendsModal.hide();
    }

    closeEditProfileBtn.onclick = function() {
        editProfileModal.hide();
    }
})