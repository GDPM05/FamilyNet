<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Friends_model extends MY_Model{
        private $table = 'friends';

        function __construct(){
            parent::__construct($this->table);
        }


        public function check_friends($id1 = null, $id2 = null){
            if(empty($id1) || empty($id2))
                return;

            $this->db->group_start();
            $this->db->where('id_user1', $id1);
            $this->db->where('id_user2', $id2);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('id_user2', $id1);
            $this->db->where('id_user1', $id2);
            $this->db->group_end();

            $query = $this->db->get($this->table);
        
            $check_already_friends = ($query->num_rows() > 0) ? $query->row() : TRUE;

            return $check_already_friends;
        }

        public function fetch_friends($id){
            if(empty($id))
                return;

            $this->db->group_start();
            $this->db->where('id_user1', $id);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('id_user2', $id);
            $this->db->group_end();

            $query = $this->db->get($this->table);
            
            return $query->result_array();
        }

        public function update_invite($id_rec, $id_send, $status){
            // Verifica se os utilizadores têm algum registo na base de dados e se o convite está em estado pendente
            if($this->check_friends($id_rec, $id_send) != true)
                return false;   

            $this->db->set('status', $status);
            $this->db->group_start();
            $this->db->where('id_user1', $id_rec);
            $this->db->where('id_user2', $id_send);
            $this->db->group_end();
            $this->db->or_group_start();
            $this->db->where('id_user1', $id_send);
            $this->db->where('id_user2', $id_rec);
            $this->db->group_end();
            $this->db->update(TABLE_FRIENDS);

            $error = $this->db->error();
            
            if(!empty($error))
                return $error;

            return true;
        }
    }

