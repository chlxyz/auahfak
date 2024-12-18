<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Viewer extends CI_Controller {

    // public $nikData = '042922';
    // private $nikPersArea = '0002';

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->nikData = $this->session->userdata('login_nik');

        $langArr = array('act', 'menu', 'basic', 'time', 'confirm', 'notif');
        foreach ($langArr as $key => $value) {
            $this->lang->load($value, $this->session->userdata('site_lang'));
        }

        $this->nikData = $this->session->userdata('login_nik');

        $this->load->model('Viewer/ViewerModel');
        $this->load->helper(array('form', 'url'));
    }

    public function ViewHistory(){
        $nik = $this->nikData;

        $viewerAccess = $this->ViewerModel->get_viewer_access($nik);

        $isViewerAuthorized = false;

        foreach ($viewerAccess as $access) {
            if ($nik == $access->id_viewer) {
                $isViewerAuthorized = true;
                break;
            }
        }

        if ($isViewerAuthorized) {
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // $this->load->view('template/main_menu');
        $this->load->view('Viewer/view_history');
        $this->load->view('template/main_bot');
        } 
        else {
            redirect('http://localhost:81/portal/home.php');
        }
    }

    public function load_data() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';
    
        $nik = $this->nikData;

        $viewerAccess = $this->ViewerModel->get_viewer_access($nik);

        $isViewer = false;

        foreach ($viewerAccess as $access) {
            if ($nik == $access->id_viewer) {
                $isViewer = true;
            }
        }
        
        $output = '';
        $data = [];

        if ($isViewer) {
            $data = $this->ViewerModel->get_request_by_viewer($nik, $limit, $offset, $search);
            $uniqueData = [];
            foreach ($data as $row) {
                $key = $row->transaction_id;
                if (!isset($uniqueData[$key])) {
                    $uniqueData[$key] = $row;
                }
            }
            $data = array_values($uniqueData);
        } else {
            $output .= '<tr><td colspan="10" style="text-align:center;">You are not authorized to view this data.</td></tr>';
            echo $output;
            return;
        }

        $no = 1;

        foreach ($data as $row) {
            $persAreaObj = $this->ViewerModel->get_persarea_name($row->PersArea);
            $PersAreaName = $persAreaObj ? $persAreaObj['PersArea_Text'] : 'Unknown';     

            $approver1NIK = $row->nik_1st_approval;
            $approver2NIK = $row->nik_2nd_approval;
            $approver1Name =  $this->db->select('nama')
            ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $approver1NIK])
            ->row_array()['nama'] ?: null;
            $approver2Name =  $this->db->select('nama')
            ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $approver2NIK])
            ->row_array()['nama'] ?: null;

            $letterDetail = $this->db->select('Jenis_SIM')
            ->get_where('Horizon_Transaction_Detail', ['Transaction_ID' => $row->transaction_id])
            ->row_array()['Jenis_SIM'] ?: null;


            $approval1status = $row->status_approval1 == 1 ? 'Approved' : ($row->status_approval1 == 0 ? 'Pending' : 'Rejected');
            $approval2status = $row->status_approval2 == 1 ? 'Approved' : ($row->status_approval2 == 0 ? 'Pending' : 'Rejected');



            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>' . $row->tanggal_request . '</td>';
            $output .= '<td>' . $row->NIK . '</td>';
            $output .= '<td>' . $row->nama . '</td>';
            $output .= '<td>' . $PersAreaName . '</td>';
            $output .= '<td>' . $row->jenis_surat . '</td>';
            $output .= '<td>' . $letterDetail . '</td>';
            $output .= '<td>' . $row->keterangan . '</td>';
            if ($row->status_approval1 == 2 || $row->status_approval2 == 2) {
                $output .= '<td>'.'</td>';
                $output .= '<td>'.'</td>';
            } else {
                $output .= '<td>' . $approver1NIK . ' -' . '<br />' . $approver1Name . ' -' . '<br />' . $approval1status . '</td>';
                $output .= '<td>' . $approver2NIK . ' -' . '<br />' . $approver2Name . ' -' . '<br />' . $approval2status . '</td>';
            }
            $output .= '<td>' . $row->reason_rejection . '</td>';

            if (!($row->status_approval1 == 0 || $row->status_approval2 == 0 || $row->status_approval1 == 2 || $row->status_approval2 == 2 || ($row->status_approval1 == 1 && $row->status_approval2 == 2))) {
                $output .= '<td>
                                <button type="button" id="previewBtn" class="btn btn-info previewBtn"
                                    data-transaction-id="' . $row->transaction_id .'"
                                    data-id="' . $row->NIK . '" 
                                    data-name="' . $row->nama . '" 
                                    data-persarea="' . $PersAreaName . '" 
                                    data-persarea-number="' . $row->PersArea . '"
                                    data-letter="' . $row->jenis_surat . '">
                                    Preview
                                </button>
                            </td>';
            } else {
                $output .= '<td></td>';
            }
            
            $output .= '</tr>';
        }
        if (empty($data) || $output === '') {
            $output .= '<tr><td colspan="10" style="text-align:center;">No Request</td></tr>';
        }
        echo $output;
    }

    public function generate_pdf_preview(){
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'No input received']);
            return;
        }

        $nik = $this->nikData;
        $suratType = $input['jenisSurat'];

        $viewerAccess = $this->ViewerModel->get_viewer_access($nik);
        $data = [];

        foreach ($viewerAccess as $access) {
            if ($nik == $access->id_viewer) {
                $isViewer = true;
            }
        }

        if ($isViewer || $nik == $access->id_viewer) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode($this->input->raw_input_stream, true);
                if (empty($input)) {
                    show_error("No input data received for PDF generation.");
                    return;
                }
                
                $persArea = $input['persAreaNumber'];
                $jenisSurat = $input['jenisSurat'];
                $NIKRequester = $input['NIKRequester'];
                $transactionId = $input['transactionId'];

                $idSurat = $this->ViewerModel->get_surat_text($jenisSurat);
        
                $ptData = $this->db->get_where('Horizon_Master_PT', [
                    'PersArea' => $persArea,
                    'id_surat' => $idSurat
                ])->row_array();

                $requesterData = $this->db->get_where('Horizon_Transaction', [
                    'NIK' => $NIKRequester,
                    'transaction_id' => $transactionId
                ])->row_array();

                $requesterDetailData = $this->db->get_where('Horizon_Transaction_Detail', [
                    'NIK' => $NIKRequester,
                    'Transaction_ID' => $transactionId
                ])->row_array();

                if (!$ptData || !$requesterData) {
                    show_error("Unable to fetch necessary data for the PDF preview.");
                    return;
                }
        
                $isiData = $this->ViewerModel->get_isi($ptData['id_isi']);
    
                $birthDate = $this->db->select('TGLLAHIR')
                    ->get_where('ms_niktelp', ['NIK' => $NIKRequester])
                    ->row_array()['TGLLAHIR'] ?: null;
                
                $joinDate = $requesterDetailData['Tanggal_Masuk'];

                $releaseDate = $requesterData['tangga_approval2_test'];

                $date = new DateTime($birthDate);
                $date2 = new DateTime($joinDate);
                $date3 = new DateTime($releaseDate);
                $formattedBirthDate = $date->format('d F Y');
                $formattedJoinDate = $date2->format('d F Y');
                $formattedReleaseDate = $date3->format('d F Y');
        
                $months = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember',
                ];
        
                foreach ($months as $englishMonth => $indonesianMonth) {
                    if (strpos($formattedBirthDate, $englishMonth) !== false) {
                        $formattedBirthDate = str_replace($englishMonth, $indonesianMonth, $formattedBirthDate);
                    }
                    if (strpos($formattedJoinDate, $englishMonth) !== false) {
                        $formattedJoinDate = str_replace($englishMonth, $indonesianMonth, $formattedJoinDate);
                    }
                    if (strpos($formattedReleaseDate, $englishMonth) !== false) {
                        $formattedReleaseDate = str_replace($englishMonth, $indonesianMonth, $formattedReleaseDate);
                    }
                }
            
                $headerData = $this->db->select('header')
                    ->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])
                    ->row_array()['header'] ?: null;
    
                $footerData = $this->db->select('footer')
                    ->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])
                    ->row_array()['footer'] ?: null;
    
                $gambarParaf = $this->db->select('gambar_paraf')
                    ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_1st_tandatangan']])
                    ->row_array()['gambar_paraf'] ?: null;

                $gambarTtd = $this->db->select('gambar_ttd')
                    ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_2nd_tandatangan']])
                    ->row_array()['gambar_ttd'] ?: null;
    
                $gambarCap = $this->db->select('gambar_cap')
                    ->get_where('Horizon_Master_Cap', ['id_cap' => $ptData['id_cap']])
                    ->row_array()['gambar_cap'] ?: null;

                $approver2name = $this->db->select('nama')
                    ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_2nd_tandatangan']])
                    ->row_array()['nama'] ?: null;
    
                $requesterPosition = $this->db->select('Positions')
                    ->get_where('ms_niktelp', ['NIK' => $NIKRequester])
                    ->row_array()['Positions'] ?: null;

                $paraf = $gambarParaf ? "<img src='{$gambarParaf}' width='30' height='30' />" : '';
                $ttd = $gambarTtd ? "<img src='{$gambarTtd}' width='180' height='80' />"     : '';

                $replaceData = [
                    '$area_name' => $ptData['PersArea_Text'],
                    '$name' => $requesterData['nama'],
                    '$nik' => $requesterData['NIK'],
                    '$approver2' => $approver2name,
                    '$sim_type' => 'SIM ' . $requesterDetailData['Jenis_SIM'],
                    '$position' => $requesterPosition,
                    '$pob' => $requesterDetailData['Tempat_Lahir'],
                    '$dob' => $formattedBirthDate,
                    '$ktp' => $requesterDetailData['No_KTP'],
                    '$join_date' => $formattedJoinDate,
                    '$paraf' => $paraf,
                    '$ttd' => $ttd,
                    '$release_date' => $formattedReleaseDate,
                    '$letter_no' => $requesterData['nomor_surat']
                ];

                $isiData = str_replace(array_keys($replaceData), array_values($replaceData), $isiData);

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
                <div style='padding-top: 200px; position: relative;'>
                    <div style='margin-left: 50px; margin-right: 50px; padding: 0; text-align: justify;'>
                    $isiData
                    </div>
                </div>";

                
                $mpdf->WriteHTML($html);
        
                $mpdf->Output("Preview.pdf", "I");
            }    
        }
        else {
            show_error("Invalid request method. Only POST allowed.");
        }
    }
}