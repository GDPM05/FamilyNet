<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ActivityParticipant_Model extends MY_Model{
        private $table = 'activity_participant';

        function __construct(){
            parent::__construct($this->table);
        }

    }

