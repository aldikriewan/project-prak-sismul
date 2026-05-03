<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stori_model extends CI_Model {

    protected $table = 'storis';
    protected $primary_key = 'id';
    protected $allowed_fields = [
        'title',
        'author',
        'image',
        'background_image',
        'description',
        'story_detail',
        'created_at',
        'updated_at'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        $query = $this->db->get($this->table);
        return $query->result();
    }

    public function get($id) {
        $query = $this->db->get_where($this->table, [$this->primary_key => $id]);
        return $query->row();
    }

    public function insert($data) {
        $data = array_intersect_key($data, array_flip($this->allowed_fields));
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data = array_intersect_key($data, array_flip($this->allowed_fields));
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->where($this->primary_key, $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }
}
