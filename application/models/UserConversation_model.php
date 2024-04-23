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

        public function check_if_conv_exists($id_user1, $id_user2){
            $this->db->select('id_conv');
            $this->db->where_in('id_user', array($id_user1, $id_user2));
            $this->db->group_by('id_conv');
            $this->db->having('COUNT(DISTINCT id_user) = 2'); // Corrected line
            $query = $this->db->get($this->table);
            
            return ($query->num_rows() == 0);
        }
        
    }

