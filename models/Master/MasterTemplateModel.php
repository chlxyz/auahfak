<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterTemplateModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_Template
        ";
    
        // Add search condition if the search parameter is provided
        if (!empty($search)) {
            $sql .= "
                WHERE template_name LIKE ?
            ";
        }
    
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY template_name
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

    public function insert_data($template_name, $header, $footer) {
        $data = [
            'template_name' => $template_name,
            'header' => $header,
            'footer' => $footer
        ];
        
        // Specify the table name in the insert call
        $this->db->insert('Horizon_Master_Template', $data);
    }

    public function update_status($id, $status) {
        $this->db->set('is_active', $status);
        $this->db->where('id_template', $id);
        $this->db->update('Horizon_Master_Template');
    }
    

    public function update_data($id_template, $template_name, $header, $footer) {
        $data = [
            'template_name' => $template_name,
            'header' => $header,
            'footer' => $footer
        ];
    
        $this->db->where('id_template', $id_template);
        $this->db->update('Horizon_Master_Template', $data);
    
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function set_inactive_active($id_template, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('id_template', $id_template);
        $this->db->update('Horizon_Master_Template', $data);
    }
}
?>
