<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Activity_model extends MY_Model{
        private $table = 'activities';

        function __construct(){
            parent::__construct($this->table);
        }

    }

