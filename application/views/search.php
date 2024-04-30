<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<main class="container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Barra de pesquisa -->
                <div class="input-group my-3">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search" value="<?php echo (isset($_SESSION['last_search'])) ? $_SESSION['last_search'] : "";?>">
                </div>
                <!-- Elementos do usuário -->
                <div class="row justify-content-center results">
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
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
                console.log(user);
                var div = '<div class="col-md-4 d-flex flex-column align-items-center text-center mb-3"><div class="user_list"><input type="hidden" name="user_id" value="'+user.id+'"><img src="'+user.pfp.path+'" alt="'+user.pfp.alt+'" title="'+user.pfp.alt+'"><div class="infos"><p>'+user.username+'</p><p>'+user.p_role+'</p></div><button><a href="<?php echo base_url('see_profile/');?>'+user.user+'">See Profile</a></button></div></div>';
                return div;
            }

            function execAjax(){
                var debounceTimeout;
                clearTimeout(debounceTimeout);
                $(".results").html("");
                var query = $('#search').val();
                $(".results").html("");
                var page = <?php echo ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;?>;
                console.log(query, page);
                debounceTimeout = setTimeout(function(){
                    if(query != "") {
                        ajax.post("<?php echo base_url('fetch'); ?>", {query:query, page:page}, function(results) {
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
    </script>
</main>
