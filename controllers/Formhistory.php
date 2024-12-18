<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Formhistory extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
 
        $this->load->model('Formhistory_model');
        $this->load->helper(array('form', 'url'));
    }
 
    public function index(){
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        // Panggil method load_data untuk mendapatkan data
        $data['history_data'] = $this->load_data();
        // $this->load->view('template/main_menu');
        $this->load->view('formhistory/index',$data);
        $this->load->view('template/main_bot');

    }

    public function load_data() {
        $encoded_session_id = $this->input->get('form');
        $session_id = base64_decode($encoded_session_id);
        
        $data = $this->Formhistory_model->get_history($session_id);
        
        $output = '';
        $no = 1;
        
        foreach ($data as $row) {
            $approval1status = $row->status_approval1 == 1 ? 'Approved' : ($row->status_approval1 == 0 ? 'Pending' : 'Rejected');
            $approval2status = $row->status_approval2 == 1 ? 'Approved' : ($row->status_approval2 == 0 ? 'Pending' : 'Rejected');

            $letterDetail = $this->db->select('Jenis_SIM')
            ->get_where('Horizon_Transaction_Detail', ['Transaction_ID' => $row->transaction_id])
            ->row_array()['Jenis_SIM'] ?: null;

            if ($approval1status == 'Rejected' || $approval2status == 'Rejected') {
                $overallStatus = 'Rejected';
            } elseif ($approval1status == 'Approved' && $approval2status == 'Approved') {
                $overallStatus = 'Approved';
            } else {
                $overallStatus = 'Pending';
            }
        
            if ($overallStatus == 'Approved' || $overallStatus == 'Rejected') {
                $output .= '<tr>';
                $output .= '<td>' . $no++ . '</td>';
                $output .= '<td>' . $row->tanggal_request . '</td>';
                $output .= '<td>' . $row->NIK . '</td>';
                $output .= '<td>' . $row->nama . '</td>';
                $output .= '<td>' . $row->NAME1 . '</td>';
                $output .= '<td>' . $row->jenis_surat . '</td>';
                $output .= '<td>' . $letterDetail . '</td>';
                $output .= '<td>' . $row->keterangan . '</td>';
                $output .= '<td>' . $row->nik_1st_approval . ' - ' . $row->approval1_name . '<br>' . $approval1status . '</td>';
                $output .= '<td>' . $row->nik_2nd_approval . ' - ' . $row->approval2_name . '<br>' . $approval2status . '</td>';
                $output .= '<td>' . $overallStatus . '</td>';
                $output .= '<td>' . $row->reason_rejection . '</td>';

                if (!($row->status_approval1 == 2 || $row->status_approval2 == 2 || ($row->status_approval1 == 1 && $row->status_approval2 == 2))) {
                    $output .= '<td>
                                    <button class="btn btn-warning previewBtn" 
                                        data-id="' . $row->NIK . '" 
                                        data-name="' . $row->nama . '" 
                                        data-persarea-number="' . $row->PersArea . '" 
                                        data-letter="' . $row->jenis_surat . '"
                                        data-transaction-id="' . $row->transaction_id . '">
                                        Preview
                                    </button>
                                </td>';
                } else {
                    $output .= '<td></td>';
                }

                $output .= '</tr>';
            }
        }
        if (empty($output)) {
            $output = '<tr><td colspan="12" style="text-align:center;">No Approved or Rejected Requests</td></tr>';
        }
        
        return $output;
    }

    public function generate_pdf_preview() {
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

            if ($jenisSurat == 'Paspor'){
                $jenisSurat = 'PASSPORT';
            }
            
            $commonData = $this->getCommonData($persArea, $jenisSurat, $NIKRequester, $transactionId);

            if (!$commonData['ptData'] || !$commonData['requesterData']) {
                show_error("Unable to fetch necessary data for the PDF preview.");
                return;
            }

            // Replace placeholders in template
            $isiData = $this->prepareIsiData($commonData);

            // Generate PDF
            $this->generatePDF($commonData, $isiData);
        }
    }

    private function getCommonData($persArea, $jenisSurat, $NIKRequester, $transactionId) {
        $idSurat = $this->Formhistory_model->get_surat_text($jenisSurat) ?: null;

        $ptData = $this->db->get_where('Horizon_Master_PT', ['PersArea' => $persArea, 'id_surat' => $idSurat])->row_array();
        $requesterData = $this->db->get_where('Horizon_Transaction', ['NIK' => $NIKRequester, 'transaction_id' => $transactionId])->row_array();
        $requesterDataMsNiktelp = $this->db->get_where('ms_niktelp', ['NIK' => $NIKRequester])->row_array();
        $requesterDetailData = $this->db->get_where('Horizon_Transaction_Detail', ['NIK' => $NIKRequester, 'Transaction_ID' => $transactionId])->row_array();
    
        $headerData = $this->db->select('header')->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])->row_array()['header'] ? : null;
        $footerData = $this->db->select('footer')->get_where('Horizon_Master_Template', ['id_template' => $ptData['id_template']])->row_array()['footer'] ? : null;

        $approver2name = $this->db->select('nama')->get_where('Horizon_Master_PenandaTangan', ['NIK' => $ptData['id_2nd_tandatangan']])->row_array()['nama'] ? : null;
    
        $parafData = $this->db->query("SELECT gambar_paraf FROM Horizon_Master_PenandaTangan WHERE NIK = ?", [$ptData['id_1st_tandatangan']])->row_array()['gambar_paraf'] ? : null;
        $ttdData = $this->db->query("SELECT gambar_ttd FROM Horizon_Master_PenandaTangan WHERE NIK = ?", [$ptData['id_2nd_tandatangan']])->row_array()['gambar_ttd'] ? : null;
        $capData = $this->db->query("SELECT gambar_cap FROM Horizon_Master_Cap WHERE id_cap = ?", [$ptData['id_cap']])->row_array()['gambar_cap'] ? : null;
    
        return compact('ptData', 'requesterData', 'requesterDetailData', 'requesterDataMsNiktelp', 'headerData', 'footerData', 'approver2name', 'parafData', 'ttdData', 'capData');
    }

    private function prepareIsiData($commonData) {
        $isiData = $this->Formhistory_model->get_isi($commonData['ptData']['id_isi']);

        // Format the release date
        $releaseDate = $commonData['requesterData']['tangga_approval2_test'];
        $birthDate = $commonData['requesterDataMsNiktelp']['TGLLAHIR'];
        $joinDate = $commonData['requesterDetailData']['Tanggal_Masuk'];
        $date = new DateTime($releaseDate);
        $date2 = new DateTime($birthDate);
        $date3 = new DateTime($joinDate);
        $formattedReleaseDate = $date->format('d F Y'); 
        $formattedBirthDate = $date2->format('d F Y');
        $formattedJoinDate = $date3->format('d F Y');

        // Manually replace the month name with the Indonesian equivalent
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

        // Replace the English month with the Indonesian month
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
    
        // $cap = $commonData['capData'] ? "<img style='padding-left: 350px; padding-top: -130px; padding-bottom: 120px;' src='{$commonData['capData']}' width='400' height='250' />" : '';

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
            <div style='margin-left: 50px; margin-right: 50px; padding: 0; text-align: justify;'>
            $isiData
            </div>
        </div>";

        $mpdf->WriteHTML($html);
    
        $mpdf->Output("Preview.pdf", "I");
    }
}