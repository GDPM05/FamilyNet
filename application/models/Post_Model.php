<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_Model extends MY_Model {
    private $table = 'post';

    function __construct(){
        parent::__construct($this->table);
    }

    public function fetch_by_family($user_id, $id_friends = []) {
        if(empty($id_friends))
            return [];

        $this->db->where('privacy_level', 3);
        $this->db->group_start();
        foreach ($id_friends as $friend_id) {
            $this->db->or_where('id_sender', $friend_id['id_user']);
        }
        $this->db->group_end();

        return $this->db->get($this->table)->result_array();
    }

    public function add_like($post_id = null){

        if($post_id == null){
            $this->error = true;
            return false;
        }

        $this->db->set('likes', 'likes + 1', FALSE);
        $this->db->where('id', $post_id);
        $this->db->update($this->table);

        return true;
    }

    public function remove_like($post_id = null){
        if($post_id == null){
            $this->error = true;
            return false;
        }

        $this->db->set('likes', 'likes - 1', FALSE);
        $this->db->where('id', $post_id);
        $this->db->update($this->table);

        return true;
    }

    public function get_likes($post_id = null){
        if($post_id == null){
            $this->error = true;
            return false;
        }
        $this->db->select('likes');
        $this->db->where('id', $post_id);
        return $this->db->get($this->table)->result_array();
    }
}
