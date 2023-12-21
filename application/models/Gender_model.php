<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Gender_model extends MY_Model{
        private $table = 'gender';

        function __construct(){
            parent::__construct($this->table);
        }

    }

