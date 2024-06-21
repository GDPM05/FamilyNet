<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ActivityMedia_model extends MY_Model{
        private $table = 'activity_media';

        function __construct(){
            parent::__construct($this->table);
        }

    }

