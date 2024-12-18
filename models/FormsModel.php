<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FormsModel extends CI_Model {

    public function getOdooToken($user = NULL, $password = NULL)
    {
        // get TOKEN ODOOO
 
        $ch = curl_init();
 
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-type: application/json'
            )
        );
 
        $postdata = array(
            'login' => $user,
            "password" => $password,
        );
 
        $postdata = json_encode($postdata);
 
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, "https://kg-pms-odoo-dev1.mykg.id/kg/api/auth/user/token");
        curl_setopt($ch, CURLOPT_POST, count($postdata));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
 
        //execute post
        ob_start();
        curl_exec($ch);
 
        $result = ob_get_clean();
 
        //close connection
        curl_close($ch);
 
        $json_decode = json_decode($result);
        $token = $json_decode->access_token;
        $_SESSION['token_odoo'] = $token;
 
        return $token;
    }



    public function getEmployeeProfileOdoo($nik)
{
    // Retrieve the token from session or generate a new one if not set
    if (isset($_SESSION['token_odoo'])) {
        $token = $_SESSION['token_odoo'];
    } else {
        $token = $this->getOdooToken('admin', 'admin');
    }

    // Initialize cURL
    $ch = curl_init();
    $authorization = "Authorization: Bearer $token";

    // Set the cURL options for API request
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        $authorization
    )
    );

    // Modify domain to use NIK for the API query
    $param = array(
        "domain" => "[('employee_id', '=', '$nik')]",  // Using NIK in the query
        'fields' => "['place_of_birth', 'identification_id', 'joining_date']"
    );

    $url = "https://kg-pms-odoo-dev1.mykg.id/kg/api/res/hr.employee?" . http_build_query($param);

    // Set the cURL options to send the request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    
    ob_start();
    curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result = ob_get_clean();

    // Check if the response is successful (HTTP Code 200)
    if ($httpcode == 200) {
        $json_decode = json_decode($result, true);
        if (isset($json_decode)) {
            return $json_decode['data'][0];
        }
    } else {
        $json_decode = json_decode($result, true);
// var_dump('Decoded Response:', $json_decode);
        return false; // Handle other HTTP errors
    }

}

    public function get_data_by_session($session_id) {
        // Query database berdasarkan session_id
        $db = $this->load->database('default', TRUE);
        $sql="select * from ms_niktelp where NIK=?";
        $query = $db->query($sql,array($session_id)); // Ganti 'users' dengan nama tabel yang sesuai

        // Mengembalikan hasil query sebagai array atau null jika tidak ditemukan
        return $query->row_array();
    }

    public function get_data() {
        $db = $this->load->database('default', TRUE);
    
        // Perform the JOIN query
        $db->select('ms_niktelp.*, ms_PersArea.NAME1'); // Select all columns from ms_niktelp and NAME1 from ms_PersArea
        $db->from('ms_niktelp');
        $db->join('ms_PersArea', 'ms_niktelp.some_column = ms_PersArea.some_column', 'left'); // Replace 'some_column' with the actual join condition
    
        $query = $db->get();
        return $query->result(); // Return the result as an array of objects
    }

    

    public function get_bank_name() {
        $db = $this->load->database('default', TRUE);
        
        $query = $db->select('bankname')
                    ->get('pd_bankkey');
        
        return $query->result();
    }

    public function get_country_name() {
        $db = $this->load->database('default', TRUE);

        $query = $db->select('Nama_Negara')
                    ->get('Horizon_Country');

        return $query->result();
    }

    public function get_embassy_name() {
        $db = $this->load->database('default', TRUE);
    
        $query = $db->select('Nama_Kedutaan')
                    ->get('Horizon_Embassy');
    
        return $query->result();
    }
    
    public function get_embassy_address($embassy_name) {
        $db = $this->load->database('default', TRUE);
    
        $query = $db->select('Alamat_Kedutaan')
                    ->where('Nama_Kedutaan', $embassy_name)
                    ->get('Horizon_Embassy');
    
        if ($query->num_rows() > 0) {
            return $query->row()->Alamat_Kedutaan;
        } else {
            return null;
        }
    }

    public function get_data_by_transaction_id($transaction_id) {
        $this->db->select('ht.NIK, ht.*, htd.*');
        $this->db->from('Horizon_Transaction ht');
        $this->db->join('Horizon_Transaction_Detail htd', 'ht.transaction_id = htd.Transaction_ID', 'left'); // Join dengan transaction_id
        $this->db->where('ht.transaction_id', $transaction_id);
        
        $query = $this->db->get();
        
        return $query->result(); // Mengembalikan hasil sebagai array objek
    }

    public function check_existing_submission($nik, $letter_type) {
        // Query to check if a submission exists for the given NIK and letter type
        $this->db->select('status_approvalTotal'); // Assuming 'status_approvalTotal' is the column that holds the submission status
        $this->db->from('Horizon_Transaction'); // Replace with your actual table name
        $this->db->where('NIK', $nik);
        $this->db->where('jenis_surat', $letter_type); // Compare with the correct column for letter type
        $this->db->where('status_approvalTotal', 0); // Check for 'Pending' status
        $query = $this->db->get();
    
        // Check if a record exists
        if ($query->num_rows() > 0) {
            // If a pending submission exists for the same letter type, return false
            return false; // User cannot submit a new request
        }
    
        // If no record exists or the status is not 'Pending', allow submission
        return true;
    }

    public function check_existing_sim_submission($nik, $sim) {
        $this->db->select('*');
        $this->db->from('Horizon_Transaction_Detail');
        $this->db->where('NIK', $nik);
        $this->db->where('Jenis_SIM', $sim);
        $query = $this->db->get();
    
        // Return true if a record exists, false otherwise
        return $query->num_rows() > 0;
    }
    

    // method insert ke database sim
    public function insert_sim_data($name, $nik, $sim, $keterangan, $letter_type, $no_ktp, $tempat_lahir, $tanggal_masuk, $posisi_jabatan) {
        // Load database
        $db = $this->load->database('default', TRUE);
    
        // Ambil PersArea berdasarkan NIK
        $db->select('PersArea');
        $db->from('ms_niktelp');
        $db->where('NIK', $nik);
        $query = $db->get();
    
        if ($query->num_rows() > 0) {
            $pers_area = $query->row()->PersArea;
    
            // Siapkan data untuk insert ke Horizon_Transaction
            $data = [
                'NIK' => $nik,
                'nama' => $name,
                'jenis_surat' => $letter_type,
                'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
                'PersArea' => $pers_area,
                'keterangan' => $keterangan,
                'status_approvalTotal' => 0 // Use 0 for 'Pending'
            ];
    
            // Lakukan insert ke tabel Horizon_Transaction
            $insert = $db->insert('Horizon_Transaction', $data);
    
            if ($insert) {
                // Dapatkan transaction_id yang baru saja diinsert
                $query = $db->query("SELECT @@IDENTITY AS id");
                $row = $query->row();
                $transaction_id = $row->id;
    
                // Siapkan data untuk insert ke Horizon_Transaction_Detail
                $data_detail = [
                    'Transaction_ID' => $transaction_id,
                    'NIK' => $nik,
                    'No_KTP' => $no_ktp,
                    'Tempat_Lahir' => $tempat_lahir,
                    'Tanggal_Masuk' => $tanggal_masuk,
                    'Positions' => $posisi_jabatan,
                    'Nama' => $name,
                    'Jenis_Surat' => $letter_type,
                    'PersArea' => $pers_area,
                    'Jenis_SIM' => $sim,
                    'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
                    'Keterangan' => $keterangan
                ];
    
                // Lakukan insert ke tabel Horizon_Transaction_Detail
                $insert_detail = $db->insert('Horizon_Transaction_Detail', $data_detail);
    
                if ($insert_detail) {
                    return $db->affected_rows(); // Kembalikan jumlah baris yang terpengaruh
                } else {
                    return false; // Insert ke Horizon_Transaction_Detail gagal
                }
            } else {
                return false; // Insert ke Horizon_Transaction gagal
            }
        } else {
            return "NIK tidak ditemukan di tabel ms_niktelp."; // NIK tidak ada di ms_niktelp
        }
    }
    

    // method insert ke database kpr
    public function insert_kpr_data($name, $nik, $letter_type, $nama_bank, $nominal_kpr, $bulan_kpr, $penghasilan_pasangan, $keterangan) {
        // Load database
        $db = $this->load->database('default', TRUE);
    
        // Ambil PersArea berdasarkan NIK
        $db->select('PersArea');
        $db->from('ms_niktelp');
        $db->where('NIK', $nik);
        $query = $db->get();
    
        if ($query->num_rows() > 0) {
            $pers_area = $query->row()->PersArea;
    
            // Siapkan data untuk insert ke Horizon_Transaction
            $data = [
                'NIK' => $nik,
                'nama' => $name,
                'jenis_surat' => $letter_type,
                'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
                'PersArea' => $pers_area,
                'keterangan' => $keterangan,
                'status_approvalTotal' => 0 // Use 0 for 'Pending'
            ];
    
            // Lakukan insert
            $insert = $db->insert('Horizon_Transaction', $data);
    
            if ($insert) {
                return $db->affected_rows(); // Kembalikan jumlah baris yang terpengaruh
            } else {
                return false; // Insert gagal
            }
        } else {
            return "NIK tidak ditemukan di tabel ms_niktelp."; // NIK tidak ada di ms_niktelp
        }
    }

    // method insert ke database paspor
    // public function insert_paspor_data($name, $nik, $letter_type, $keterangan, $no_ktp, $tempat_lahir, $tanggal_masuk, $posisi_jabatan) {
    //     // Load database
    //     $db = $this->load->database('default', TRUE);
    
    //     // Ambil PersArea berdasarkan NIK
    //     $db->select('PersArea');
    //     $db->from('ms_niktelp');
    //     $db->where('NIK', $nik);
    //     $query = $db->get();
    
    //     if ($query->num_rows() > 0) {
    //         $pers_area = $query->row()->PersArea;
    
    //         // Siapkan data untuk insert ke Horizon_Transaction
    //         $data = [
    //             'NIK' => $nik,
    //             'nama' => $name,
    //             'jenis_surat' => $letter_type,
    //             'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
    //             'PersArea' => $pers_area,
    //             'keterangan' => $keterangan,
    //             'status_approvalTotal' => 0 // Use 0 for 'Pending'
    //         ];

    //         $insert = $db->insert('Horizon_Transaction', $data);
    
    //         $data_detail = [
    //             'NIK' => $nik,
    //             'No_KTP' => $no_ktp,
    //             'Tempat_Lahir' => $tempat_lahir,
    //             'Tanggal_Masuk' => $tanggal_masuk,
    //             'Positions' => $posisi_jabatan,
    //             'Nama' => $name,
    //             'Jenis_Surat' => $letter_type,
    //             'PersArea' => $pers_area,
    //             'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
    //             'Keterangan' => $keterangan
    //         ];
    
    //         // Lakukan insert
    //         $insert = $db->insert('Horizon_Transaction', $data);
    //         $insert_detail = $db->insert('Horizon_Transaction_Detail', $data_detail);
    
    //         if ($insert && $insert_detail) {
    //             return $db->affected_rows(); // Kembalikan jumlah baris yang terpengaruh
    //         } else {
    //             return false; // Insert gagal
    //         }
    //     } else {
    //         return "NIK tidak ditemukan di tabel ms_niktelp."; // NIK tidak ada di ms_niktelp
    //     }
    // }

    public function insert_paspor_data($name, $nik, $letter_type, $keterangan, $no_ktp, $tempat_lahir, $tanggal_masuk, $posisi_jabatan) {
        // Load database
        $db = $this->load->database('default', TRUE);
    
        // Ambil PersArea berdasarkan NIK
        $db->select('PersArea');
        $db->from('ms_niktelp');
        $db->where('NIK', $nik);
        $query = $db->get();
    
        if ($query->num_rows() > 0) {
            $pers_area = $query->row()->PersArea;
    
            // Siapkan data untuk insert ke Horizon_Transaction
            $data = [
                'NIK' => $nik,
                'nama' => $name,
                'jenis_surat' => $letter_type,
                'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
                'PersArea' => $pers_area,
                'keterangan' => $keterangan,
                'status_approvalTotal' => 0 // Use 0 for 'Pending'
            ];

            $insert = $db->insert('Horizon_Transaction', $data);

            if ($insert) {
                $query = $db->query("SELECT @@IDENTITY AS id");
                $row = $query->row();
                $transaction_id = $row->id;

                $data_detail = [
                    'Transaction_ID' => $transaction_id,
                    'NIK' => $nik,
                    'No_KTP' => $no_ktp,
                    'Tempat_Lahir' => $tempat_lahir,
                    'Tanggal_Masuk' => $tanggal_masuk,
                    'Positions' => $posisi_jabatan,
                    'Nama' => $name,
                    'Jenis_Surat' => $letter_type,
                    'PersArea' => $pers_area,
                    'tanggal_request' => date('Y-m-d H:i:s'), // Tanggal saat ini
                    'Keterangan' => $keterangan
                ];
                
                $insert_detail = $db->insert('Horizon_Transaction_Detail', $data_detail);
    
                if ($insert_detail) {
                    return $db->affected_rows(); // Kembalikan jumlah baris yang terpengaruh
                } else {
                    return false; // Insert gagal
                }
            } else {
                return false; // Insert ke Horizon_Transaction gagal
            }
        } else {
            return "NIK tidak ditemukan di tabel ms_niktelp."; // NIK tidak ada di ms_niktelp
        }
    }
    

    // Method untuk mendapatkan daftar tipe surat
    public function get_letter_types() {
        return [
            'KPR' => 'KPR',
            'VISA' => 'VISA',
            'PASSPORT' => 'PASSPORT',
            'SIM' => 'SIM',
            'Jaminan RS' => 'Jaminan RS'
        ];
    }


}
