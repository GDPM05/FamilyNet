<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Family_model extends MY_Model{
        private $table = 'family';

        function __construct(){
            parent::__construct($this->table);
        }

    }

