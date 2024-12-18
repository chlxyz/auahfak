<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Formstatus extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
 
        $this->load->model('Formstatus_model');
        $this->load->helper(array('form', 'url'));
    }
 
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $data['history_data'] = $this->load_data();
        $this->load->view('formstatus/index',$data);
        $this->load->view('template/main_bot');

    }
 
    public function load_data() {
        $data = $this->Formstatus_model->get_history();
    
        $output = '';
        $no = 1;
    
        foreach ($data as $row) {
            $approval1status = $row->status_approval1 == 1 ? 'Approved' : ($row->status_approval1 == 0 ? 'Pending' : 'Rejected');
            $approval2status = $row->status_approval2 == 1 ? 'Approved' : ($row->status_approval2 == 0 ? 'Pending' : 'Rejected');
    
            if ($approval1status == 'Rejected' || $approval2status == 'Rejected') {
                $overallStatus = 'Rejected'; // If either approval is rejected
            } elseif ($approval1status == 'Approved' && $approval2status == 'Approved') {
                $overallStatus = 'Approved'; // If both are approved
            } else {
                $overallStatus = 'Pending'; // In all other cases (e.g., one is approved and the other is pending)
            }
    
            // Only show rows that are Pending
            if ($overallStatus == 'Pending') {
                $output .= '<tr>';
                $output .= '<td>' . $no++ . '</td>';
                $output .= '<td>' . $row->tanggal_request . '</td>';
                $output .= '<td>' . $row->NIK . '</td>';
                $output .= '<td>' . $row->nama . '</td>';
                $output .= '<td>' . $row->NAME1 . '</td>';
                $output .= '<td>' . $row->jenis_surat . '</td>';
                $output .= '<td>' . $row->keterangan . '</td>';
                $output .= '<td>' . $row->nik_1st_approval . ' - ' . $row->approval1_name . '<br>' . $approval1status . '</td>';
                $output .= '<td>' . $row->nik_2nd_approval . ' - ' . $row->approval2_name . '<br>' . $approval2status . '</td>';
                $output .= '<td>' . $overallStatus . '</td>';
                $output .= '</tr>';
            }
        }
    
        // If there are no pending requests, show a message
        if (empty($output)) {
            $output = '<tr><td colspan="9" style="text-align:center;">No Request</td></tr>';
        }
    
        return $output;
    }
}