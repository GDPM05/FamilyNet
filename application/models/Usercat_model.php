<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Usercat_model extends MY_Model{
        private $table = 'user_cat';

        function __construct(){
            parent::__construct($this->table);
        }

    }

