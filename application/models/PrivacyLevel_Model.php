<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class PrivacyLevel_Model extends MY_Model{
        private $table = 'privacy_levels';

        function __construct(){
            parent::__construct($this->table);
        }

    }

