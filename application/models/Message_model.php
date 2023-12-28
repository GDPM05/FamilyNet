<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Message_model extends MY_Model{
        private $table = 'message';

        function __construct(){
            parent::__construct($this->table);
        }

        public function get_message_count($where = array()){
            if($where){
                $this->db->group_start();
                    $this->db->where('id_sender', $where['id1']);
                    $this->db->or_where('id_receiver', $where['id2']);
                $this->db->group_end();
                $this->db->group_start();
                    $this->db->where('id_sender', $where['id2']);
                    $this->db->or_where('id_receiver', $where['id1']);
                $this->db->group_end();
            }
            
            $q = $this->db->get($this->table);
            return $q->num_rows();
        }    

        public function fetch_messages($pag = false, $limit = null, $start = null, $order = null, $where = null){
            if($order)
                $this->db->order_by($order);

            if($where){
                $this->db->group_start();
                    $this->db->where('id_sender', $where['id_friend']);
                    $this->db->or_where('id_receiver', $where['id_friend']);
                $this->db->group_end();
                $this->db->group_start();
                    $this->db->where('id_sender', $where['id_user']);
                    $this->db->or_where('id_receiver', $where['id_user']);
                $this->db->group_end();
            }

            if($pag){
                $this->db->limit($limit, $start);
                $query = $this->db->get($this->table);
                return $query->result_array();
            }else{
                return $this->db->get($this->table)->result_array();
            }
        }

    }

