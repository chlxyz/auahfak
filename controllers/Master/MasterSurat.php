<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterSurat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Master/MasterSuratModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master surat
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_surat');
        $this->load->view('template/main_bot');
    }

    // load data untuk master surat
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $data = $this->MasterSuratModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table
        $output = '';
        $no = 1;

        if (!empty($data)) {
        foreach ($data as $row) {
            $row->is_active = ($row->is_active == 1) ? 'Active' : 'Inactive';
    
            if ($row->is_active == 'Active') {
                $button = 'Deactivate';
            } else {
                $button = 'Activate';
            }

            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>' . $row->surat_text .'</td>';
            $output .= '<td>' . $row->is_active .'</td>';
            $output .= '<td>
                            <button class="toggle-btn" data-id="' . $row->id_surat . '" data-status="' . $row->is_active . '">' . $button . '</button>
                            <button class="btn btn-warning editButton" data-id="' . $row->id_surat . '" data-name="' . $row->surat_text . '">Edit</button>
                        </td>'; 
            $output .= '</tr>';
        }
    } else {
        $output .= '<tr>';
        $output .= '<td colspan="6" class="text-center">No Data Available</td>';
        $output .= '</tr>';
    }
        echo $output;
    }


    // check apakah jenis surat sudah ada atau belum    
    public function get_surat_data() {
        $suratText = strtoupper($this->input->post('suratText'));
        $data = $this->MasterSuratModel->get_surat_data($suratText); // panggil fungsi get_surat_data() dari model MasterSuratModel untuk check availability

        if ($data && $suratText == $data->surat_text) { // Access properties directly
            echo json_encode(['status' => 'error', 'message' => 'Data already exists']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Data available']);
        }
    }
    
    // update status inactive dan active
    public function update_status() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $status = ($input['status'] == 'Active') ? 1 : 0;
    
        $this->MasterSuratModel->update_status($id, $status);
    
        echo json_encode(['status' => 'success']);
    }

    // process form untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
            $surat_text = $this->input->post('surat_text');
            $id_surat = $this->input->post('id_surat');
    
            if ($mode === 'add') {
                $this->MasterSuratModel->insert_data($surat_text);
            } elseif ($mode === 'edit'  ) {
                $this->MasterSuratModel->update_data($id_surat, $surat_text);
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
            // echo "Successfully submitted";
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            // echo "Invalid request";
        }
    }
    
}
