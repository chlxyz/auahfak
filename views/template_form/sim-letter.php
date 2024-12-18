<p>
    <?php 
        echo form_label('SIM:', 'sim'); 

        // Dropdown options
        $jenis_sim = [
            '' => 'Select SIM', // Default placeholder
            'A' => 'A',
            'B1' => 'B1',
            'B2' => 'B2',
            'C' => 'C'
        ];

        // Dropdown field
        echo form_dropdown('sim', $jenis_sim, '', [
            'id' => 'Jenis_SIM'
        ]);
    ?>
</p>
