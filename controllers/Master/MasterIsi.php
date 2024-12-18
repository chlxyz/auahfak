<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterIsi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Master/MasterIsiModel');
        $this->load->helper(array('form', 'url'));
    }
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_isi');
        $this->load->view('template/main_bot');
    }

    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $data = $this->MasterIsiModel->get_data($limit, $offset, $search); // panggil fungsi get_data() dari model MasterTemplateModel untuk load data table
        $output = '';
        $no = 1;
    
        foreach ($data as $row) {
            $row->is_active = ($row->is_active == 1) ? 'Active' : 'Inactive';
    
            // Fixing the if statement syntax
            if ($row->is_active == 'Active') {
                $button = 'Deactivate'; // If Active, the button should show Deactivate
            } else {
                $button = 'Activate'; // If Inactive, the button should show Activate
            }

            // $isiContent = (strlen($row->isi) > 1000) ? substr($row->isi, 0, 50) . '...' : $row->isi; //problem in here
        
            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>' . $row->Judul . '</td>';
            $output .= '<td>
                            <button class="btn btn-warning detailsButton" data-id="' . $row->id_isi . '">Details</button>
                        </td>';
            $output .= '<td>' . $row->lampiran1 . '</td>';
            $output .= '<td>' . $row->lampiran2 . '</td>';
            $output .= '<td>' . $row->is_active .'</td>';
            $output .= '<td>
                            <button class="toggle-btn" data-id="' . $row->id_isi . '" data-status="' . $row->is_active . '">' . $button . '</button>
                            <button class="btn btn-warning editButton" data-id="' . $row->id_isi . '">Edit</button>
                        </td>'; 
            $output .= '</tr>';
        }
        echo $output;
    }

    public function detailsData() {
        $id_isi = $this->input->get('id_isi');
    
        $data = $this->MasterIsiModel->get_details($id_isi);
    
        if ($data) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'isi' => $data->isi,
                    'Judul' => $data->Judul,
                    'lampiran1' => $data->lampiran1,
                    'lampiran2' => $data->lampiran2,
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data not found']);
        }
    }

    public function detailsPage() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_isi', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }

    public function editDetails() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'edit';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_isi', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }

    public function add() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'add';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_isi', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }

    public function generate_pdf_preview() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode = $this->input->post('mode');

            if ($mode === 'add') {
                $isi = $this->input->post('Isi');

                require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';
    
                $mpdf = new mPDF('utf-8', array(210, 297));
    
                $mpdf->SetHTMLHeader($headerHtml);
                $mpdf->SetHTMLFooter($footerHtml);
    
                $html = "<div style='padding-top: 300px; position: relative;'>
                            <div style='margin-left: 0px; margin-right: 0px; padding: 0; text-align: justify;'>
                            $isi
                            </div>
                        </div>";

                $mpdf->WriteHTML($html);
    
                $mpdf->Output("Preview.pdf", "I");
            }

            if ($mode === 'edit') {
                $isi = $this->input->post('Isi');

                require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';
    
                $mpdf = new mPDF('utf-8', array(210, 297));
    
                $mpdf->SetHTMLHeader($headerHtml);
                $mpdf->SetHTMLFooter($footerHtml);
    
                $html = "<div style='padding-top: 300px; position: relative;'>
                            <div style='margin-left: 0px; margin-right: 0px; padding: 0; text-align: justify;'>
                            $isi
                            </div>
                        </div>";

                $mpdf->WriteHTML($html);
    
                $mpdf->Output("Preview.pdf", "I");
            }

            if ($mode === 'view') {
                $isi = $this->input->post('Isi');

                require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';
    
                $mpdf = new mPDF('utf-8', array(210, 297));
    
                $mpdf->SetHTMLHeader($headerHtml);
                $mpdf->SetHTMLFooter($footerHtml);
    
                $html = "<div style='padding-top: 300px; position: relative;'>
                            <div style='margin-left: 0px; margin-right: 0px; padding: 0; text-align: justify;'>
                            $isi
                            </div>
                        </div>";

                $mpdf->WriteHTML($html);
    
                $mpdf->Output("Preview.pdf", "I");
            }
        }
    }

    
    public function get_surat_data() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $surat = $this->input->post('surat_text');
            $result = $this->MasterIsiModel->get_data_surat($surat);
    
            if ($result) {
                echo json_encode(['status' => 'success', 'data' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Approver not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }


    public function update_status() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $status = ($input['status'] == 'Active') ? 1 : 0;
    
        $this->MasterIsiModel->update_status($id, $status);
    
        echo json_encode(['status' => 'success']);
    }
    

        // public function process_form() {
        //     if ($this->input->server('REQUEST_METHOD') == 'POST') {
        //         $mode = $this->input->post('mode');
        //         $isi = $this->input->post('Isi');
        //         $id_isi = $this->input->post('id_isi');
        //         $lampiran1 = $this->input->post('lampiran1') ? $this->input->post('Lampiran1') : '';
        //         $lampiran2 = $this->input->post('lampiran2') ? $this->input->post('Lampiran1') : '';
        //         $Judul = $this->input->post('title');
        
        //         if ($mode === 'add') {
        //             $this->MasterIsiModel->insert_data($isi, $lampiran1, $lampiran2, $Judul);
        //         } 
                
        //         if ($mode === 'edit'  ) {
        //             $this->MasterIsiModel->update_data($isi, $lampiran1, $lampiran2, $Judul, $id_isi);
        //         }
        
        //         echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
        //     } else {
        //         echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        //         // echo "Invalid request";
        //     }
        // }
 
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
            $isi = $this->input->post('Isi');
            $judul = $this->input->post('title');
            $uploadPath = 'uploads/';
    
            // Server-side validation
            if (empty(trim($judul)) || empty(trim($isi))) {
                echo json_encode(['status' => 'error', 'message' => 'Title and Isi are required fields.']);
                return;
            }
    
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = '*';
            $config['max_size'] = 10240; // 10 MB
    
            $this->load->library('upload', $config);
    
            $lampiran1 = '';
            $lampiran2 = '';
    
            // Handle Lampiran1 Upload
            if (!empty($_FILES['Lampiran1']['name'])) {
                $config['file_name'] = time() . '_Lampiran1_' . $_FILES['Lampiran1']['name'];
                $this->upload->initialize($config);
    
                if ($this->upload->do_upload('Lampiran1')) {
                    $uploadData = $this->upload->data();
                    $lampiran1 = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }
    
            // Handle Lampiran2 Upload
            if (!empty($_FILES['Lampiran2']['name'])) {
                $config['file_name'] = time() . '_Lampiran2_' . $_FILES['Lampiran2']['name'];
                $this->upload->initialize($config);
    
                if ($this->upload->do_upload('Lampiran2')) {
                    $uploadData = $this->upload->data();
                    $lampiran2 = $uploadPath . $uploadData['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }
    
            // Process the form based on mode
            if ($mode === 'add') {
                $this->MasterIsiModel->insert_data($isi, $lampiran1, $lampiran2, $judul);
            } 
            
            if ($mode === 'edit') {
                $id_isi = $this->input->post('id_isi');
    
                // Fetch existing data if no new files are uploaded
                $existingData = $this->MasterIsiModel->get_data_by_id($id_isi);
    
                if (empty($lampiran1)) {
                    $lampiran1 = $existingData['lampiran1']; // Retain existing value
                }
                if (empty($lampiran2)) {
                    $lampiran2 = $existingData['lampiran2']; // Retain existing value
                }
    
                $this->MasterIsiModel->update_data($id_isi, $isi, $lampiran1, $lampiran2, $judul);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid mode.']);
                return;
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        }
    }  
}
