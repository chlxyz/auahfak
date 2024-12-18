<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterBundler extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('Master/MasterBundlerModel');
        $this->load->helper(array('form', 'url'));
    }

    // access master bundler
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_bundler');
        $this->load->view('template/main_bot');
    }

    // get data untuk dropdowns dan input
    public function get_data() {
        $response = [
            'status' => 'success',
            'data' => [
                'persArea' => [],
                'template' => [],
                'surat' => [],
                'isi' => [],
                'approver1' => [],
                'approver2' => [],
                'cap' => []
            ]
        ];
    
        // get data untuk dropdown area
        foreach ($this->MasterBundlerModel->get_data_area() as $row) {
            $response['data']['persArea'][] = [
                'id' => $row->PERSA,
                'text' => $row->PERSA . ' - ' . $row->NAME1,
                'area_name' => $row->NAME1,
                'area_initial' => $row->NAME2,
                'area_address' => $row->STRAS
            ];
        }

        // get data untuk dropdown template
        foreach ($this->MasterBundlerModel->get_data_template() as $row) {
            if ($row->is_active == 1) { // Check if active
                $response['data']['template'][] = [
                    'id' => $row->id_template,
                    'text' => $row->template_name,
                    'header' => $row->header,
                    'footer' => $row->footer
                ];
            }
        }
    
        // get data untuk dropdown surat
        foreach ($this->MasterBundlerModel->get_data_surat() as $row) {
            if ($row->is_active == 1) { // Check if active
                $response['data']['surat'][] = [
                    'id' => $row->id_surat,
                    'text' => $row->surat_text
                ];
            }
        }
    
        // get data untuk dropdown isi
        foreach ($this->MasterBundlerModel->get_data_isi() as $row) {
            if ($row->is_active == 1) { // Check if active
                $response['data']['isi'][] = [
                    'id' => $row->id_isi,
                    'text' => $row->Judul
                ];
            }
        }
    
        // get data untuk dropdown approver 1 dan approver 2
        foreach ($this->MasterBundlerModel->get_data_penandatangan() as $row) {
            if ($row->is_active == 1) { // Check if active
                $response['data']['approver1'][] = [
                    'id' => $row->NIK,
                    'text' => $row->nama,
                    'gambar_ttd' => $row->gambar_ttd,
                    'gambar_paraf' => $row->gambar_paraf
                ];
    
                $response['data']['approver2'][] = [
                    'id' => $row->NIK,
                    'text' => $row->nama,
                    'gambar_ttd' => $row->gambar_ttd,
                    'gambar_paraf' => $row->gambar_paraf
                ];
            }
        }
    
        // get data untuk dropdown cap
        foreach ($this->MasterBundlerModel->get_data_cap() as $row) {
            if ($row->is_active == 1) { // Check if active
                $response['data']['cap'][] = [
                    'id' => $row->id_cap,
                    'text' => $row->nama_cap,
                    'gambar_cap' => $row->gambar_cap
                ];
            }
        }
        echo json_encode($response);
    }

    // access master bundler details
    public function detailsPage() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'view';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Master/master_bundler', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }
    
    // access master bundler edit details
    public function editDetails() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'edit';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_bundler', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }

    // access master bundler add
    public function add() {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'add';
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view('Master/master_bundler', ['mode' => $mode]);
        $this->load->view('template/main_bot');
    }
    
    // get data details untuk auto populate
    public function detailsData() {
        $persArea = $this->input->get('persArea');
        $idSurat = $this->input->get('id_surat');
    
        $data = $this->MasterBundlerModel->get_details($persArea, $idSurat);
    
        if ($data) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'select_persarea' => $data->PersArea,
                    'PersArea_text' => $data->PersArea_Text,
                    'PersArea_inisial' => $data->PersArea_inisial,
                    'PersArea_alamat' => $data->PersArea_alamat,
                    'select_approver1' => $data->id_1st_tandatangan,
                    'select_approver2' => $data->id_2nd_tandatangan,
                    'viewer' => $data->id_viewer,
                    'viewer_name' => $data->viewer_name,
                    'select_template' => $data->id_template,
                    'select_surat' => $data->id_surat,
                    'select_isi' => $data->id_isi,
                    'select_cap' => $data->id_cap,
                    
                    'template_name' => $data->template_name,
                    'select_template_header' => $data->template_header,
                    'select_template_footer' => $data->template_footer,
                    
                    // Approver 1 details
                    'approver1_nik' => $data->approver1_nik,
                    'approver1_name' => $data->approver1_name,
                    'approver1_ttd' => $data->approver1_ttd,
                    'approver1_paraf' => $data->approver1_paraf,
                    
                    // Approver 2 details
                    'approver2_nik' => $data->approver2_nik,
                    'approver2_name' => $data->approver2_name,
                    'approver2_ttd' => $data->approver2_ttd,
                    'approver2_paraf' => $data->approver2_paraf
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data not found']);
        }
    }
    
    // get data viewer via input NIK
    public function get_viewer_data() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $nik = $this->input->post('NIK');
            $result = $this->MasterBundlerModel->get_data_viewer($nik);
    
            if ($result) {
                echo json_encode(['status' => 'success', 'data' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Approver not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }

    // generate PDF ketika user menekan button preview
    public function generate_pdf_preview() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateId = $this->input->post('select_template');
            $headerId = $this->input->post('select_template_header');
            $footerId = $this->input->post('select_template_footer');
            $approver1NIK = $this->input->post('select_approver1');
            $approver2NIK = $this->input->post('select_approver2');
    
            $isiId = $this->input->post('select_isi');
            $capId = $this->input->post('select_cap');
            $area_name = $this->input->post('PersArea_text');
            $PersArea = $this->input->post('select_persarea');
    
            $isiQuery = "SELECT isi FROM Horizon_Master_Isi WHERE id_isi = ?";
            $lampiran1Query = "SELECT lampiran1 FROM Horizon_Master_Isi WHERE id_isi = ?";
            $lampiran2Query = "SELECT lampiran2 FROM Horizon_Master_Isi WHERE id_isi = ?";
            $headerQuery = "SELECT header FROM Horizon_Master_Template WHERE id_template = ?";
            $footerQuery = "SELECT footer FROM Horizon_Master_Template WHERE id_template = ?";
            $parafQuery = "SELECT gambar_paraf FROM Horizon_Master_PenandaTangan WHERE NIK = ?";
            $ttdQuery = "SELECT gambar_ttd FROM Horizon_Master_PenandaTangan WHERE NIK = ?";
            $capQuery = "SELECT gambar_cap FROM Horizon_Master_Cap WHERE id_cap = ?";
            $approver2NameQuery = "SELECT nama FROM Horizon_Master_PenandaTangan WHERE NIK = ?";
            $letterNumberQuery = "SELECT NOMOR_SURAT FROM ms_PersArea WHERE PERSA = ?";

            $this->SQLSRV = $this->load->database('TESTING', TRUE);
            
    
            $isiStmt = $this->SQLSRV->query($isiQuery, [$isiId]);
            $lampiran1Stmt = $this->db->query($lampiran1Query, [$isiId]);
            $lampiran2Stmt = $this->db->query($lampiran2Query, [$isiId]);
            $headerStmt = $this->db->query($headerQuery, [$templateId]);
            $footerStmt = $this->db->query($footerQuery, [$templateId]);
            $parafStmt = $this->db->query($parafQuery, [$approver1NIK]);
            $ttdStmt = $this->db->query($ttdQuery, [$approver2NIK]);
            $capStmt = $this->db->query($capQuery, [$capId]);
            $approver2NameStmt = $this->db->query($approver2NameQuery, [$approver2NIK]);
            $letterNumberStmt = $this->db->query($letterNumberQuery, [$PersArea]);
            
            $isiData = $isiStmt->row_array()['isi'];
            $lampiran1Data = $lampiran1Stmt->row_array()['lampiran1'];
            $lampiran2Data = $lampiran2Stmt->row_array()['lampiran2'];
            $headerData = $headerStmt->row_array()['header'];
            $footerData = $footerStmt->row_array()['footer'];
            $parafData = $parafStmt->row_array()['gambar_paraf'];
            $ttdData = $ttdStmt->row_array()['gambar_ttd'];
            $capData = $capStmt->row_array()['gambar_cap'];
            $approver2Name = $approver2NameStmt->row_array()['nama'];
            $letterNumberData = $letterNumberStmt->row_array()['NOMOR_SURAT'];

            $paraf = "<img src='{$parafData}' width='30' height='30' />";
            $ttd = "<img src='{$ttdData}' width='180' height='80' />";
            $cap = "
                <img style='padding-left: 350px; padding-top: -130px; padding-bottom: 120px;' src='{$capData}' width='400' height='250' />
            ";

            $isiData = str_replace(
                ['$area_name', '$paraf', '$ttd', '$approver2', '$letter_no'],
                [$area_name, $paraf, $ttd, $approver2Name, $letterNumberData],
                $isiData
            );
            
            // $sheetData = array(
            //     $isiData, $lampiran1Data, $lampiran2Data,
            // );

            require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';
    
            $mpdf = new mPDF('utf-8', array(210, 297));
            $mpdf->AddPageByArray([
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-top' => 0,
                'margin-bottom' => 0
            ]);
            $mpdf->SetDisplayMode('fullpage');

            $headerHtml = "<div style='text-align: center; border: 0px solid red; padding-top: -40px;'>
                <img src='$headerData' width='100%' />
            </div>";
            $mpdf->SetHTMLHeader($headerHtml, 'O', true);

            $footerHtml = "<div style='text-align: center; border: 0px solid red; padding-bottom: -40px;'>
                <img src='$footerData' width='100%' />
            </div>";
            $mpdf->SetHTMLFooter($footerHtml, 'O');
    
            $html = "
            <div style='position: relative; padding-top: 300px;'>
                <div style='margin-left: 100px; margin-right: 100px; text-align: justify; padding: 0; position: relative; z-index: 1;'>
                    $isiData
                </div>
            </div>
            ";
            $mpdf->WriteHTML($html);
    
            // // Add lampiran 1 if available
            // if (!empty($lampiran1Data)) {
            //     $mpdf->AddPage(); // Add a new page
            //     $mpdf->SetHTMLHeader(''); // Remove header
            //     $mpdf->SetHTMLFooter(''); // Remove footer
            //     $lampiran1Html = "<embed src=\"$lampiran1Data\" width=\"1748px\" height=\"2480px\" />";
            //     $mpdf->WriteHTML($lampiran1Html);
            // }
    
            // // Add lampiran 2 if available
            // if (!empty($lampiran2Data)) {
            //     $mpdf->AddPage(); // Add a new page
            //     $mpdf->SetHTMLHeader(''); // Remove header
            //     $mpdf->SetHTMLFooter(''); // Remove footer
            //     $lampiran2Html = "<embed src=\"$lampiran2Data\" width=\"1748px\" height=\"2480px\" />";
            //     $mpdf->WriteHTML($lampiran2Html);
            // }

            // $mpdf = new mPDF('utf-8', array(190, 236));

            // $headerHtml = "<div style='text-align: center; padding: 0;'>
            //     <img src='$headerData' width='100%' />
            // </div>";
            // $mpdf->SetHTMLHeader($headerHtml);

            // $footerHtml = "<div style='text-align: center; padding: 0;'>
            //     <img src='$footerData' width='100%' />
            // </div>";
            // $mpdf->SetHTMLFooter($footerHtml);

            // for ($i = 0; $i < count($sheetData); $i++) {
            //     $isiData = $sheetData[$i];
            //     $html = "<div style='padding-top: 170px;'>
            //                 <div style='margin-left: 50px; margin-right: 50px;'>$isiData</div>
            //             </div>";

            //     $mpdf->WriteHTML($html);

            //     if (!empty($lampiran1Data) && $i == 1) {
            //         $mpdf->AddPage();
            //         $mpdf->SetHTMLHeader('');
            //         $mpdf->SetHTMLFooter('');
                
            //         $mpdf->SetSourceFile($lampiran1Data);
            //         $templateId = $mpdf->ImportPage(1);
            //         $mpdf->UseTemplate($templateId);
            //     }
                
            //     if (!empty($lampiran2Data) && $i == 2) {
            //         $mpdf->AddPage();
            //         $mpdf->SetHTMLHeader('');
            //         $mpdf->SetHTMLFooter('');
                
            //         $mpdf->SetSourceFile($lampiran2Data);
            //         $templateId = $mpdf->ImportPage(1);
            //         $mpdf->UseTemplate($templateId);
            //     }
                

            //     Add a new page for the next content (except for the last iteration)
            //     if ($i < count($sheetData) - 1) {
            //         $mpdf->AddPage();
            //     }
            // }

            $mpdf->Output("Preview.pdf", "I");
            // unset($mpdf);
        }
    }
    
    // process form submission untuk add dan edit (update)
    public function process_form() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $mode = $this->input->post('mode');
    
            $persarea = $this->input->post('select_persarea');
            $nomor = $this->input->post('nomor') ? $this->input->post('nomor') : '';
            $persarea_text = $this->input->post('PersArea_text');
            $persarea_inisial = $this->input->post('PersArea_inisial');
            $persarea_alamat = $this->input->post('PersArea_alamat');
            $id_template = $this->input->post('select_template');
            $id_1st_tandatangan = (string)$this->input->post('select_approver1');
            $id_2nd_tandatangan = (string)$this->input->post('select_approver2');
            $id_viewer = $this->input->post('viewer');
            $id_surat = $this->input->post('select_surat');
            $id_isi = $this->input->post('select_isi');
            $id_cap = $this->input->post('select_cap');
    
            header('Content-Type: application/json');
    
            if ($mode === 'edit') {
                // Validate that approver IDs are unique
                if ($id_1st_tandatangan == $id_2nd_tandatangan) {
                    echo json_encode(['status' => 'error', 'message' => 'Approver 1 and Approver 2 cannot be the same']);
                    return;
                }
            
                if ($id_1st_tandatangan == $id_viewer || $id_2nd_tandatangan == $id_viewer) {
                    echo json_encode(['status' => 'error', 'message' => 'Viewer cannot be the same as Approver 1 or Approver 2']);
                    return;
                }
            
                $updated = $this->MasterBundlerModel->update_data(
                    $persarea, $persarea_text, $nomor, $persarea_inisial, $persarea_alamat,
                    $id_1st_tandatangan, $id_2nd_tandatangan, $id_viewer, $id_template,
                    $id_surat, $id_isi, $id_cap
                );
            
                if ($updated) {
                    echo json_encode(['status' => 'success', 'message' => 'Data successfully updated']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
                }                
            }

            if ($mode === 'add') {
                $checkExistence = $this->MasterBundlerModel->check_data_exists($persarea, $id_surat);
                
                if ($checkExistence) {
                    echo json_encode(['status' => 'error', 'message' => 'Data already exists']);
                    return;
                }
            
                if ($id_1st_tandatangan === $id_2nd_tandatangan) {
                    echo json_encode(['status' => 'error', 'message' => 'Approver 1 and Approver 2 cannot be the same']);
                    return;
                }
            
                if ($id_1st_tandatangan === $id_viewer || $id_2nd_tandatangan === $id_viewer) {
                    echo json_encode(['status' => 'error', 'message' => 'Viewer cannot be the same as Approver 1 or Approver 2']);
                    return;
                }
            
                $inserted = $this->MasterBundlerModel->insert_data(
                    $persarea, $persarea_text, $nomor, $persarea_inisial, $persarea_alamat,
                    $id_1st_tandatangan, $id_2nd_tandatangan, $id_viewer, $id_template,
                    $id_surat, $id_isi, $id_cap
                );

                echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
    
            }
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
}
