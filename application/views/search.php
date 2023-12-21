<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

?>

<main>
    <div class="search-bar">
        <input type="text" id="search" name="search" placeholder="Search" value="<?php echo (isset($_SESSION['last_search'])) ? $_SESSION['last_search'] : "";?>">
        <div id="results"></div>
    </div>
    <div class="results">
    </div>
    <p class="users_page"></p>
    <script type="text/javascript">
       $(document).ready(function(){
            execAjax();

            $(document).on('keyup', '#search', function(event){
                var keyCode = event.which || event.keyCode;
                if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 48 && keyCode <= 57) || keyCode == 8) {
                    execAjax();
                }
            });

            function createDiv(user){
                console.log(user);
                var div = '<div class="user_list"><input type="hidden" name="user_id" value="'+user.id+'"><img src="'+user.pfp.path+'" alt="'+user.pfp.alt+'" title="'+user.pfp.alt+'"><div class="infos"><p>'+user.username+'</p><p>'+user.p_role+'</p></div><button><a href="<?php echo base_url('see_profile/');?>'+user.user+'">See Profile</a></button></div>';
                return div;
            }

            function execAjax(){
                console.log("aa");
                var debounceTimeout;
                clearTimeout(debounceTimeout);
                $(".results").html("");
                var query = $('#search').val();
                $(".results").html("");
                var page = <?php echo ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;?>;
                console.log(query, page);
                debounceTimeout = setTimeout(function(){
                    console.log("aa");
                    if(query != "") {
                        console.log("bb")
                        $.ajax({
                            url:"<?php echo base_url('fetch'); ?>",
                            method:"POST",
                            data:{
                                query:query, 
                                page:page
                            },
                            success:function(results) {
                                console.log(results);
                                $(".results").html("");
                                console.log(results.query);
                                Object.values(results.query).forEach(user => {
                                    console.log(results.query.user);
                                    var div = createDiv(user);
                                    $(".results").append(div);
                                });
                                $(".users_page").html(results.links);
                            }
                        });
                    }
                }, 200);
            }
        });



        </script>
</main>