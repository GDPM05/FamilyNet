<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ResetPassword_Model extends MY_Model{
        private $table = 'reset_password';

        function __construct(){
            parent::__construct($this->table);
        }

    }

