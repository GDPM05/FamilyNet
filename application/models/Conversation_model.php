<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Conversation_model extends MY_Model{
        private $table = 'conversation';

        function __construct(){
            parent::__construct($this->table);
        }

    }

