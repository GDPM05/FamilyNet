<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Comments_Model extends MY_Model{
        private $table = 'comments';

        function __construct(){
            parent::__construct($this->table);
        }

    }

