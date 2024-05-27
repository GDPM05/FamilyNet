<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class GroupUsers_model extends MY_Model{
        private $table = 'group_user';

        function __construct(){
            parent::__construct($this->table);
        }

    }

