<p>
    <?php 
        echo form_label('Nomor Pasport:', 'nomor_pasport'); 
        echo form_input([
            'placeholder' => 'Nomor Pasport',
            'name' => 'nomor_pasport',
            'id' => 'nomor_pasport',
            'type' => 'text',
            'required' => 'required'
        ]); 
    ?>
</p>

<p>
    <?php 
        $country_options = ['' => 'Pilih Negara'];
        if (!empty($countrys)) {
            foreach ($countrys as $country) {
                $country_options[$country->Nama_Negara] = $country->Nama_Negara;
            } 
        }

        echo form_label('Nama Negara:', 'Nama_Negara'); 
        echo form_dropdown('Nama_Negara', $country_options, '', [
            'id' => 'Nama_Negara',
            'required' => 'required'
        ]);
    ?>
</p>

<p>
    <?php 
        echo form_label('Rencana Awal Kunjungan:', 'rencana_awal'); 
        echo form_input([
            'style' => 'line-height: 20px;',
            'placeholder' => 'Rencana Awal Kunjungan',
            'name' => 'rencana_awal',
            'id' => 'rencana_awal',
            'type' => 'date',
            'value' => '',
            'required' => 'required',
        ]); 
    ?>
</p>

<p>
    <?php 
        echo form_label('Rencana Akhir Kunjungan:', 'rencana_akhir'); 
        echo form_input([
            'style' => 'line-height: 20px;',
            'placeholder' => 'Rencana Akhir Kunjungan',
            'name' => 'rencana_akhir',
            'id' => 'rencana_akhir',
            'type' => 'date',
            'value' => '',
            'required' => 'required',
        ]); 
    ?>
</p>

<p>
    <?php 
        echo form_label('Tujuan Kedutaan:', 'Tujuan_Kedutaan'); 
        echo form_dropdown('Tujuan_Kedutaan', [], '', [
            'id' => 'Tujuan_Kedutaan',
            'required' => 'required'
        ]);
    ?>
</p>

<p>
    <?php 
        echo form_label('Alamat Kedutaan:', 'Alamat_Kedutaan'); 
        echo form_input([
            'placeholder' => 'Alamat Kedutaan',
            'name' => 'Alamat_Kedutaan',
            'id' => 'Alamat_Kedutaan',
            'type' => 'text',
            'value' => '',
            'required' => 'required',
            'disabled' => 'disabled'
        ]); 
    ?>
</p>

<script>
$(document).ready(function() {
    fetchEmbassyNames();

    $('#Tujuan_Kedutaan').on('change', function() {
        fetchEmbassyAddress();
    });
});

function fetchEmbassyNames() {
    $.ajax({
        url: '<?php echo site_url("Forms/fetch_embassy_names"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            var options = '<option value="">Pilih Tujuan Kedutaan</option>';
            $.each(response, function(index, embassy) {
                options += '<option value="' + embassy.Nama_Kedutaan + '">' + embassy.Nama_Kedutaan + '</option>';
            });
            $('#Tujuan_Kedutaan').html(options);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function fetchEmbassyAddress() {
    var embassyName = $('#Tujuan_Kedutaan').val();
    if (embassyName) {
        $.ajax({
            url: '<?php echo site_url("Forms/fetch_embassy_address"); ?>',
            type: 'POST',
            data: { embassy_name: embassyName },
            success: function(response) {
                $('#Alamat_Kedutaan').val(response);
                $('#Alamat_Kedutaan').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    } else {
        $('#Alamat_Kedutaan').val('');
        $('#Alamat_Kedutaan').prop('disabled', true);
    }
}
</script>