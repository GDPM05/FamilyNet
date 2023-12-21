<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ParentalRole_model extends MY_Model{
        private $table = 'parental_role';

        function __construct(){
            parent::__construct($this->table);
        }

    }

