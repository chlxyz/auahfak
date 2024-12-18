<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterTemplate extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Master/MasterTemplateModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master template
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_template');
        $this->load->view('template/main_bot');
    }

    // load data master template
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $data = $this->MasterTemplateModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table
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
            $output .= '<td>' . $row->template_name .'</td>';
            $output .= '<td><img src="' . base_url($row->header) . '" width="100" height="100"></td>';
            $output .= '<td><img src="' . base_url($row->footer) . '" width="100" height="100"></td>';
            $output .= '<td>' . $row->is_active . '</td>';
            $output .= '<td>
                            <button class="toggle-btn" data-id="' . $row->id_template . '" data-status="' . $row->is_active . '">' . $button . '</button>
                            <button class="btn btn-warning editButton" data-id="' . $row->id_template . '" data-name="' . $row->template_name . '" data-header="' . $row->header . '" data-footer="' . $row->footer . '">Edit</button>
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
    
        $this->MasterTemplateModel->update_status($id, $status); // panggil fungsi update_status() dari model MasterTemplateModel untuk update status
        echo json_encode(['status' => 'success']);
    }
    
    // process form submission untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
            $template_name = $this->input->post('TemplateName');
            $id_template = $this->input->post('id_template');

            $uploadPath = 'uploads/';
            $header = '';
            $footer = '';
    
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $this->load->library('upload', $config);
    
            if (!empty($_FILES['Header']['name'])) {
                $this->upload->initialize($config);
                if ($this->upload->do_upload('Header')) {
                    $uploadData = $this->upload->data();
                    $header = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }
    
            if (!empty($_FILES['Footer']['name'])) {
                $this->upload->initialize($config);
                if ($this->upload->do_upload('Footer')) {
                    $uploadData = $this->upload->data();
                    $footer = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }
    
            if ($mode === 'add') {
                $this->MasterTemplateModel->insert_data($template_name, $header, $footer);
            } elseif ($mode === 'edit') {
                $this->MasterTemplateModel->update_data($id_template, $template_name, $header, $footer);
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
    
}
