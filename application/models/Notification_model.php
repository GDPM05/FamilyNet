
<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Notification_model extends MY_Model{
        private $table = 'notifications';

        function __construct(){
            parent::__construct($this->table);
        }

    }
