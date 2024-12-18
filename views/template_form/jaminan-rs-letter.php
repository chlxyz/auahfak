    <p>
        <?php 
            echo form_label('Nama Rumah Sakit:', 'Nama_Rumah_Sakit'); 
            echo form_input([
                'value' => '',
                'name' => 'Nama_Rumah_Sakit',
                'id' => 'Nama_Rumah_Sakit',
                'type' => 'text',
            ]); 
        ?>
    </p>
    <p>
        <?php 
            echo form_label('Alamat Rumah Sakit:', 'Alamat_Rumah_Sakit'); 
            echo form_input([
                'value' => '',
                'name' => 'Alamat_Rumah_Sakit',
                'id' => 'Alamat_Rumah_Sakit',
                'type' => 'text',
            ]); 
        ?>
    </p>