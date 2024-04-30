<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class FamilyUser_model extends MY_Model{
        private $table = 'family_user';

        function __construct(){
            parent::__construct($this->table);
        }

    }

