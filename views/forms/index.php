<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Ensure body can scroll */
        html, body {
            height: 100%;
            overflow-y: auto;
        }

        /* Ensure main containers allow content to expand */
        #main, #content, .container, .card {
            height: auto; /* Allow them to grow with content */
            overflow: hidden; /* Ensure they don't clip content */
        }

        /* Card Styling */
        .card {
            padding: 20px;
            background-color: #ffffff;
            /* border: 1px solid #e0e0e0;
            border-radius: 8px; */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            color: #3178c6;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #d3d3d3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Form Styling */
        p {
            display: block;
            justify-content: left;
            align-items: center;
            margin-bottom: 15px;
            line-height: 20px;
        }

        p label {
            font-weight: bold;
            color: #555;
            width: 15%;
        }

        p input, p select {
            width: 40%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
            background-color: #f9f9f9;
        }

        p input:readonly {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .readonly{
            background-color: #e9ecef;
            color: #6c757d;
        }

    </style>
</head>
<body>

<section id="main">
    <section id="content">
        <div class="container">
            <div class="card"> 

    <h2>Permintaan Surat Keterangan Kerja</h2>
    
    <form id="submissionForm">
    <!-- Form Open dengan site_url() -->
    <?php echo form_open('forms/submit', ['method' => 'post']); ?>


        <!-- NIK -->
        <p>
    <?php 
        echo form_label('NIK:', 'number'); 
        echo form_input([
            'placeholder' => 'NIK',
            'value' => $session_data['NIK'],
            'name' => 'nik',
            'id' => 'number',
            'type' => 'number',
            'required' => 'required',
            'readonly' => 'readonly',
            'class' => 'readonly'
        ]); 
    ?>
    </p>

    <!-- Name -->
    <p>
        <?php 
            echo form_label('Nama Lengkap:', 'name'); 
            echo form_input([
                'value' => $session_data['Nama'],
                'placeholder' => 'Name',
                'type' => 'name',
                'name' => 'name',
                'id' => 'name',
                'required' => 'required',
                'readonly' => 'readonly',
                'class' => 'readonly'
            ]); 
        ?>
    </p>


            <!-- Posisi/Jabatan -->
            <p>
        <?php 
            echo form_label('Posisi/Jabatan:', 'posisi_jabatan'); 
            echo form_input([
                'class' => 'readonly',
                'placeholder' => 'Posisi/Jabatan',
                'value' => $session_data['Positions'],
                'name' => 'posisi_jabatan',
                'id' => 'posisi_jabatan',
                'type' => 'text',
                'required' => 'required',
                'readonly' => 'readonly',
                'class' => 'readonly'
            ]); 
        ?>
    </p>

    <!-- Tanggal Masuk -->
    <p>
<?php 
    echo form_label('Tanggal Masuk:', 'tanggal_masuk'); 

    $joining_date = isset($json_decode['joining_date']) ? $json_decode['joining_date'] : 'No Data Found';
    if ($joining_date !== 'No Data Found') {
        $date = DateTime::createFromFormat('Y-m-d', $joining_date);
        // Ubah format menjadi hari, bulan, dan tahun
        $formatted_date = $date ? $date->format('Y-m-d') : 'Invalid Date';
    } else {
        $formatted_date = 'No Data Found';
    }

    echo form_input([
        'placeholder' => 'Tanggal Masuk',
        'name' => 'tanggal_masuk',
        'value' => $formatted_date,
        'id' => 'number',
        'type' => 'text',
        'required' => 'required',
        'readonly' => 'readonly',
        'class' => 'readonly'
    ]); 
?>
</p>


                <!-- NO KTP -->

                
    <p>
        <?php 
            echo form_label('No. KTP:', 'no_ktp'); 
            echo form_input([
                'placeholder' => 'No.KTP',
                'value' => isset($json_decode ['identification_id']) ? $json_decode ['identification_id'] : 'No Data Found', // Safeguard if data is missing
                'name' => 'no_ktp',
                'id' => 'no_ktp',
                'type' => 'text',
                'required' => 'required',
                'readonly' => 'readonly',
                'class' => 'readonly'
            ]); 
        ?>
    </p>


        <!-- Tempat Lahir -->
    <p>
    <?php 
        echo form_label('Tempat lahir:', 'tempat_lahir'); 
        echo form_input([
            'placeholder' => 'Tempat Lahir',
            // 'value' => $session_data['TempatLahir'],
            'value' => isset($json_decode ['place_of_birth']) ? $json_decode ['place_of_birth'] : 'No Data Found',
            'name' => 'tempat_lahir',
            'id' => 'tempat_lahir',
            'type' => 'text',
            'required' => 'required',
            'readonly' => 'readonly',
            'class' => 'readonly'
        ]); 
    ?>
    </p>

        <!-- Tanggal Lahir -->
    <p>
        <?php 
            echo form_label('Tanggal lahir:', 'tanggal_lahir'); 
            echo form_input([
                'name' => 'tanggal_lahir',
                'value' => date('Y-m-d', strtotime($session_data['TGLLAHIR'])),
                'id' => 'tanggal_lahir',
                'type' => 'text',
                'required' => 'required',
                'readonly' => 'readonly',
                'class' => 'readonly'
            ]); 
        ?>
    </p>

        <!-- No HP -->
    <p>
    <?php 
        echo form_label('Nomor Telepon:', 'no_hp'); 
        echo form_input([
            'placeholder' => 'Nomor Telepon',
            'value' => $session_data['Telp'],
            'name' => 'no_hp',
            'id' => 'no_hp',
            'type' => 'text',
            'required' => 'required',
            'readonly' => 'readonly',
            'class' => 'readonly'
        ]); 
    ?>
    </p>

    <!-- Tanggal Pengajuan -->

    <p>
    <?php 
        echo form_label('Tanggal Pengajuan:', 'tanggal_pengajuan'); 
        
        // Mengakses $tanggal_pengajuan yang dikirimkan oleh controller
        echo form_input([
            'name' => 'TanggalPengajuan',
            'id' => 'tanggal_pengajuan',
            'type' => 'text', // Changed to 'text' because 'date' input type is not compatible with PHP date formatting
            'readonly' => 'readonly',
            'class' => 'readonly',  // Membuat input tidak dapat diubah
            'value' => date('Y-m-d'),  // Generate current date dynamically in 'd-m-Y' format
            'placeholder' => 'Tanggal Pengajuan',
        ]); 
    ?>
</p>





    <!-- Letter Type Dropdown -->
    <p>
        <?php 
            // Menampilkan label dan dropdown
            echo form_label('Letter Type:', 'letter_type'); 
            echo form_dropdown('letter_type', ['' => 'Select Letter Type'] + $letter_types, '', [
                'id' => 'letter_type',
                'name' => 'letter_type',
                'placeholder' => 'Select Letter Type'
            ]);
        ?>
    </p>
    <div id="additional-form">
        <!-- Form tambahan akan dimuat di sini berdasarkan pilihan dropdown -->
    </div>

    <p>
        <?php 
            echo form_label('Keterangan:', 'keterangan'); 
            echo form_input([
                'value' => '',
                'name' => 'keterangan',
                'id' => 'keterangan',
                'type' => 'text',
            ]); 
        ?>
    </p>

    <p>
    <?php echo form_submit('submit', 'Submit', 'class="btn btn-primary" style="width: 44%; margin: 10px 1%;"'); ?>
</p>

</form>

    <?php echo form_close(); ?>
    </div>
</div>
</section>
</section>
<script>
    $(document).ready(function() {
        // Form submission via AJAX
        $('#submissionForm').on('submit', function(event) {
            event.preventDefault();
            var url = '<?php echo site_url("forms/ajax_submit"); ?>';
 
            console.log('Submitting to URL:', url); // Debug URL
            console.log('Serialized Data:', $(this).serialize()); // Debug data
 
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug response
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect ke halaman formstatus
                                window.location.href = '<?php echo site_url("formstatus"); ?>';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText); // Debug server error
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
 
 
 
        // Load additional form based on letter type selection
        $(document).on('change', '#letter_type', function () {
            var letter_type = $(this).val();
            console.log('Dropdown changed to:', letter_type);
 
            if (!letter_type || letter_type === '') {
                console.warn('No letter type selected.');
                return;
            }
 
            $.ajax({
                url: '<?php echo site_url("Forms/load_letter_form"); ?>?t=' + new Date().getTime(),
                type: 'POST',
                data: { letter_type: letter_type },
                success: function (response) {
                    console.log('Response received:', response);
                    if (response.trim() === '') {
                        console.warn('Empty response received.');
                    }
                    if ($('#additional-form').length === 0) {
                        console.error('#additional-form element is missing in the DOM.');
                        return;
                    }
                    $('#additional-form').html(response);
 
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load additional form.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
 
    });
</script>
</body>
</html>
