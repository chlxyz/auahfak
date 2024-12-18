<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterCapModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_Cap
        ";
        
        // Add search condition if a search term is provided
        if (!empty($search)) {
            $sql .= "
                WHERE nama_cap LIKE ?
            ";
        }
        
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY nama_cap ASC
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
        ";
        
        // Prepare parameters for the query
        $params = [];
        if (!empty($search)) {
            $searchParam = "%$search%";
            $params[] = $searchParam;
        }
        $params = array_merge($params, [$offset, $limit]);
        
        // Execute the query with the prepared parameters
        $query = $this->db->query($sql, $params);
        
        // Return the result as an array of objects
        return $query->result();
    }

    public function count_data() {
        return $this->db->from('Horizon_Master_Cap')->count_all_results();
    }

    public function insert_data($nama_cap, $gambar_cap) {
        $data = [
            'nama_cap' => $nama_cap,
            'gambar_cap' => $gambar_cap
        ];
        $this->db->insert('Horizon_Master_Cap', $data);
    }
    
    public function update_status($id, $status) {
        $this->db->set('is_active', $status);
        $this->db->where('id_cap', $id);
        $this->db->update('Horizon_Master_Cap');
    }
    
    public function update_data($id_cap, $nama_cap, $gambar_cap) {
        $data = [
            'nama_cap' => $nama_cap,
            'gambar_cap' => $gambar_cap,
        ];
    
        $this->db->where('id_cap', $id_cap);
        $this->db->update('Horizon_Master_Cap', $data);
    
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
}
