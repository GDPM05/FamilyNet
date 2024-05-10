<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class MY_Model extends CI_Model{
        private $table;
        public $error;
        public $error_message;
        function __construct($table){
            parent::__construct();
            $this->table = $table;
        }

        public function get_count($where = array()){
            if(!empty($where))
                $this->db->like($where);
            
            $q = $this->db->get($this->table);
            return $q->num_rows();
        }    

        public function fetch($where, $fields = null){
            if($fields)
                $this->db->select($fields);
                $q = $this->db->get_where($this->table, $where);
            if($q->num_rows() < 1){
                $this->error = true;
                $this->error_message = "Não há registo.";
                return;
            }

            return $q->result_array()[0];
        }

        public function fetch_all($pag = false, $limit = null, $start = null, $order = null, $where = null){
            if($order)
                $this->db->order_by($order);
            if($where)
                $this->db->where($where);
            if($pag){
                $this->db->limit($limit, $start);
                $query = $this->db->get($this->table);
                return $query->result_array();
            }else
                return $this->db->get($this->table)->result_array();
        }

        public function fetch_all_like($like, $limit, $start, $where, $fields){
            $this->db->like($like[0], $like[1]);
            if(!empty($limit) || !empty($start))
                $this->db->limit($limit, $start);
            if($fields != null)
                $this->db->select($fields);
            if(!empty($where)){
                if($where['multiple']){
                    unset($where['multiple']);
                    $this->db->where_in($where);
                }else{
                    unset($where['multiple']);
                    $this->db->where($where);
                }
            }
            $query = $this->db->get($this->table);
            return $query->result_array();
        }

        public function fetch_random(){
            $this->db->order_by('RAND()');
            $query = $this->db->get($this->table);
            return $query->row_array();

        }

        public function insert($data_array = array()){
            if(empty($data_array)){
                $this->error = true;
                $this->error_message = "Array de informações está vazio!";
                return false;
            }

            $query = $this->db->insert($this->table, $data_array);

            if(!$query){
                $this->error = true;
                $this->error_message = 'Internal Error. Try again later. '.$this->db->error;
                return false;
            }

            return $this->db->insert_id();
        }

        public function update($data = array(), $where = array()){
            if(empty($data) || empty($where)){
                $this->error = true;
                $this->error_message = 'Array de informações ou clausúla where vazios!';
                return;
            }
            
            $this->db->where($where);
            $this->db->update($this->table, $data);
        }

        public function delete($where = array()){
            if(empty($where)){
                $this->error = true;
                $this->error_message = 'Array de claúsula where vazio!';
                return;
            }
            
            $this->db->where($where);
            $this->db->delete($this->table);
        }

        public function check_if_exists($where = null){
            if($where)
                $this->db->where($where);
            

            $q = $this->db->get($this->table);

            if($q->num_rows() > 0)
                return $q->result_array()[0];

            return false;
        }

        public function get_all($table, $limit, $start){
            $this->db->limit($limit, $start);
            $q = $this->db->get($table);
            return $q->result_array();
        }

    }
