<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class PostMedia_Model extends MY_Model{
        private $table = 'post_media';

        function __construct(){
            parent::__construct($this->table);
        }

    }

