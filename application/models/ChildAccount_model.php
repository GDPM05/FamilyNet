
<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class ChildAccount_model extends MY_Model{
        private $table = 'child_account';

        function __construct(){
            parent::__construct($this->table);
        }

    }
