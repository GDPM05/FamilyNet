<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css" rel="stylesheet">-->
        <link rel="icon" type="image/x-icon" href="<?php echo base_url('resources/Logo.ico');?>">
        <link rel="stylesheet" href="<?php echo base_url('resources/style.css');?>">
        <script src="<?php echo base_url('server/node_modules\socket.io\client-dist\socket.io.js');?>"></script>
        <script src="<?php echo base_url('resources/Client.js');?>"></script>
        <script src="<?php echo base_url('resources/AjaxHandler.js');?>"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://pagination.js.org/dist/2.6.0/pagination.min.js"></script> 
        <script src="<?php echo base_url('resources/script.js');?>"></script>
        <title><?php echo $title;?></title>
    </head>
<body>

