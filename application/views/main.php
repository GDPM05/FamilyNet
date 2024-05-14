<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    //print_r($_COOKIE);
    $id = rand();
    $code = substr(md5($id), 0, 5);
    print_r($code);
    echo '<br/>';
    echo ''.substr(md5(rand()), 0, 5);

?>

<main>
</main>