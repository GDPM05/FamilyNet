
<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class NotificationType_model extends MY_Model{
        private $table = 'notification_type';

        function __construct(){
            parent::__construct($this->table);
        }

    }
