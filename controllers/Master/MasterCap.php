<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterCap extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Master/MasterCapModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master cap
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_cap');
        $this->load->view('template/main_bot');
    }

    // load data master cap
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';
        
        $data = $this->MasterCapModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table // panggil fungsi get_data() dari model MasterCapModel untuk load data table
        $output = '';
        $no = 1;

        if (!empty($data)) {
        foreach ($data as $row) {
            $row->is_active = ($row->is_active == 1) ? 'Active' : 'Inactive';
    
            if ($row->is_active == 'Active') {
                $button = 'Deactivate'; // jika aktif, buttonya berubah menjadi diactivate
            } else {
                $button = 'Activate'; // jika tidak aktif, buttonnya berubah menjadi activate
            }
    
            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>' . htmlspecialchars($row->nama_cap) . '</td>';
            $output .= '<td><img src="' . base_url($row->gambar_cap) . '" width="100" height="100"></td>';
            $output .= '<td>' . $row->is_active .'</td>';

            $output .= '<td>
                            <button class="toggle-btn" data-id="' . $row->id_cap . '" data-status="' . $row->is_active . '">' . $button . '</button>
                            <button class="btn btn-warning editButton" data-id="' . $row->id_cap . '" data-name="' . htmlspecialchars($row->nama_cap) . '" data-image="' . htmlspecialchars($row->gambar_cap) . '">Edit</button>
                        </td>'; 
            $output .= '</tr>';
        }
        } else {
            // Output jika data kosong
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
        $status = ($input['status'] == 'Active') ? 1 : 0; // Convert status ke 1 or 0 untuk DB

        $this->MasterCapModel->update_status($id, $status); // panggil fungsi update_status() dari model MasterCapModel untuk update status active dan inactive
    
        // return respone
        echo json_encode(['status' => 'success']);
    }
    
    // process form submission untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
            $nama_cap = $this->input->post('StampName');
            $id_cap = $this->input->post('id_cap');
            $uploadPath = 'uploads/';
            $gambar_cap = '';
            
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $this->load->library('upload', $config);
            
            if (!empty($_FILES['Stamp']['name']) && $this->upload->do_upload('Stamp')) {
                $uploadData = $this->upload->data();
                $gambar_cap = $uploadPath . $uploadData['file_name'];
            } else {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
            
            if ($mode === 'add') {
                $this->MasterCapModel->insert_data($nama_cap, $gambar_cap);
            } elseif ($mode === 'edit'  ) {
                $this->MasterCapModel->update_data($id_cap, $nama_cap, $gambar_cap);
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
            // echo "Successfully submitted";
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            // echo "Invalid request";
        }
    }
}
