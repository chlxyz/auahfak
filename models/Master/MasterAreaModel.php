<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterAreaModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM ms_PersArea
        ";
    
        if (!empty($search)) {
            $sql .= "
                WHERE PERSA LIKE ? 
                   OR NAME1 LIKE ? 
                   OR NAME2 LIKE ? 
                   OR STRAS LIKE ?
            ";
        }
    
        $sql .= "
            ORDER BY PERSA
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
        ";
    
        $params = [];
        if (!empty($search)) {
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        $params = array_merge($params, [$offset, $limit]);
    
        $query = $this->db->query($sql, $params);
    
        return $query->result();
    }
    
    public function count_data() {
        return $this->db->from('ms_PersArea')->count_all_results();
    }

    public function get_data_area($persa) {
        $this->db->select('NAME1', 'NAME2', 'STRAS', 'NOMOR_SURAT');
        $this->db->from('ms_PersArea');
        $this->db->where('PERSA', $persa);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function update_data($persarea, $areaname, $areainitial, $areaaddress, $nomorsurat) {
        $data = [
            'NAME1' => $areaname,
            'NAME2' => $areainitial,
            'STRAS' => $areaaddress,
            'NOMOR_SURAT' => $nomorsurat
        ];
    
        $this->db->where('PERSA', $persarea);
        $this->db->update('ms_PersArea', $data);
    
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function set_inactive_active($NIK, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('NIK', $NIK);
        $this->db->update('Horizon_Master_PenandaTangan', $data);
    }
}
?>
