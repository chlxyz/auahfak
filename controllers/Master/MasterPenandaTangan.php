<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterPenandaTangan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        $this->load->model('Master/MasterPenandaTanganModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master penanda tangan
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_penandatangan');
        $this->load->view('template/main_bot');
    }

    // load data master penanda tangan
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $data = $this->MasterPenandaTanganModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table
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
            $output .= '<td>' . $row->nama . '</td>';
            $output .= '<td><img src="' . base_url($row->gambar_ttd) . '" width="100" height="100"></td>';
            $output .= '<td><img src="' . base_url($row->gambar_paraf) . '" width="100" height="100"></td>';            
            $output .= '<td>' . $row->is_active .'</td>';
            $output .= '<td>
                            <button class="toggle-btn" data-id="' . $row->NIK. '" data-status="' . $row->is_active . '">' . $button . '</button>
                            <button class="btn btn-warning editButton" data-id="' . $row->NIK . '" data-name="' . $row->nama . '"  data-ttd="' . $row->gambar_ttd . '" data-paraf="' . $row->gambar_paraf . '">Edit</button>
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

    // update status active dan inactive
    public function update_status() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $status = ($input['status'] == 'Active') ? 1 : 0;
    
        $this->MasterPenandaTanganModel->update_status($id, $status); // panggil fungsi update_status() dari model MasterPenandaTanganModel untuk update status active dan inactive
    
        echo json_encode(['status' => 'success']);
    }
    
    // get data approver via NIK input
    public function get_approver_data() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $nik = $this->input->post('NIK');
            $result = $this->MasterPenandaTanganModel->get_data_approver($nik); // panggil fungsi get_data_approver() dari model MasterPenandaTanganModel untuk get data approver via NIK input
    
            if ($result) {
                echo json_encode(['status' => 'success', 'data' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Approver not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
    
    // process form submission untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
            $NIK = $this->input->post('approver_nik');
            $nama = $this->input->post('approver_name');
            
            $uploadPath = 'uploads/';
            $gambar_ttd = '';
            $gambar_paraf = '';
    
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $this->load->library('upload', $config);
    
            if (!empty($_FILES['approver_ttd']['name'])) {
                $this->upload->initialize($config);
    
                if ($this->upload->do_upload('approver_ttd')) {
                    $uploadData = $this->upload->data();
                    $gambar_ttd = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }
    
            if (!empty($_FILES['approver_paraf']['name'])) {
                $config['file_name'] = 'paraf_' . time();
                $this->upload->initialize($config);
    
                if ($this->upload->do_upload('approver_paraf')) {
                    $uploadData = $this->upload->data();
                    $gambar_paraf = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // check apakah NIK sudah ada atau belum
            $existingData = $this->MasterPenandaTanganModel->get_by_nik($NIK);
    
            if ($mode === 'add') {
                if ($existingData) {
                    echo json_encode(['status' => 'error', 'message' => 'Approver is already added']);
                    return;
                }
                $this->MasterPenandaTanganModel->insert_data($NIK, $nama, $gambar_ttd, $gambar_paraf);
            } elseif ($mode === 'edit') {
                if (!$existingData) {
                    echo json_encode(['status' => 'error', 'message' => 'NIK does not exist for editing']);
                    return;
                }
                $this->MasterPenandaTanganModel->update_data($NIK, $nama, $gambar_ttd, $gambar_paraf);
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
}
