<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Forms extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model("FormsModel");
        $this->load->database();
    }


    public function employeeProfile()
    {
        $this->load->model('Employee_model');
        $data = $this->Employee_model->getEmployeeProfileOdoo();

    
        $this->load->view('forms/index', $data);
    }

    public function fetch_embassy_names() {
        $this->load->model('FormsModel');
        $embassies = $this->FormsModel->get_embassy_name();

        echo json_encode($embassies);
    }

    public function fetch_embassy_address() {
        if ($this->input->post('embassy_name')) {
            $embassy_name = $this->input->post('embassy_name');

            $this->load->model('FormsModel');
            $address = $this->FormsModel->get_embassy_address($embassy_name);

            echo $address;
        }
    }
    
    public function index() {
        date_default_timezone_set('Asia/Jakarta');
        $db = $this->load->database('default', TRUE);
        
        $data['tanggal_pengajuan'] = date("Y-m-d");
        $data['banks'] = $this->FormsModel->get_bank_name();
        $data['countrys'] = $this->FormsModel->get_country_name();
        $data['embassys'] = $this->FormsModel->get_embassy_name();
    
        $encoded_session_id = $this->input->get('form');
        $session_id = base64_decode($encoded_session_id);
        
        if ($session_id !== false) {
            $session_data = $this->FormsModel->get_data_by_session($session_id);
            $json_decode = $this->FormsModel->getEmployeeProfileOdoo($session_id);
            $getbanks = $this->FormsModel->get_bank_name();
            $getcountrys = $this->FormsModel->get_country_name();
            $getembassys = $this->FormsModel->get_embassy_name();
    
            if ($session_data) {
                $data['json_decode'] = $json_decode;
                $data['session_data'] = $session_data;
                $data['banks'] = $getbanks;
                $data['countrys'] = $getcountrys;
                $data['embassys'] = $getembassys;
            } else {
                $data['error_message'] = "Data tidak ditemukan untuk sessionid: " . $session_id;
            }
        } else {
            $data['error_message'] = "Sessionid tidak valid.";
        }
    
        $data['letter_types'] = $this->FormsModel->get_letter_types();
    
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->view('template/main_top');
        $this->load->view("forms/index", $data);
        $this->load->view('template/main_bot');
    }


    public function load_letter_form() {
        $letter_type = $this->input->post('letter_type');
        log_message('debug', 'Letter type received: ' . $letter_type);
   
        if ($letter_type == 'KPR') {
            $getbanks = $this->FormsModel->get_bank_name();
            $data['banks'] = $getbanks;
            $this->load->view('template_form/kpr-letter', $data);
        } elseif ($letter_type == 'SIM') {
            $this->load->view('template_form/sim-letter');
        } elseif ($letter_type == 'Jaminan RS') {
            $this->load->view('template_form/jaminan-rs-letter');
        } elseif ($letter_type == 'VISA') {
            $getcountrys = $this->FormsModel->get_country_name();
            $getembassys = $this->FormsModel->get_embassy_name();
            $data['countrys'] = $getcountrys;
            $data['embassys'] = $getembassys;
            $this->load->view('template_form/visa-letter', $data);
        } elseif ($letter_type == 'PASSPORT') {
            $this->load->view('template_form/paspor-letter');
        } else {
            echo 'Form not found.';
        }
    }
    
    public function ajax_submit() {
        if ($this->input->is_ajax_request()) {
            $transaction_id = $this->input->post('Transaction_ID');
            $name = $this->input->post('name');
            $nik = $this->input->post('nik');
            $no_ktp = $this->input->post('no_ktp');
            $posisi_jabatan = $this->input->post('posisi_jabatan');
            $tempat_lahir = $this->input->post('tempat_lahir');
            $tanggal_masuk = $this->input->post('tanggal_masuk');
            $letter_type = $this->input->post('letter_type');
            $nama_bank = $this->input->post('bank_name');
            $tujuan_negara = $this->input->post('tujuan_negara');
            $tujuan_kedutaan = $this->input->post('tujuan_kedutaan');
            $nominal_kpr = $this->input->post('nominal_kpr');
            $bulan_kpr = $this->input->post('bulan_kpr');
            $penghasilan_pasangan = $this->input->post('penghasilan_pasangan');
            $sim = $this->input->post('sim');
            $kpr = $this->input->post("kpr");
            $visa = $this->input->post("visa");
            $paspor = $this->input->post("paspor");
            $jaminan_rs = $this->input->post("jaminan_rs");
            $keterangan = $this->input->post("keterangan");
    
            // Check if the user has already submitted the same letter type
            if ($letter_type == "SIM") {
                // Check if the user has already submitted the same SIM type
                $existing_sim_submission = $this->FormsModel->check_existing_sim_submission($nik, $sim);
                if ($existing_sim_submission) {
                    echo json_encode(['status' => 'error', 'message' => "You have already submitted a request for this SIM type."]);
                    return;
                }
            } else {
                // For other letter types, check using jenis_surat
                $existing_submission = $this->FormsModel->check_existing_submission($nik, $letter_type);
                if (!$existing_submission) {
                    echo json_encode(['status' => 'error', 'message' => "You have already submitted a $letter_type request."]);
                    return;
                }
            }
    
            // visa data
            if ($letter_type == "VISA") {
                if (!empty($name) && !empty($nik) && !empty($letter_type) && !empty($tujuan_negara) && !empty($tujuan_kedutaan)) {
                    $result = $this->FormsModel->insert_visa_data($name, $nik, $letter_type, $tujuan_negara, $tujuan_kedutaan);
                    echo json_encode(['status' => 'success', 'message' => 'Terimakasih Telah Mengirim Form!']);
                    return;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Data not filled.']);
                    return;
                }
            }
    
            // sim form
            if ($letter_type == "SIM") {
                if (!empty($name) && !empty($nik) && !empty($sim) && !empty($keterangan) && !empty($no_ktp) && !empty($tempat_lahir) && !empty($tanggal_masuk) && !empty($posisi_jabatan)) {
                            // Check if the user has already submitted the same SIM type
                    $existing_sim_submission = $this->FormsModel->check_existing_sim_submission($nik, $sim);
                    if ($existing_sim_submission) {
                        echo json_encode(['status' => 'error', 'message' => "You have already submitted a request for this SIM type."]);
                        return;
                    }
                    $result = $this->FormsModel->insert_sim_data($name, $nik, $sim, $keterangan, $letter_type, $no_ktp, $tempat_lahir, $tanggal_masuk, $posisi_jabatan, $transaction_id);
                    $transaction_data = $this->FormsModel->get_data_by_transaction_id($transaction_id);
                    echo json_encode(['status' => 'success', 'message' => 'Terimakasih Telah Mengirim Form!']);
                    return;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Data not filled.']);
                    return;
                }
            }
    
            // kpr form
            if ($letter_type == "KPR") {
                if (!empty($name) && !empty($nik) && !empty($letter_type) && !empty($keterangan)) {
                    $result = $this->FormsModel->insert_kpr_data($name, $nik, $letter_type, $nama_bank, $nominal_kpr, $bulan_kpr, $penghasilan_pasangan, $keterangan);
                    $transaction_data = $this->FormsModel->get_data_by_transaction_id($transaction_id);
                    echo json_encode(['status' => 'success', 'message' => 'Terimakasih Telah Mengirim Form!']);
                    return;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Data not filled.']);
                    return;
                }
     }
    
            // paspor form
            if ($letter_type == "PASSPORT") {
                if (!empty($name) && !empty($nik) && !empty($letter_type) && !empty($keterangan) && !empty($no_ktp) && !empty($tempat_lahir) && !empty($tanggal_masuk) && !empty($posisi_jabatan)) {
                    $result = $this->FormsModel->insert_paspor_data($name, $nik, $letter_type, $keterangan, $no_ktp, $tempat_lahir, $tanggal_masuk, $posisi_jabatan);
                    $transaction_data = $this->FormsModel->get_data_by_transaction_id($transaction_id);
                    echo json_encode(['status' => 'success', 'message' => 'Terimakasih Telah Mengirim Form!']);
                    return;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Data not filled.']);
                    return;
                }
            }
    
            // jaminan RS form
            if ($letter_type == "Jaminan RS") {
                if (!empty($name) && !empty($nik) && !empty($letter_type)) {
                    $result = $this->FormsModel->insert_jaminan_rs_data($name, $nik, $letter_type);
                    $transaction_data = $this->FormsModel->get_data_by_transaction_id($transaction_id);
                    echo json_encode(['status' => 'success', 'message' => 'Terimakasih Telah Mengirim Form!']);
                    return;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Data not filled.']);
                    return;
                }
            }
    
        } else {
            show_error('No direct script access allowed');
        }
    }
}