<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterArea extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $langArr = array('act','menu','basic','time','confirm','notif');
        foreach ($langArr as $key => $value) {
            $this->lang->load($value,$this->session->userdata('site_lang'));
        }
        $this->load->model('account_model');

        $this->load->model('Master/MasterAreaModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master area
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_area');
        $this->load->view('template/main_bot');
    }

    // load data table master area
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';
    
        $data = $this->MasterAreaModel->get_data($limit, $offset, $search);
        $output = '';
        $no = $offset + 1;
    
        foreach ($data as $row) {
            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>' . $this->session->userdata('login_nik') . '</td>';
            $output .= '<td>' . $row->PERSA . '</td>';
            $output .= '<td>' . $row->NAME1 . '</td>';
            $output .= '<td>' . $row->NAME2 . '</td>';
            $output .= '<td>' . $row->STRAS . '</td>';
            $output .= '<td>' . $row->NOMOR_SURAT . '</td>';
            $output .= '<td>
                            <button class="btn btn-warning editButton" data-id="' . $row->PERSA . '" data-name="' . $row->NAME1 . '" data-initial="' . $row->NAME2 . '" data-address="' . $row->STRAS . '" data-nomorsurat="' . $row->NOMOR_SURAT . '">Edit</button>
                        </td>';
            $output .= '</tr>';
        }
    
        echo $output;
    }
    
    // load data area berdasarkan persarea
    public function get_area_data() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $persa = $this->input->post('persarea');
            $result = $this->MasterAreaModel->get_data_area($persa); // panggil fungsi get_data_area() dari model MasterAreaModel untuk load data table
    
            if ($result) {
                echo json_encode(['status' => 'success', 'data' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Area not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
    
    // process form submission untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $persarea = $this->input->post('PersArea');
            $areaname = $this->input->post('AreaName');
            $areainitial = $this->input->post('AreaInisial');
            $areaaddress = $this->input->post('AreaAddress');
            $nomorsurat = $this->input->post('NomorSurat');
    
            // validasi area initial harus 3 karakter
            if (strlen($areainitial) !== 3) {
                echo json_encode(['status' => 'error', 'message' => 'Area Initial must be exactly 3 characters']);
                return;
            }

            $update_result = $this->MasterAreaModel->update_data($persarea, $areaname, $areainitial, $areaaddress, $nomorsurat);
    
            if ($update_result) {
                echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
            }
        } else {
            // Invalid request method
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
}
