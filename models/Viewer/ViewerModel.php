<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ViewerModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_surat_text($jenisSurat) {
        $this->db->select('id_surat');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where('surat_text', $jenisSurat);
        $query = $this->db->get();

        return $query->row()->id_surat; // Return id_surat directly or null if no result
    }

    // public function get_history($persArea) {
    //     $this->db->select('
    //         ht.*,
    //         hpt.PersArea_Text, 
    //         hpt.id_1st_tandatangan, 
    //         hpt.id_2nd_tandatangan,
    //         hmp1.nama AS first_signer_name, 
    //         hmp2.nama AS second_signer_name
    //     ');
    //     $this->db->from('Horizon_Transaction ht');
    //     $this->db->join('Horizon_Master_PT hpt', 'ht.PersArea = hpt.PersArea', 'left');
    //     $this->db->join('Horizon_Master_PenandaTangan hmp1', 'hpt.id_1st_tandatangan = hmp1.NIK', 'left');
    //     $this->db->join('Horizon_Master_PenandaTangan hmp2', 'hpt.id_2nd_tandatangan = hmp2.NIK', 'left');
    //     $this->db->where('ht.PersArea', $persArea);
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function get_isi($idIsi) {
        $this->load->database();
    
        $this->SQLSRV = $this->load->database('TESTING', TRUE);
    
        $query = $this->SQLSRV->query("SELECT isi FROM Horizon_Master_Isi WHERE id_isi = ?", [$idIsi]);
    
        if ($query->num_rows() > 0) {
            return $query->row()->isi;
        } else {
            return null;
        }
    }

    public function get_request_by_viewer($nik, $limit, $offset, $search = '') {
        $this->db->select('PersArea, id_surat');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_viewer', $nik);
        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }
        
        $persAreas = [];
        $idSurats = [];
        foreach ($query->result() as $row) {
            $persAreas[] = $row->PersArea;
            $idSurats[] = $row->id_surat;
        }
    
        $this->db->select('surat_text');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where_in('id_surat', $idSurats);
        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }
        
        $suratTexts = [];
        foreach ($query->result() as $row) {
            $suratTexts[] = $row->surat_text;
        }

        $sql = "
            SELECT * 
            FROM Horizon_Transaction 
            WHERE jenis_surat IN ?
            AND PersArea IN ?
            AND (
                (status_approval1 = 1 AND status_approval2 = 1) 
                OR (status_approval1 = 2 AND status_approval2 = 2) 
                OR (status_approval1 = 2) 
                OR (status_approval1 = 1 AND status_approval2 = 2)
            )
        ";

        if (!empty($search)) {
            $sql .= "
                AND (jenis_surat LIKE ? OR nama LIKE ?)
            ";
        }

        $sql .= " ORDER BY tanggal_request DESC";
        $sql .= " OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

        $params = [$suratTexts, $persAreas];
        if (!empty($search)) {
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $params[] = $offset;
        $params[] = $limit;
        
        $query = $this->db->query($sql, $params);

        return $query->result();
    }
    
    // public function get_request_by_viewer($nik) {
    //     $this->db->select('PersArea, id_surat');
    //     $this->db->from('Horizon_Master_PT');
    //     $this->db->where('id_viewer', $nik);
    //     $query = $this->db->get();
    
    //     if ($query->num_rows() > 0) {
    //         $persAreas = [];
    //         $idSurats = [];
    //         foreach ($query->result() as $row) {
    //             $persAreas[] = $row->PersArea;
    //             $idSurats[] = $row->id_surat;
    //         }

    //         $this->db->select('surat_text');
    //         $this->db->from('Horizon_Master_Surat');
    //         $this->db->where_in('id_surat', $idSurats);
    //         $query = $this->db->get();
        
    //         if ($query->num_rows() === 0) {
    //             return [];
    //         }
        
    //         $suratTexts = [];
    //         foreach ($query->result() as $row) {
    //             $suratTexts[] = $row->surat_text;
    //         }
    
    //         $this->db->select('
    //             ht.*,
    //             hpt.PersArea_Text, 
    //             hpt.id_1st_tandatangan, 
    //             hpt.id_2nd_tandatangan,
    //             hmp1.nama AS first_signer_name, 
    //             hmp2.nama AS second_signer_name
    //         ');
    //         $this->db->from('Horizon_Transaction ht');
    //         $this->db->join('Horizon_Master_PT hpt', 'ht.PersArea = hpt.PersArea', 'left');
    //         $this->db->join('Horizon_Master_PenandaTangan hmp1', 'hpt.id_1st_tandatangan = hmp1.NIK', 'left');
    //         $this->db->join('Horizon_Master_PenandaTangan hmp2', 'hpt.id_2nd_tandatangan = hmp2.NIK', 'left');
    //         $this->db->where_in('ht.PersArea', $persAreas);
    //         $this->db->where_in('ht.jenis_surat', $suratTexts);
    //         $query = $this->db->get();
    
    //         return $query->result();
    //     } else {
    //         return [];
    //     }
    // }

    // public function get_request_by_viewer($nik, $limit, $offset, $search = '') {
    //     // Step 1: Get PersArea and id_surat
    //     $this->db->select('PersArea, id_surat');
    //     $this->db->from('Horizon_Master_PT');
    //     $this->db->where('id_viewer', $nik);
    //     $query = $this->db->get();
    
    //     if ($query->num_rows() > 0) {
    //         $persAreas = [];
    //         $idSurats = [];
    //         foreach ($query->result() as $row) {
    //             $persAreas[] = $row->PersArea;
    //             $idSurats[] = $row->id_surat;
    //         }
    
    //         $this->db->select('surat_text');
    //         $this->db->from('Horizon_Master_Surat');
    //         $this->db->where_in('id_surat', $idSurats);
    //         $query = $this->db->get();
    
    //         if ($query->num_rows() === 0) {
    //             return [];
    //         }
    
    //         $suratTexts = [];
    //         foreach ($query->result() as $row) {
    //             $suratTexts[] = $row->surat_text;
    //         }
    
    //         // Step 3: Final Query with ROW_NUMBER()
    //         $sql = "
    //             SELECT * FROM (
    //                 SELECT 
    //                     ht.*,
    //                     hpt.PersArea_Text,
    //                     hpt.id_1st_tandatangan,
    //                     hpt.id_2nd_tandatangan,
    //                     hmp1.nama AS first_signer_name, 
    //                     hmp2.nama AS second_signer_name,
    //                     ROW_NUMBER() OVER (ORDER BY ht.tanggal_request DESC) AS row_num
    //                 FROM Horizon_Transaction ht
    //                 LEFT JOIN Horizon_Master_PT hpt ON ht.PersArea = hpt.PersArea
    //                 LEFT JOIN Horizon_Master_PenandaTangan hmp1 ON hpt.id_1st_tandatangan = hmp1.NIK
    //                 LEFT JOIN Horizon_Master_PenandaTangan hmp2 ON hpt.id_2nd_tandatangan = hmp2.NIK
    //                 WHERE ht.PersArea IN ?
    //                 AND ht.jenis_surat IN ?
    //                 ";
    
    //         // Add search filter if applicable
    //         if (!empty($search)) {
    //             $sql .= " AND (ht.jenis_surat LIKE ? OR ht.nama LIKE ?)";
    //         }
    
    //         $sql .= ") AS paginated 
    //                   WHERE row_num BETWEEN ? AND ?";
    
    //         // Prepare parameters
    //         $params = [$persAreas, $suratTexts];
    //         if (!empty($search)) {
    //             $params[] = "%$search%";
    //             $params[] = "%$search%";
    //         }
    
    //         // Calculate start and end for pagination
    //         $start = $offset + 1; // ROW_NUMBER starts at 1
    //         $end = $offset + $limit;
    //         $params[] = $start;
    //         $params[] = $end;
    
    //         // Execute the query
    //         $query = $this->db->query($sql, $params);
    //         return $query->result();
    //     } else {
    //         return [];
    //     }
    // }
    

    // public function get_request_by_viewer($nik) {
    //     $this->db->select('PersArea, id_surat');
    //     $this->db->from('Horizon_Master_PT');
    //     $this->db->where('id_viewer', $nik);
    //     $viewerQuery = $this->db->get();
    
    //     if ($viewerQuery->num_rows() > 0) {
    //         $persAreas = [];
    //         $idSurats = [];
    //         foreach ($viewerQuery->result() as $row) {
    //             $persAreas[] = $row->PersArea;
    //             $idSurats[] = $row->id_surat;
    //         }
    
    //         $this->db->select('surat_text');
    //         $this->db->from('Horizon_Master_Surat');
    //         $this->db->where_in('id_surat', $idSurats);
    //         $suratQuery = $this->db->get();
    
    //         if ($suratQuery->num_rows() === 0) {
    //             return [];
    //         }
    
    //         $suratTexts = [];
    //         foreach ($suratQuery->result() as $row) {
    //             $suratTexts[] = $row->surat_text;
    //         }
    
    //         // Select and join the transaction table while grouping by transaction_id
    //         $this->db->select('
    //             ht.transaction_id, 
    //             ht.NIK,
    //             ht.PersArea,
    //             ht.jenis_surat,
    //             ht.status_approval1,
    //             ht.status_approval2,
    //             ht.tanggal_request,
    //             ht.reason_rejection,
    //             hpt.PersArea_Text, 
    //             hpt.id_1st_tandatangan, 
    //             hpt.id_2nd_tandatangan,
    //             hmp1.nama AS first_signer_name, 
    //             hmp2.nama AS second_signer_name
    //         ');
    //         $this->db->from('Horizon_Transaction ht');
    //         $this->db->join('Horizon_Master_PT hpt', 'ht.PersArea = hpt.PersArea', 'left');
    //         $this->db->join('Horizon_Master_PenandaTangan hmp1', 'hpt.id_1st_tandatangan = hmp1.NIK', 'left');
    //         $this->db->join('Horizon_Master_PenandaTangan hmp2', 'hpt.id_2nd_tandatangan = hmp2.NIK', 'left');
    //         $this->db->where_in('ht.PersArea', $persAreas);
    //         $this->db->where_in('ht.jenis_surat', $suratTexts);
    //         $this->db->group_by('
    //             ht.transaction_id,
    //             ht.NIK,
    //             ht.PersArea,
    //             ht.jenis_surat,
    //             ht.status_approval1,
    //             ht.status_approval2,
    //             ht.tanggal_request,
    //             ht.reason_rejection,
    //             hpt.PersArea_Text,
    //             hpt.id_1st_tandatangan, 
    //             hpt.id_2nd_tandatangan,
    //             hmp1.nama,
    //             hmp2.nama
    //         ');  // Add all selected columns here
    //         $transactionQuery = $this->db->get();
    
    //         return $transactionQuery->result();
    //     } else {
    //         return [];
    //     }
    // }
    
    
    
    public function get_request($persArea) {
        $this->db->select('*');
        $this->db->from('Horizon_Transaction');
        $this->db->where('PersArea', $persArea);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_approval_data($persArea, $idSurat) {
        $this->db->select('PersArea, PersArea_Text, nomor, PersArea_inisial, PersArea_alamat, id_1st_tandatangan, id_2nd_tandatangan, id_viewer, id_template, id_surat, id_isi, id_cap')
        ->from('Horizon_Master_PT')
        ->where('PersArea', $persArea)
        ->where('id_surat', $idSurat)
        ->get();
        return null;
    }

    public function get_persarea_name($persArea)
    {
        $this->db->select('PersArea_Text');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('PersArea', $persArea);
        $query = $this->db->get();
        
        return $query->row_array();
    }

    public function get_paraf_status($persArea, $id_1st_approver){
        $this->db->select('status_approval1');
        $this->db->from('Horizon_Transaction');
        $this->db->where('PersArea', $persArea);
        $this->db->where('nik_1st_approval', $id_1st_approver);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_ttd_status($persArea, $id_2nd_approver){
        $this->db->select('status_approval2');
        $this->db->from('Horizon_Transaction');
        $this->db->where('PersArea', $persArea);
        $this->db->where('nik_2nd_approval', $id_2nd_approver);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_transaction($NIKRequester, $letterType) {
        return $this->db->where('NIK', $NIKRequester)
                        ->where('jenis_surat', $letterType)
                        ->get('Horizon_Transaction')
                        ->row_array();
    }

    public function get_viewer_access($nik) {
        $this->db->select('
            id_viewer,
            PersArea
        ');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_viewer', $nik);
        $query = $this->db->get();
        return $query->result();
    }

}
?>