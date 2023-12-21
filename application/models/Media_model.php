<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Media_model extends MY_Model{
        private $table = 'media';

        function __construct(){
            parent::__construct($this->table);
        }

    }

