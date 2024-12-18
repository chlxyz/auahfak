<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterPT extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Master/MasterPTModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master pt
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_pt');
        $this->load->view('template/main_bot');
    }

    // load data table
    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $data = $this->MasterPTModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table
        $output = '';
        $no = 1;
    
        if (!empty($data)) {
            foreach ($data as $row) {
                $id_surat = $row->id_surat;
                $letter_type_row = $this->MasterPTModel->get_letter_type($id_surat); // convert id_surat ke surat_text (untuk munculin jenis surat)

                $output .= '<tr>';
                $output .= '<td>' . $no++ . '</td>';
                $output .= '<td>' . $row->PersArea . '</td>';
                $output .= '<td>' . $row->PersArea_Text . '</td>';
                $output .= '<td>
                                <button class="btn btn-warning detailsButton" data-id="' . $row->PersArea . '" data-name="' . $row->id_surat . '">Details</button>
                            </td>';
    
                if ($letter_type_row && isset($letter_type_row->surat_text)) {
                    $output .= '<td>' . $letter_type_row->surat_text . '</td>';
                } else {
                    $output .= '<td>Unknown</td>';
                }
    
                $output .= '<td>
                                <button class="btn btn-warning editButton" data-id="' . $row->PersArea . '" data-name="' . $row->id_surat . '">Edit</button>
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
}
