
    <p>
        <?php 
            // Prepare the dropdown options array with a default option
            $bank_options = ['' => 'Pilih Bank'];  // Default option
            
            // Check if bank data exists and loop through it to create options
            if (!empty($banks)) {
                foreach ($banks as $bank) {
                    // Ensure the 'bankname' is set correctly
                    $bank_options[$bank->bankname] = $bank->bankname;
                }
            }

            // Create the form dropdown
            echo form_label('Nama Bank:', 'nama_bank'); 
            echo form_dropdown('nama_bank', $bank_options, '', [
                'id' => 'nama_bank',
                'required' => 'required'
            ]);
        ?>
    </p>


    <p>
        <?php 
            echo form_label('Harga KPR:', 'harga_kpr'); 
            echo form_input([
                'placeholder' => 'Harga KPR',
                'name' => 'harga_kpr',
                'id' => 'harga_kpr',
                'type' => 'text',
                'required' => 'required'
            ]); 
        ?>
    </p>

    <p>
        <?php 
            echo form_label('Nominal Cicilan:', 'nominal_cicilan'); 
            echo form_input([
                'placeholder' => 'Nominal Cicilan',
                'name' => 'nominal_cicilan',
                'id' => 'nominal_cicilan',
                'type' => 'text',
                'required' => 'required'
            ]); 
            echo '/ Bulan';
        ?>
    </p>
    <span style="color: red; margin-left: 165px;">* Cicilan Maksimal 25% dari Gaji Pokok</span>

    <p>
        <?php 
            echo form_label('Durasi Cicilan:', 'durasi_cicilan'); 
            echo form_input([
                'placeholder' => 'Durasi Cicilan',
                'name' => 'durasi_cicilan',
                'id' => 'durasi_cicilan',
                'type' => 'text',
                'required' => 'required'
            ]); 
            echo 'Bulan';
        ?>
    </p>

    <span style="color: red; margin-left: 165px;">* Diisi jika Join Income</span>
    <p>
        <?php 
            echo form_label('Penghasilan Pasangan:', 'penghasilan_pasangan'); 
            echo form_input([
                'placeholder' => 'Penghasilan Pasangan (Jika Ada)',
                'name' => 'penghasilan_pasangan',
                'id' => 'penghasilan_pasangan',
                'type' => 'text',
            ]); 
        ?>
    </p>

