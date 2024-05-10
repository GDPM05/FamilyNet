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
    </script>
</main>
