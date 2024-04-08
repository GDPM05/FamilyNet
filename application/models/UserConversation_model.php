<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class UserConversation_model extends MY_Model{
        private $table = 'user_conversation';

        function __construct(){
            parent::__construct($this->table);
        }

        public function get_conversation($where){
            if($where){
                $this->db->group_start();
                    $this->db->where('id_conv', $where['id_conv']);
                    $this->db->where('id_user !=', $where['my_id']);
                $this->db->group_end();
            }
            
            $q = $this->db->get($this->table);
            return $q->row_array();
        }
    }

