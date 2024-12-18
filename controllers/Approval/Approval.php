<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Approval extends CI_Controller {

    // private $nikData = '046496';
    // public $nikData = '027544';
    // public $nikData = '002850';

    public $nikData;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        $this->nikData = $this->session->userdata('login_nik');

        $langArr = array('act', 'menu', 'basic', 'time', 'confirm', 'notif');
        foreach ($langArr as $key => $value) {
            $this->lang->load($value, $this->session->userdata('site_lang'));
        }

        $this->load->model('account_model');
        $this->load->model('Approval/ApprovalModel');
        $this->load->helper(array('form', 'url'));
    }

    public function ApprovalRequest() {
        $nik = $this->nikData;

        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        $isApproverAuthorized = false;
    
        foreach ($approverAccess['as_first_approver'] as $access) {
            if ($nik == $access->id_1st_tandatangan) {
                $isApproverAuthorized = true;
                break;
            }
        }
    
        if (!$isApproverAuthorized) {
            foreach ($approverAccess['as_second_approver'] as $access) {
                if ($nik == $access->id_2nd_tandatangan) {
                    $isApproverAuthorized = true;
                    break;
                }
            }
        }
    
        if ($isApproverAuthorized) {
            $this->load->helper('html');
            $this->load->helper('language');
            $this->load->view('template/main_top');
            $this->load->view('Approval/approval_request');
            $this->load->view('template/main_bot');
        } else {
            redirect('http://localhost:81/portal/home.php');
        }
    }

    public function HistoryApproval(){
        $nik = $this->nikData;

        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        $isApproverAuthorized = false;
    
        foreach ($approverAccess['as_first_approver'] as $access) {
            if ($nik == $access->id_1st_tandatangan) {
                $isApproverAuthorized = true;
                break;
            }
        }

        if (!$isApproverAuthorized) {
            foreach ($approverAccess['as_second_approver'] as $access) {
                if ($nik == $access->id_2nd_tandatangan) {
                    $isApproverAuthorized = true;
                    break;
                }
            }
        }

        if ($isApproverAuthorized) {
            // Load the views for the approval request page
            $this->load->helper('html');
            $this->load->helper('language');
            $this->load->view('template/main_top');
            $this->load->view('Approval/history_approval');
            $this->load->view('template/main_bot');
        } else {
            // Redirect the user to the specified URL
            redirect('http://localhost:81/portal/home.php');
        }
    }

    public function get_history() {
        $limit = 5;
        $page = $this->input->get('page') ?: 1;
        $offset = ($page - 1) * $limit;
        $search = $this->input->get('search') ?: '';

        $nik = $this->nikData;

        $data = $this->ApprovalModel->get_history($nik, $limit, $offset, $search);

        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        $isApproverAuthorized = false;
    
        foreach ($approverAccess['as_first_approver'] as $access) {
            if ($nik == $access->id_1st_tandatangan) {
                $isApproverAuthorized = true;
                break;
            }
        }
        
        if (!$isApproverAuthorized) {
            foreach ($approverAccess['as_second_approver'] as $access) {
                if ($nik == $access->id_2nd_tandatangan) {
                    $isApproverAuthorized = true;
                    break;
                }
            }
        }
        
        $output = '';
        $no = 1;

        if ($isApproverAuthorized) {
            foreach ($data as $row) {
                $persAreaObj = $this->ApprovalModel->get_persarea_name($row->PersArea);
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

                $approval1status = $row->status_approval1 == 1 ? 'Approved' :
                                ($row->status_approval1 == 2 ? 'Rejected' : '');

                $approval2status = $row->status_approval2 == 1 ? 'Approved' :
                                ($row->status_approval2 == 2 ? 'Rejected' : '');
                
                if ($row->status_approval1 == 2 || $row->status_approval2 == 2 || ($row->status_approval1 == 1 && $row->status_approval2 == 2)) {

                }
                
                $output .= '<tr>';
                $output .= '<td>' . $no++ . '</td>';
                $output .= '<td>' . $row->tanggal_request . '</td>';
                $output .= '<td>' . $row->NIK . '</td>';
                $output .= '<td>' . $row->nama . '</td>';
                $output .= '<td>' . $PersAreaName . '</td>';
                $output .= '<td>' . $row->jenis_surat . '</td>';
                $output .= '<td>' . $letterDetail . '</td>';
                $output .= '<td>' . $row->keterangan . '</td>';
                $output .= '<td>' . $approver1NIK . ' -' . '<br />' . $approver1Name . ' -' . '<br />' . $approval1status . '</td>';
                $output .= '<td>' . $approver2NIK . ' -' . '<br />' . $approver2Name . ' -' . '<br />' . $approval2status . '</td>';
                $output .= '<td>' . $row->reason_rejection . '</td>';
                
                if (!($row->status_approval1 == 2 || $row->status_approval2 == 2 || ($row->status_approval1 == 1 && $row->status_approval2 == 2))) {
                    $output .= '<td>
                                    <button class="btn btn-warning previewBtn" 
                                        data-id="' . $row->NIK . '" 
                                        data-name="' . $row->nama . '" 
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
        } else {
            $output = '<tr><td colspan="9" class="text-center">No Request History</td></tr>';
        }
        echo $output;
    }

    public function approve() {
        ob_clean();
        header('Content-Type: application/json');
        date_default_timezone_set('UTC');
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'No input received']);
            exit();
        }
    
        $NIKRequester = $input['NIKRequester'];
        $letterType = $input['letterType'];
        $transactionId = $input['transactionId'];
        $nik = $this->nikData;
        $rejectionReason = isset($input['rejectionReason']) ? $input['rejectionReason'] : null;
    
        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        $transaction = $this->ApprovalModel->get_transaction($NIKRequester, $letterType, $transactionId);
    
        foreach ($approverAccess['as_first_approver'] as $access) {
            if ($transaction['PersArea'] === $access->PersArea) {
                if ($rejectionReason) {
                    $statusUpdated = $this->ApprovalModel->update_rejection_status(
                        $transaction['transaction_id'],
                        1,
                        $nik,
                        $rejectionReason,
                        date('Y-m-d H:i:s')
                    );
                    echo json_encode([
                        'success' => $statusUpdated,
                        'message' => $statusUpdated ? 'First approver rejection successfully processed' : 'Rejection failed'
                    ]);
                } else {
                    $statusUpdated = $this->ApprovalModel->update_approval_status(
                        $transaction['transaction_id'],
                        1,
                        $nik,
                        date('Y-m-d H:i:s')
                    );
                    echo json_encode([
                        'success' => $statusUpdated,
                        'message' => $statusUpdated ? 'First approver approval successfully processed' : 'Approval failed'
                    ]);
                }
                exit();
            }
        }
        foreach ($approverAccess['as_second_approver'] as $access) {
            if ($transaction['PersArea'] === $access->PersArea) {
                if ($rejectionReason) {
                    $statusUpdated = $this->ApprovalModel->update_rejection_status(
                        $transaction['transaction_id'],
                        2,
                        $nik,
                        $rejectionReason,
                        date('Y-m-d H:i:s')
                    );
                    echo json_encode([
                        'success' => $statusUpdated,
                        'message' => $statusUpdated ? 'Second approver rejection successfully processed' : 'Rejection failed'
                    ]);
                } else {
                    $statusUpdated = $this->ApprovalModel->update_approval_status(
                        $transaction['transaction_id'],
                        2,
                        $nik,
                        date('Y-m-d H:i:s')
                    );
    
                    if ($statusUpdated) {
                        $year = date('Y');
                        $month = date("m");

                        $monthInRoman = [
                            '01' => 'I',
                            '02' => 'II',
                            '03' => 'III',
                            '04' => 'IV',
                            '05' => 'V',
                            '06' => 'VI',
                            '07' => 'VII',
                            '08' => 'VIII',
                            '09' => 'IX',
                            '10' => 'X',
                            '11' => 'XI',
                            '12' => 'XII'
                        ];
                        
                        $romanMonth = $monthInRoman[$month] ? : 'Unknown'; 

                        
                        $companycode = $this->ApprovalModel->get_companycode($transaction['PersArea']);
                        $totalTransaction = $this->ApprovalModel->get_total_transaction($year, $transaction['PersArea']);
                        $nextTransactionNumber = $totalTransaction + 1;
                        $formattedNo = sprintf('%03d', $nextTransactionNumber);
                        $formatSurat = $this->ApprovalModel->get_format_surat_number($transaction['PersArea']);
                        $nomorSurat = str_replace(
                            ['$NO', '$MONTH', '$CC', '$YEAR'],
                            [$formattedNo, $romanMonth, $companycode, $year],
                            $formatSurat
                        );
                        $this->ApprovalModel->update_nomor_surat($transaction['transaction_id'], $nomorSurat);
    
                        echo json_encode([
                            'success' => true,
                            'message' => 'Second approver approval successfully processed. Nomor surat generated.',
                            'nomor_surat' => $nomorSurat
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Second approver approval failed'
                        ]);
                    }
                }
                exit();
            }
        }
        echo json_encode(['success' => false, 'message' => 'No matching approver role found for this transaction']);
        exit();
    }
    
    public function generate_pdf_preview_history() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode($this->input->raw_input_stream, true);
            if (empty($input)) {
                show_error("No input data received for PDF generation.");
                return;
            }

            $persArea = $input['persAreaNumber'];
            $jenisSurat = $input['jenisSurat'];
            $NIKRequester = $input['NIKRequester'];

            $commonData = $this->getCommonData($persArea, $jenisSurat, $NIKRequester);

            if (!$commonData['ptData'] || !$commonData['requesterData']) {
                show_error("Unable to fetch necessary data for the PDF preview.");
                return;
            }

            $isiData = $this->prepareIsiData($commonData);

            // Generate PDF
            $this->generatePDF($commonData, $isiData);
        }
    }    

    private function getCommonData($persArea, $jenisSurat, $NIKRequester) {
        $idSurat = $this->ApprovalModel->get_surat_text($jenisSurat) ?: null;

        $ptData = $this->db->get_where('Horizon_Master_PT', ['PersArea' => $persArea, 'id_surat' => $idSurat])->row_array();
        $requesterData = $this->db->get_where('Horizon_Transaction', ['NIK' => $NIKRequester])->row_array();
        $requesterDataMsNiktelp = $this->db->get_where('ms_niktelp', ['NIK' => $NIKRequester])->row_array();
        $requesterDetailData = $this->db->get_where('Horizon_Transaction_Detail', ['NIK' => $NIKRequester])->row_array();
    
        $headerData = $this->db->select('header')->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])->row_array()['header'] ? : null;
        $footerData = $this->db->select('footer')->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])->row_array()['footer'] ? : null;

        $approver2name = $this->db->select('nama')->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_2nd_tandatangan']])->row_array()['nama'] ? : null;
    
        $parafData = $this->db->query("SELECT gambar_paraf FROM Horizon_Master_PenandaTangan WHERE NIK = ?", [$ptData['id_1st_tandatangan']])->row_array()['gambar_paraf'] ? : null;
        $ttdData = $this->db->query("SELECT gambar_ttd FROM Horizon_Master_PenandaTangan WHERE NIK = ?", [$ptData['id_2nd_tandatangan']])->row_array()['gambar_ttd'] ? : null;
        $capData = $this->db->query("SELECT gambar_cap FROM Horizon_Master_Cap WHERE id_cap = ?", [$ptData['id_cap']])->row_array()['gambar_cap'] ? : null;
    
        return compact('ptData', 'requesterData', 'requesterDetailData', 'requesterDataMsNiktelp', 'headerData', 'footerData', 'approver2name', 'parafData', 'ttdData', 'capData');
    }
    
    private function prepareIsiData($commonData) {
        $isiData = $this->ApprovalModel->get_isi($commonData['ptData']['id_isi']);

        $releaseDate = $commonData['requesterData']['tangga_approval2_test'];
        $birthDate = $commonData['requesterDataMsNiktelp']['TGLLAHIR'];
        $joinDate = $commonData['requesterDetailData']['Tanggal_Masuk'];
        $date = new DateTime($releaseDate);
        $date2 = new DateTime($birthDate);
        $date3 = new DateTime($joinDate);
        $formattedReleaseDate = $date->format('d F Y');
        $formattedBirthDate = $date2->format('d F Y');
        $formattedJoinDate = $date3->format('d F Y');

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
            if (strpos($formattedReleaseDate, $englishMonth) !== false) {
                $formattedReleaseDate = str_replace($englishMonth, $indonesianMonth, $formattedReleaseDate);
            }
            if (strpos($formattedBirthDate, $englishMonth) !== false) {
                $formattedBirthDate = str_replace($englishMonth, $indonesianMonth, $formattedBirthDate);
            }
            if (strpos($formattedJoinDate, $englishMonth) !== false) {
                $formattedJoinDate = str_replace($englishMonth, $indonesianMonth, $formattedJoinDate);
            }
        }
    
        $isiData = str_replace(
            ['$area_name', '$name', '$nik', '$approver2', '$sim_type', '$position', '$paraf', '$ttd', '$pob', '$dob', '$ktp', '$join_date', '$release_date', '$letter_no'],
            [
                $commonData['ptData']['PersArea_Text'],
                $commonData['requesterData']['nama'], 
                $commonData['requesterData']['NIK'], 
                $commonData['approver2name'], 
                'SIM ' . $commonData['requesterDetailData']['Jenis_SIM'], 
                $commonData['requesterDetailData']['Positions'], 
                "<img src='{$commonData['parafData']}' width='30' height='30' />", 
                "<img src='{$commonData['ttdData']}' width='180' height='80' />", 
                $commonData['requesterDetailData']['Tempat_Lahir'],
                $formattedBirthDate,
                $commonData['requesterDetailData']['No_KTP'], 
                $formattedJoinDate,
                $formattedReleaseDate,
                $commonData['requesterData']['nomor_surat']
            ],
            $isiData
        );

        return $isiData;
    }

    private function generatePDF($commonData, $isiData) {
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
            <img src='{$commonData['headerData']}' width='100%' />
        </div>";
        $mpdf->SetHTMLHeader($headerHtml, 'O', true);

        $footerHtml = "<div style='text-align: center; border: 0px solid red; padding-bottom: -40px;'>
            <img src='{$commonData['footerData']}' width='100%' />
        </div>";
        $mpdf->SetHTMLFooter($footerHtml, 'O');

        $html = "
        <div style='padding-top: 200px; position: relative;'>
            <div style='margin-left: 50px; margin-right: 50px; text-align: justify; padding: 0;'>
            $isiData
            </div>
        </div>";

        $mpdf->WriteHTML($html);
    
        $mpdf->Output("Preview.pdf", "I");
    }

    public function generate_pdf_preview() {
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'No input received']);
            return;
        }
    
        $nik = $this->nikData;
        $isFirstApprover = false;
        $isSecondApprover = false;
        $suratType = $input['jenisSurat'];
    
        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        foreach ($approverAccess['as_first_approver'] as $access) {
            if ($nik == $access->id_1st_tandatangan) {
                $isFirstApprover = true;
                break;
            }
        }
    
        if (!$isFirstApprover) {
            foreach ($approverAccess['as_second_approver'] as $access) {
                if ($nik == $access->id_2nd_tandatangan) {
                    $isSecondApprover = true;
                    break;
                }
            }
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $persArea = $input['persAreaNumber'];
            $jenisSurat = $input['jenisSurat'];
            $NIKRequester = $input['NIKRequester'];
            $transactionId = $input['transactionId'];
    
            $idSurat = $this->ApprovalModel->get_surat_text($jenisSurat);
    
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
    
            $isiData = $this->ApprovalModel->get_isi($ptData['id_isi']);
    
            $birthDate = $this->db->select('TGLLAHIR')
                ->get_where('ms_niktelp', ['NIK' => $NIKRequester])
                ->row_array()['TGLLAHIR'] ?: null;
            
            $joinDate = $requesterDetailData['Tanggal_Masuk'];
    
            $date = new DateTime($birthDate);
            $date2 = new DateTime($joinDate);
            $formattedBirthDate = $date->format('d F Y');
            $formattedJoinDate = $date2->format('d F Y');
    
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
                $formattedBirthDate = str_replace($englishMonth, $indonesianMonth, $formattedBirthDate);
                $formattedJoinDate = str_replace($englishMonth, $indonesianMonth, $formattedJoinDate);
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
    
            $approver2name = $this->db->select('nama')
                ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_2nd_tandatangan']])
                ->row_array()['nama'] ?: null;
    
            $requesterPosition = $this->db->select('Positions')
                ->get_where('ms_niktelp', ['NIK' => $NIKRequester])
                ->row_array()['Positions'] ?: null;
    
            $letterNumber = $this->db->select('NOMOR_SURAT')
                ->get_where('ms_PersArea', ['PERSA' => $requesterData['PersArea']])
                ->row_array()['NOMOR_SURAT'] ?: null;
    
            $paraf = $gambarParaf ? "<img src='{$gambarParaf}' width='29' height='20' />" : '';
    
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
                '$letter_no' => $letterNumber
            ];
    
            foreach ($replaceData as $placeholder => $value) {
                if ($isFirstApprover && $placeholder === '$paraf') {
                    continue;
                }
                $isiData = str_replace($placeholder, $value, $isiData);
            }
    
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
    
        } else {
            show_error("Invalid request method. Only POST allowed.");
        }
    }
    

    public function load_data() {
        $nik = $this->nikData;

        $approverAccess = $this->ApprovalModel->get_approver_access($nik);
    
        $data = [];

        foreach ($approverAccess['as_first_approver'] as $access) {
            $result = $this->ApprovalModel->get_requests_by_approver1($nik, $access->PersArea, $access->id_surat);
            if (!empty($result)) {
                $data = array_merge($data, $result);
            }
        }

        foreach ($approverAccess['as_second_approver'] as $access) {
            $result = $this->ApprovalModel->get_requests_by_approver2($nik, $access->PersArea, $access->id_surat);
            if (!empty($result)) {
                $data = array_merge($data, $result);
            }
        }
    
        $output = '';
        $no = 1;
    
        foreach ($data as $row) {
            if ($row['status_approval1'] == 1 && $row['status_approval2'] == 1 || $row['status_approval1'] == 2 || $row['status_approval2'] == 2) {
                continue;
            }

            $persAreaObj = $this->ApprovalModel->get_persarea_name($row['PersArea']);
            $PersAreaName = $persAreaObj ? $persAreaObj['PersArea_Text'] : 'Unknown';     

            $approver1NIK = $row['nik_1st_approval'];

            $approver2NIK = $row['nik_2nd_approval'];

            $approver1Name =  $this->db->select('nama')
            ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $approver1NIK])
            ->row_array()['nama'] ?: null;

            $approver2Name =  $this->db->select('nama')
            ->get_where('Horizon_Master_PenandaTangan', ['NIK' => $approver2NIK])
            ->row_array()['nama'] ?: null;

            $letterDetail = $this->db->select('Jenis_SIM')
            ->get_where('Horizon_Transaction_Detail', ['Transaction_ID' => $row['transaction_id']])
            ->row_array()['Jenis_SIM'] ?: null;
        
            $approval1status = $row['status_approval1'] == 1 ? 'Approved' :
                ($row['status_approval1'] == 0 ? 'Pending' : 'Rejected');
                
            $approval2status = $row['status_approval2'] == 1 ? 'Approved' :
                ($row['status_approval2'] == 0 ? 'Pending' : 'Rejected');
        
            $output .= '<tr>';
            $output .= '<td>' . $no++ . '</td>';
            $output .= '<td>
                            <button class="btn btn-warning actionButton" 
                                data-transaction="' . $row['transaction_id'] . '"
                                data-id="' . $row['NIK'] . '" 
                                data-name="' . $row['nama'] . '" 
                                data-persarea="' . $PersAreaName . '"
                                data-persarea-number="' . $row['PersArea'] . '" 
                                data-letter="' . $row['jenis_surat'] . '">'
                                . $row['tanggal_request'] .
                            '</button>
                        </td>';
            $output .= '<td>' . $row['NIK'] . '</td>';
            $output .= '<td>' . $row['nama'] . '</td>';
            $output .= '<td>' . $PersAreaName . '</td>';
            $output .= '<td>' . $row['jenis_surat'] . '</td>';
            $output .= '<td>' . $letterDetail . '</td>';
            $output .= '<td>' . $row['keterangan'] . '</td>';
            $output .= '<td>' . $approver1NIK . ' -' . '<br />' . $approver1Name . ' -' . '<br />' . $approval1status . '</td>';
            $output .= '<td>' . $approver2NIK . ' -' . '<br />' . $approver2Name . ' -' . '<br />' . $approval2status . '</td>';
            $output .= '</tr>';
        }
    
        if (empty($data) || $output === '') {
            $output = '<tr><td colspan="10" style="text-align:center;">No Request</td></tr>';
        }
    
        echo $output;
    }
}