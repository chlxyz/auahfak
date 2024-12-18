<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterPTModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_PT
        ";
    
        // Add search condition if the search parameter is provided
        if (!empty($search)) {
            $sql .= "
                WHERE PersArea_Text LIKE ?
            ";
        }
    
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY PersArea_Text
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
        return $this->db->from('Horizon_Master_PT')->count_all_results();
    }

    public function get_letter_type($id_surat){
        $this->db->select('surat_text');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where('id_surat', $id_surat);
        $query = $this->db->get();
        return $query->row();
    }

    
}
?>
