<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ActivityLikes_Model extends MY_Model{
        private $table = 'activity_likes';

        function __construct(){
            parent::__construct($this->table);
        }

    }

