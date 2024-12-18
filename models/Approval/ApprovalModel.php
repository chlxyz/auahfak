<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApprovalModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    // public function get_approver_access() {
    //     $this->db->select('id_1st_tandatangan');
    //     $this->db->select('id_2nd_tandatangan');
    //     $this->db->select('PersArea');
    //     $this->db->from('Horizon_Master_PT');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function approve_1($nik, $persArea, $id_surat) {
        $this->db->select('PersArea, id_surat');
    }

    public function get_approver_access($nik) {
        $this->db->select('id_1st_tandatangan, id_2nd_tandatangan, PersArea, id_surat');
        $this->db->from('Horizon_Master_PT');
        $this->db->group_start();
        $this->db->where('id_1st_tandatangan', $nik);
        $this->db->or_where('id_2nd_tandatangan', $nik);
        $this->db->group_end();
        $query = $this->db->get();
        
        // Separate based on role
        $result = $query->result();
        $grouped = [
            'as_first_approver' => [],
            'as_second_approver' => [],
        ];
    
        foreach ($result as $row) {
            if ($row->id_1st_tandatangan == $nik) {
                $grouped['as_first_approver'][] = $row;
            }
            if ($row->id_2nd_tandatangan == $nik) {
                $grouped['as_second_approver'][] = $row;
            }
        }
        return $grouped;
    }
    
    // public function get_approver_access($nik) {
    //     $this->db->select('id_1st_tandatangan, id_2nd_tandatangan, PersArea, id_surat');
    //     $this->db->from('Horizon_Master_PT');
    //     $this->db->group_start();
    //     $this->db->where('id_1st_tandatangan', $nik);
    //     $this->db->or_where('id_2nd_tandatangan', $nik);
    //     $this->db->group_end();
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    

    public function get_surat_text($jenisSurat) {
        $this->db->select('id_surat');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where('surat_text', $jenisSurat);
        $query = $this->db->get();

        return $query->row()->id_surat; // Return id_surat directly or null if no result
    }
    
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

    public function get_requests_by_approver1($nik,$persArea,$id_surat) {
        $this->db->select('PersArea, id_surat');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_1st_tandatangan', $nik);
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
        $this->db->where('id_surat', $id_surat);
        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }
    
        $suratTexts = [];
        foreach ($query->result() as $row) {
            $suratTexts[] = $row->surat_text;
        }

        $this->db->select('*');
        $this->db->from('Horizon_Transaction');
        $this->db->where_in('jenis_surat', $suratTexts);
        $this->db->where('PersArea', $persArea);

        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }

        return $query->result_array();
    }    

    public function get_requests_by_approver2($nik,$persArea,$id_surat) {
        $this->db->select('PersArea, id_surat');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_2nd_tandatangan', $nik);

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
        $this->db->where('id_surat', $id_surat);
        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }
    
        $suratTexts = [];
        foreach ($query->result() as $row) {
            $suratTexts[] = $row->surat_text;
        }

        $this->db->select('*');
        $this->db->from('Horizon_Transaction');
        $this->db->where_in('jenis_surat', $suratTexts);
        $this->db->where('PersArea', $persArea);
        $this->db->where('nik_1st_approval IS NOT NULL');

        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            return [];
        }
    
        return $query->result_array();
    }

    public function get_request_by_1st_tandatangan($nik) {
        $this->db->select('PersArea');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_1st_tandatangan', $nik);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            $persAreas = [];
            foreach ($query->result() as $row) {
                $persAreas[] = $row->PersArea;
            }
    
            $this->db->select('*');
            $this->db->from('Horizon_Transaction');
            $this->db->where_in('PersArea', $persAreas);
            $query = $this->db->get();
    
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_request_by_2nd_tandatangan($nik) {
        $this->db->select('PersArea');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_2nd_tandatangan', $nik);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            $result = $query->row();
            $persArea = $result->PersArea;
    
            $this->db->select('*');
            $this->db->from('Horizon_Transaction');
            $this->db->where('PersArea', $persArea);
            $query = $this->db->get();
    
            return $query->result();
        } else {
            return [];
        }
    }

    // public function get_history($nik, $limit, $offset, $search) {
    //     $this->db->select('PersArea, id_surat');
    //     $this->db->from('Horizon_Master_PT');
    //     $this->db->group_start();
    //     $this->db->where('id_1st_tandatangan', $nik);
    //     $this->db->or_where('id_2nd_tandatangan', $nik);
    //     $this->db->group_end();
    //     $query = $this->db->get();
    
    //     if ($query->num_rows() === 0) {
    //         return [];
    //     }
        
    //     $persAreas = [];
    //     $idSurats = [];
    //     foreach ($query->result() as $row) {
    //         $persAreas[] = $row->PersArea;
    //         $idSurats[] = $row->id_surat;
    //     }
    
    //     $this->db->select('surat_text');
    //     $this->db->from('Horizon_Master_Surat');
    //     $this->db->where_in('id_surat', $idSurats);
    //     $query = $this->db->get();
    
    //     if ($query->num_rows() === 0) {
    //         return [];
    //     }
        
    //     $suratTexts = [];
    //     foreach ($query->result() as $row) {
    //         $suratTexts[] = $row->surat_text;
    //     }
    
    //     $this->db->select('*');
    //     $this->db->from('Horizon_Transaction');
    //     $this->db->where_in('jenis_surat', $suratTexts);
    //     $this->db->where_in('PersArea', $persAreas);
    
    //     $this->db->group_start();
    //     $this->db->where('status_approval1', 1);
    //     $this->db->where('status_approval2', 1);
    //     $this->db->or_group_start();
    //     $this->db->where('status_approval1', 2);
    //     $this->db->where('status_approval2', 2);
    //     $this->db->group_end();
    //     $this->db->or_where('status_approval1', 2);
    //     $this->db->or_where('status_approval1', 1);
    //     $this->db->where('status_approval2', 2);
    //     $this->db->group_end();
    
    //     $query = $this->db->get();
    
    //     return $query->result();
    // }

    public function get_history($nik, $limit, $offset, $search = '') {
        $this->db->select('PersArea, id_surat');
        $this->db->from('Horizon_Master_PT');
        $this->db->group_start();
        $this->db->where('id_1st_tandatangan', $nik);
        $this->db->or_where('id_2nd_tandatangan', $nik);
        $this->db->group_end();
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

    public function get_transaction($NIKRequester, $letterType, $transactionId) {
        return $this->db->where('NIK', $NIKRequester)
                        ->where('jenis_surat', $letterType)
                        ->where('transaction_id', $transactionId)
                        ->get('Horizon_Transaction')
                        ->row_array();
    }

    public function get_companycode($persarea){
        $this->db->select('NAME2');
        $this->db->from('ms_PersArea');
        $this->db->where('PERSA', $persarea);
        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->NAME2 : null;
    }

    public function get_total_transaction($year, $persarea) {
        return $this->db->where('YEAR(tanggal_request)', $year)
                        ->where('PersArea', $persarea)
                        ->where('status_approval2', '1') 
                        ->from('Horizon_Transaction')
                        ->count_all_results();
    }

    public function get_format_surat_number($persarea) {
        $this->db->select('NOMOR_SURAT');
        $this->db->from('ms_PersArea');
        $this->db->where('PERSA', $persarea);
        $query = $this->db->get();
    
        // Fetch a single row and return the 'NOMOR_SURAT' value
        $result = $query->row();
        return $result ? $result->NOMOR_SURAT : null; // Return null if no data is found
    }
    
    public function update_nomor_surat($transactionId, $nomorSurat) {
        // Prepare the data to update
        $data = [
            'nomor_surat' => $nomorSurat
        ];

        // Execute the update query
        $this->db->where('transaction_id', $transactionId);
        $this->db->update('Horizon_Transaction', $data); // Replace 'your_table_name' with the actual table name

        // Check if the update was successful
        return $this->db->affected_rows() > 0;
    }

    public function update_approval_status($transactionId, $approvalLevel, $approverNik, $approvalDate) {
        $approvalField = $approvalLevel === 1 ? 'status_approval1' : 'status_approval2';
        $approverField = $approvalLevel === 1 ? 'nik_1st_approval' : 'nik_2nd_approval';
        $approvalDateField = $approvalLevel === 1 ? 'tangga_approval1_test' : 'tangga_approval2_test';
    
        $data = [
            $approvalField => 1,
            $approverField => $approverNik,
            $approvalDateField => $approvalDate,
        ];
    
        $this->db->where('transaction_id', $transactionId);
        return $this->db->update('Horizon_Transaction', $data);
    }   

    public function update_approval_status1($transactionId, $approverNik, $approvalDate) {
        $approvalField = 'status_approval1';
        $approverField2 = 'nik_1st_approval';
        $approvalDateField = 'tanggal_approval1';
    
        $data = [
            $approvalField => 1,
            $approverField2 => $approverNik,
            $approvalDateField => $approvalDate,
        ];
    
        $this->db->where('transaction_id', $transactionId);
        return $this->db->update('Horizon_Transaction', $data);
    }

    public function update_approval_status2($transactionId, $approverNik, $approvalDate) {
        $approvalField = 'status_approval2';
        $approverField2 = 'nik_2nd_approval';
        $approvalDateField = 'tanggal_approval2';
    
        $data = [
            $approvalField => 1,
            $approverField2 => $approverNik,
            $approvalDateField => $approvalDate,
        ];
    
        $this->db->where('transaction_id', $transactionId);
        return $this->db->update('Horizon_Transaction', $data);
    }

    public function update_rejection_status($transactionId, $approvalLevel, $approverNik, $rejectionReason, $timestamp) {
        $approvalField = $approvalLevel === 1 ? 'status_approval1' : 'status_approval2';
        $approvalDateField = $approvalLevel === 1 ? 'tanggal_approval1' : 'tanggal_approval2';
        $approverField = $approvalLevel === 1 ? 'nik_1st_approval' : 'nik_2nd_approval';
    
        $data = [
            'reason_rejection' => $rejectionReason,
            $approvalField => 2,
            $approvalDateField => $timestamp,
            $approverField => $approverNik
        ];
    
        $this->db->where('transaction_id', $transactionId);
        return $this->db->update('Horizon_Transaction', $data);
    }
}
?>