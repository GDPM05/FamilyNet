<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Groups_model extends MY_Model{
        private $table = 'groups';

        function __construct(){
            parent::__construct($this->table);
        }

    }

