<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterSuratModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_Surat
        ";
    
        // Add search condition if the search parameter is provided
        if (!empty($search)) {
            $sql .= "
                WHERE surat_text LIKE ?
            ";
        }
    
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY surat_text
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
        return $this->db->from('Horizon_Master_Template')->count_all_results();
    }

    public function insert_data($surat_text) {
        $data = [
            'surat_text' => strtoupper($surat_text) // Convert to uppercase
        ];
        
        $this->db->insert('Horizon_Master_Surat', $data);
    }
    

    public function update_status($id, $status) {
        $this->db->set('is_active', $status);
        $this->db->where('id_surat', $id);
        $this->db->update('Horizon_Master_Surat');
    }

    public function get_surat_data($suratText) {
        $this->db->select('*');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where('surat_text', $suratText);
        $query = $this->db->get();
        return $query->row();
    }
    

    public function update_data($id_surat, $surat_text) {
        $data = [
            'surat_text' => strtoupper($surat_text) // Convert to uppercase
        ];
        
    
        $this->db->where('id_surat', $id_surat);
        $this->db->update('Horizon_Master_Surat', $data);
    
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function set_inactive_active($id_surat, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('id_surat', $id_surat);
        $this->db->update('Horizon_Master_Surat', $data);
    }
}
?>
