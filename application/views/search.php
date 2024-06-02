<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="input-group mb-3">
                <input type="text" id="search" name="search" class="form-control" placeholder="Pesquisa" value="<?php echo (isset($_SESSION['last_search'])) ? $_SESSION['last_search'] : "";?>">
            </div>
            <div class="row justify-content-center results">
                <!-- Resultados da pesquisa -->
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('resources/search-js.js');?>"></script>
</main>

