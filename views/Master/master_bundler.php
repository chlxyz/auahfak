<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master Bundler</h1>
                </div>

                <style>
                .form-container {
                    display: grid;
                    grid-template-columns: 1fr 2fr;
                    gap: 15px 10px;
                    align-items: center;
                    margin: 20px;
                }

                .form-container label {
                    font-weight: bold;
                    text-align: left;
                    margin: 0;
                }

                .form-container input,
                .form-container select {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }

                .form-container img {
                    width: 100px;
                    height: 100px;
                    display: none;
                    margin-top: 10px;
                }

                .button-container {
                    grid-column: span 2;
                    display: flex;
                    justify-content: space-between;
                    margin: 20px;
                }

                .button-container button {
                    width: 48%;
                    padding: 10px;
                }

            </style>
                <form id="MasterBundler">
                    <div class="form-container">
                        <label for="select_persarea">PersArea:</label>
                        <select name="select_persarea" id="select_persarea" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="PersArea_text">Area name:</label>
                        <input type="text" name="PersArea_text" id="PersArea_text" required>

                        <label for="PersArea_inisial">Area inisial:</label>
                        <input type="text" name="PersArea_inisial" id="PersArea_inisial" required>

                        <label for="PersArea_alamat">Area alamat:</label>
                        <input type="text" name="PersArea_alamat" id="PersArea_alamat" required>

                        <label for="select_template">Select template:</label>
                        <select name="select_template" id="select_template" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="select_template_header">Header:</label>
                        <div>
                        <select name="select_template_header" id="select_template_header" required></select>
                        <img id="headerImagePreview" src="" alt="Selected Header Preview" style="width: 100%; height: 100%;">
                        </div>

                        <label for="select_template_footer">Footer:</label>
                        <div>
                        <select name="select_template_footer" id="select_template_footer" required></select>
                        <img id="footerImagePreview" src="" alt="Selected Footer Preview" style="width: 100%; height: 100%;">
                        </div>

                        <label for="select_surat">Select surat:</label>
                        <select name="select_surat" id="select_surat" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="select_isi">Select isi:</label>
                        <select name="select_isi" id="select_isi" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="viewer">Viewer:</label>
                        <input type="text" name="viewer" id="viewer" required>

                        <label for="viewer_name">Viewer name:</label>
                        <input type="text" name="viewer_name" id="viewer_name" required>

                        <label for="select_approver1">Select approver 1:</label>
                        <select name="select_approver1" id="select_approver1" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="select_approver1_ttd">Approver 1 ttd:</label>
                        <div>
                        <select name="select_approver1_ttd" id="select_approver1_ttd" required></select>
                        <img id="ttd1ImagePreview" src="" alt="Selected ttd Preview">
                        </div>

                        <label for="select_approver1_paraf">Approver 1 paraf:</label>
                        <div>
                        <select name="select_approver1_paraf" id="select_approver1_paraf" required></select>
                        <img id="paraf1ImagePreview" src="" alt="Selected paraf Preview">
                        </div>

                        <label for="select_approver2">Select approver 2:</label>
                        <select name="select_approver2" id="select_approver2" required>
                        <option value="" selected>-SELECT-</option>
                        </select>

                        <label for="select_approver2_ttd">Approver 2 ttd:</label>
                        <div>
                        <select name="select_approver2_ttd" id="select_approver2_ttd" required></select>
                        <img id="ttd2ImagePreview" src="" alt="Selected ttd Preview">
                        </div>

                        <label for="select_approver2_paraf">Approver 2 paraf:</label>
                        <div>
                        <select name="select_approver2_paraf" id="select_approver2_paraf" required></select>
                        <img id="paraf2ImagePreview" src="" alt="Selected paraf Preview">
                        </div>

                        <label for="select_cap">Select stamp:</label>
                        <div>
                        <select name="select_cap" id="select_cap" onchange="updateCapPreview(this)" required>
                        <option value="" selected>-SELECT-</option>
                        </select>
                        <img id="stampImagePreview" src="" alt="Selected Stamp Preview">
                        </div>
                    </div>

                    <div class="button-container">
                        <button type="button" id="previewButton" class="btn btn-secondary">Preview</button>
                        <button type="button" id="submitButton" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </section>
</section>

<div id="pdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center;">
    <div style="position: relative; width: 80%; height: 80%; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); overflow: hidden;">
        <button id="closeModal" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px; font-weight: bold;">&times;</button>
        <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
</div>

<script>
    const base_url = "<?php echo base_url(); ?>";
    document.addEventListener("DOMContentLoaded",  function() {

    const url = base_url + "index.php/MasterBundler/get_data";
    const urlParams = new URLSearchParams(window.location.search);
    const persArea = urlParams.get('persArea');
    const mode = urlParams.get('mode');
    const idSurat = urlParams.get('id_surat');

    fetch(url)
    .then(response => response.json())
        .then(data => {
            // ambil data dari backend untuk auto populate setiap field
            if (data.status === "success") {
                const persAreaSelect = document.getElementById("select_persarea");
                const persAreaName = document.getElementById("PersArea_text");
                const persAreaInisial = document.getElementById("PersArea_inisial");
                const persAreaAddress = document.getElementById("PersArea_alamat");
                const templateSelect = document.getElementById("select_template");
                const templateSelectHeader = document.getElementById("select_template_header");
                const templateSelectFooter = document.getElementById("select_template_footer");
                const approver1Select = document.getElementById("select_approver1");
                const approver1ttd = document.getElementById("select_approver1_ttd");
                const approver1paraf = document.getElementById("select_approver1_paraf");
                const approver2Select = document.getElementById("select_approver2");
                const approver2ttd = document.getElementById("select_approver2_ttd");
                const approver2paraf = document.getElementById("select_approver2_paraf");
                const cap = document.getElementById("select_cap");
                const suratSelect = document.getElementById("select_surat");
                const isiSelect = document.getElementById("select_isi");

                // data ketika user memilih persarea, maka field nama, inisial, dan alamat akan terisi otomatis
                data.data.persArea.forEach(persArea => {
                    const option = document.createElement("option");
                    option.value = persArea.id;
                    option.textContent = persArea.text;
                    option.dataset.area_name = persArea.area_name;
                    option.dataset.area_initial = persArea.area_initial;
                    option.dataset.area_address = persArea.area_address;
                    persAreaSelect.appendChild(option);
                });

                // data ketika user memilih template, maka field header dan footer akan terisi otomatis
                data.data.template.forEach(template => {
                    const option = document.createElement("option");
                    option.value = template.id;
                    option.textContent = template.text;
                    option.dataset.header = template.header;  
                    option.dataset.footer = template.footer;  
                    templateSelect.appendChild(option);
                });

                // data ketika user memilih jenis surat
                data.data.surat.forEach(surat => {
                    const option = document.createElement("option");
                    option.value = surat.id;
                    option.textContent = surat.text;
                    suratSelect.appendChild(option);
                });

                // data ketika user memilih isi surat
                data.data.isi.forEach(isi => {
                    const option = document.createElement("option");
                    option.value = isi.id;
                    option.textContent = isi.text;
                    isiSelect.appendChild(option);
                });

                // data untuk user memilih cap surat
                data.data.cap.forEach(capOption => {
                    const option = document.createElement("option");
                    option.value = capOption.id;
                    option.textContent = capOption.text;
                    option.dataset.gambar_cap = capOption.gambar_cap;
                    cap.appendChild(option);
                });

                // data ketika user ingin memiilih approver 1, maka field ttd dan paraf akan terisi
                data.data.approver1.forEach(approver => {
                    const option = document.createElement("option");
                    option.value = approver.id;
                    option.textContent = approver.text;
                    option.dataset.gambarTtd = approver.gambar_ttd;
                    option.dataset.gambarParaf = approver.gambar_paraf;
                    approver1Select.appendChild(option);
                });

                // data ketika user ingin memilih approver 2, maka field ttd dan paraf akan terisi
                data.data.approver2.forEach(approver => {
                    const option = document.createElement("option");
                    option.value = approver.id;
                    option.textContent = approver.text;
                    option.dataset.gambarTtd = approver.gambar_ttd;
                    option.dataset.gambarParaf = approver.gambar_paraf;
                    approver2Select.appendChild(option);
                });

                // Event untuk update field detail persArea pas user pilih persArea tertentu
                persAreaSelect.addEventListener("change", function() {
                    const selectedOption = persAreaSelect.options[persAreaSelect.selectedIndex];
                    const area_name = selectedOption.dataset.area_name || '';
                    const area_initial = selectedOption.dataset.area_initial || '';
                    const area_address = selectedOption.dataset.area_address || '';

                    persAreaName.value = area_name || '';
                    persAreaInisial.value = area_initial || '';
                    persAreaAddress.value = area_address || '';

                    persAreaName.disabled = !area_name;
                    persAreaInisial.disabled = !area_initial;
                    persAreaAddress.disabled = !area_address;
                });

                // Event untuk update ttd dan paraf approver pertama saat dipilih
                approver1Select.addEventListener("change", function() {
                    const selectedOption = approver1Select.options[approver1Select.selectedIndex];
                    const ttd = selectedOption.dataset.gambarTtd || ''; 
                    const paraf = selectedOption.dataset.gambarParaf || '';

                    console.log('Approver 1 selected: ', selectedOption.textContent);
                    console.log('TTD for Approver 1: ', ttd);
                    console.log('Paraf for Approver 1: ', paraf);

                    approver1ttd.innerHTML = "";
                    approver1paraf.innerHTML = "";

                    if (ttd) {
                        const ttdOption = document.createElement("option");
                        ttdOption.value = ttd;
                        ttdOption.textContent = ttd;
                        approver1ttd.appendChild(ttdOption);
                    }

                    if (paraf) {
                        const parafOption = document.createElement("option");
                        parafOption.value = paraf;
                        parafOption.textContent = paraf;
                        approver1paraf.appendChild(parafOption);
                    }

                    approver1ttd.disabled = !ttd;
                    approver1paraf.disabled = !paraf;
                });

                // Event untuk update ttd dan paraf approver kedua saat dipilih
                approver2Select.addEventListener("change", function() {
                    const selectedOption = approver2Select.options[approver2Select.selectedIndex];
                    const ttd = selectedOption.dataset.gambarTtd || '';
                    const paraf = selectedOption.dataset.gambarParaf || '';

                    console.log('Approver 2 selected: ', selectedOption.textContent);
                    console.log('TTD for Approver 2: ', ttd);
                    console.log('Paraf for Approver 2: ', paraf);

                    approver2ttd.innerHTML = "";
                    approver2paraf.innerHTML = "";

                    if (ttd) {
                        const ttdOption = document.createElement("option");
                        ttdOption.value = ttd;
                        ttdOption.textContent = ttd;
                        approver2ttd.appendChild(ttdOption);
                    }

                    if (paraf) {
                        const parafOption = document.createElement("option");
                        parafOption.value = paraf;
                        parafOption.textContent = paraf;
                        approver2paraf.appendChild(parafOption);
                    }

                    approver2ttd.disabled = !ttd;
                    approver2paraf.disabled = !paraf;
                });

                // Event untuk update header dan footer otomatis saat template dipilih
                templateSelect.addEventListener("change", function() {
                    const selectedOption = templateSelect.options[templateSelect.selectedIndex];
                    const header = selectedOption.dataset.header || '';
                    const footer = selectedOption.dataset.footer || '';

                    templateSelectHeader.innerHTML = "";
                    templateSelectFooter.innerHTML = "";

                    if (header) {
                        const headerOption = document.createElement("option");
                        headerOption.value = header;
                        headerOption.textContent = header;
                        templateSelectHeader.appendChild(headerOption);
                    }

                    if (footer) {
                        const footerOption = document.createElement("option");
                        footerOption.value = footer;
                        footerOption.textContent = footer;
                        templateSelectFooter.appendChild(footerOption);
                    }

                    templateSelectHeader.disabled = !header;
                    templateSelectFooter.disabled = !footer;
                });
            } else {
                alert("Failed to load data");
            }
        })
        .catch(() => {
            alert("Failed to load data");
        });

        if (persArea && idSurat && mode === 'view') {
            const detailsUrl = `${base_url}index.php/MasterBundler/detailsData?persArea=${persArea}&id_surat=${idSurat}`;

            async function fetchAndVerifyData() {
                try {
                    const response = await fetch(detailsUrl);
                    const data = await response.json();

                    if (data.status === "success" && data.data) {
                        const requiredFields = [
                            'select_persarea', 'PersArea_text', 'PersArea_inisial', 'PersArea_alamat',
                            'viewer', 'select_surat', 'select_isi', 'select_cap', 'viewer_name',
                            'select_template', 'select_template_header', 'select_template_footer',
                            'approver1_nik', 'approver1_name', 'approver1_ttd', 'approver1_paraf',
                            'approver2_nik', 'approver2_name', 'approver2_ttd', 'approver2_paraf'
                        ];

                        // Check for missing fields
                        const missingFields = requiredFields.filter(field => !(field in data.data) || !data.data[field]);
                        if (missingFields.length > 0) {
                            console.error("Missing fields in fetched data:", missingFields);
                            alert(`Error: Missing fields: ${missingFields.join(", ")}`);
                            return; // Stop further execution if data is incomplete
                        }

                        // If all fields are present, proceed with populating
                        populateFields(data.data);
                    } else {
                        alert("Failed to load data or data is invalid.");
                    }
                } catch (error) {
                    console.error("Error fetching data:", error);
                }
            }

            function populateFields(data) {
                // Populate basic input fields
                const fields = [
                    'select_persarea', 'PersArea_text', 'PersArea_inisial', 'PersArea_alamat',
                    'viewer', 'select_surat', 'select_isi', 'select_cap', 'viewer_name'
                ];

                fields.forEach(fieldId => {
                    const element = document.getElementById(fieldId);
                    if (element) {
                        element.value = data[fieldId];
                        element.readOnly = true;
                        element.disabled = true;
                        console.log(`Populated ${fieldId} with ${data[fieldId]}`);
                    }
                });

                // Populate select_template
                const templateSelect = document.getElementById('select_template');
                if (templateSelect) {
                    populateSelect(
                        templateSelect,
                        [{ value: data.select_template, text: data.template_name }],
                        data.select_template,
                        { header: data.select_template_header, footer: data.select_template_footer }
                    );
                }

                // Populate approvers
                populateApprover('select_approver1', data, {
                    nik: 'approver1_nik',
                    name: 'approver1_name',
                    ttd: 'approver1_ttd',
                    paraf: 'approver1_paraf',
                });

                populateApprover('select_approver2', data, {
                    nik: 'approver2_nik',
                    name: 'approver2_name',
                    ttd: 'approver2_ttd',
                    paraf: 'approver2_paraf',
                });
            }

            // Utility functions remain unchanged
            function populateSelect(selectElement, options, selectedValue, dataset) {
                selectElement.innerHTML = ""; // Clear previous options
                options.forEach(optionData => {
                    const option = document.createElement("option");
                    option.value = optionData.value;
                    option.textContent = optionData.text;
                    if (dataset) {
                        Object.keys(dataset).forEach(key => {
                            option.dataset[key] = dataset[key];
                        });
                    }
                    selectElement.appendChild(option);
                });
                selectElement.value = selectedValue;
                selectElement.disabled = true;
                selectElement.dispatchEvent(new Event('change')); // Trigger any change handlers
            }

            function populateApprover(selectId, data, fields) {
                const selectElement = document.getElementById(selectId);
                const ttdElement = document.getElementById(`${selectId}_ttd`);
                const parafElement = document.getElementById(`${selectId}_paraf`);

                if (selectElement) {
                    populateSelect(
                        selectElement,
                        [{ value: data[fields.nik], text: data[fields.name] }],
                        data[fields.nik],
                        { ttd: data[fields.ttd], paraf: data[fields.paraf] }
                    );

                    populateDependentFields(selectElement, ttdElement, parafElement);
                    selectElement.addEventListener("change", () =>
                        populateDependentFields(selectElement, ttdElement, parafElement)
                    );
                }
            }

            function populateDependentFields(selectElement, ttdElement, parafElement) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const ttd = selectedOption?.dataset.ttd || '';
                const paraf = selectedOption?.dataset.paraf || '';

                updateSelectField(ttdElement, ttd);
                updateSelectField(parafElement, paraf);
            }

            function updateSelectField(selectElement, value) {
                if (selectElement) {
                    selectElement.innerHTML = ""; // Clear previous options
                    if (value) {
                        const option = document.createElement("option");
                        option.value = value;
                        option.textContent = value;
                        selectElement.appendChild(option);
                    }
                    selectElement.disabled = !value; // Disable if no value
                }
            }

            // Start the process
            fetchAndVerifyData();
        }
        
        // mode edit
        if (persArea && idSurat && mode === 'edit') {

            const detailsUrl = base_url + `index.php/MasterBundler/detailsData?persArea=${persArea}&id_surat=${idSurat}`;
            console.log('Details URL:', detailsUrl);
            fetch(detailsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data) {
                        const fields = [
                            'select_persarea', 'PersArea_text', 'PersArea_inisial', 'PersArea_alamat',
                            'select_approver1', 'select_approver2', 'viewer',
                            'select_template', 'select_surat', 'select_isi', 'select_cap', 'viewer_name',

                            'select_template_header', 'select_template_footer',
                            'approver1_ttd', 'approver1_paraf',
                            'approver2_ttd', 'approver2_paraf'
                        ];
                        
                        const fieldsReadOnly = [
                            'select_persarea', 'PersArea_text', 'PersArea_inisial', 'PersArea_alamat',
                            'select_surat', 'viewer_name'
                        ];

                        fields.forEach(fieldId => {
                            const element = document.getElementById(fieldId);
                            if (element && data.data[fieldId] !== undefined) {
                                element.value = data.data[fieldId];
                                console.log(`Populated ${fieldId} with ${data.data[fieldId]}`);
                            }
                        });

                        fieldsReadOnly.forEach(fieldId => {
                            const element = document.getElementById(fieldId);
                            if (element && data.data[fieldId] !== undefined) {
                                element.value = data.data[fieldId];
                                if (fieldId === 'select_persarea' || fieldId === 'select_surat') {
                                    element.setAttribute('readonly', true); // Prevent edits but keep value extractable
                                    element.style.pointerEvents = 'none'; // Disable interaction visually
                                    element.style.backgroundColor = '#e9ecef'; // Optional: mimic a disabled field
                                } else {
                                    element.readOnly = true; // Make input fields read-only
                                }
                            }
                        });

                        // Update template yang dipilih
                        const templateSelect = document.getElementById('select_template');
                        if (templateSelect && data.data.select_template) {
                            // Kalau ada data untuk template, isi dan trigger perubahan
                            templateSelect.value = data.data.select_template;
                            templateSelect.dispatchEvent(new Event('change'));
                        }

                        // Update header template yang dipilih
                        const templateSelectHeader = document.getElementById('select_template_header');
                        if (templateSelectHeader && data.data.select_template_header) {
                            // Kalau ada data header, set value dan trigger event perubahan
                            templateSelectHeader.value = data.data.select_template_header;
                            templateSelectHeader.dispatchEvent(new Event('change'));
                        }

                        // Update footer template yang dipilih
                        const templateSelectFooter = document.getElementById('select_template_footer');
                        if (templateSelectFooter && data.data.select_template_footer) {
                            // Set value untuk footer dan jalankan perubahan
                            templateSelectFooter.value = data.data.select_template_footer;
                            templateSelectFooter.dispatchEvent(new Event('change'));
                        }

                        // Update approver 1 yang dipilih
                        const approver1Select = document.getElementById('select_approver1');
                        if (approver1Select && data.data.select_approver1) {
                            // Pilih approver 1 berdasarkan data
                            approver1Select.value = data.data.select_approver1;
                            approver1Select.dispatchEvent(new Event('change'));
                        }

                        // Update TTD dan paraf approver 1
                        const approver1Ttd = document.getElementById('select_approver1_ttd');
                        const approver1Paraf = document.getElementById('select_approver1_paraf');
                        if (approver1Ttd && data.data.approver1_ttd) {
                            // Isi value untuk TTD approver 1
                            approver1Ttd.value = data.data.approver1_ttd;
                            approver1Ttd.dispatchEvent(new Event('change'));
                        }
                        if (approver1Paraf && data.data.approver1_paraf) {
                            // Isi value untuk paraf approver 1
                            approver1Paraf.value = data.data.approver1_paraf;
                            approver1Paraf.dispatchEvent(new Event('change'));
                        }

                        // Update approver 2 yang dipilih
                        const approver2Select = document.getElementById('select_approver2');
                        if (approver2Select && data.data.select_approver2) {
                            // Pilih approver 2 berdasarkan data
                            approver2Select.value = data.data.select_approver2;
                            approver2Select.dispatchEvent(new Event('change'));
                        }

                        // Update TTD dan paraf approver 2
                        const approver2Ttd = document.getElementById('select_approver2_ttd');
                        const approver2Paraf = document.getElementById('select_approver2_paraf');
                        if (approver2Ttd && data.data.approver2_ttd) {
                            // Isi value untuk TTD approver 2
                            approver2Ttd.value = data.data.approver2_ttd;
                            approver2Ttd.dispatchEvent(new Event('change'));
                        }
                        if (approver2Paraf && data.data.approver2_paraf) {
                            // Isi value untuk paraf approver 2
                            approver2Paraf.value = data.data.approver2_paraf;
                            approver2Paraf.dispatchEvent(new Event('change'));
                        }

                        // Ganti teks tombol submit jadi "Update"
                        const submitButton = document.getElementById('submitButton');
                        if (submitButton) {
                            // Ganti teksnya biar sesuai konteks
                            submitButton.textContent = "Update";
                        }


                        document.getElementById('MasterBundler').addEventListener('submit', function(event) {
                            event.preventDefault();

                            const formData = new FormData(this);

                            formData.append('mode', 'edit');

                            formData.forEach((value, key) => {
                                console.log(`${key}: ${value}`);
                            });

                    
                            const url = base_url + "index.php/MasterBundler/process_form";

                            fetch(url, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text()) // Fetch response as text for debugging
                            .then(text => {
                                console.log('Raw Response:', text);
                                try {
                                    const data = JSON.parse(text); // Try parsing manually to see if it works
                                    if (data.status === 'success') {
                                        alert(data.message);
                                        window.location.href = `${base_url}index.php/MasterPT`;
                                    } else {
                                        alert(data.message || 'Error submitting the form');
                                    }
                                } catch (error) {
                                    console.error('JSON Parsing Error:', error, 'Raw Response:', text);
                                    alert('Error: Invalid server response');
                                }
                            })
                            .catch((error) => {
                                console.error('Fetch Error:', error);
                                alert('Error submitting the form');
                            });

                        }, { once: true });
                    } else {
                        alert("Failed to load additional data.");
                    }
                })
                .catch(error => {
                    console.error("Error fetching additional data:", error);
                    alert("Error fetching additional data.");
                });
        }

        // mode add
        if (mode === 'add') {
            const form = document.getElementById('MasterBundler');

            if (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(form);

                    let hasError = false;
                    form.querySelectorAll('input[required], select[required]').forEach(input => {
                        if (!input.value.trim()) {
                            hasError = true;
                            input.classList.add('error');
                            input.setAttribute('aria-invalid', 'true');
                        } else {
                            input.classList.remove('error');
                            input.removeAttribute('aria-invalid');
                        }
                    });

                    if (hasError) {
                        alert('Please fill in all required fields.');
                        return;
                    }

                    formData.append('mode', 'add');

                    console.log('Submitting form data:');
                    formData.forEach((value, key) => {
                        console.log(`${key}: ${value}`);
                    });

                    const url = `${base_url}index.php/MasterBundler/process_form`;

                    fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                window.location.href = `${base_url}index.php/MasterPT`;
                            } else if (data.status === 'error') {
                                alert(data.message || 'Error submitting the form');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error submitting the form');
                        });
                });
            } else {
                console.error('Form with ID "MasterBundler" not found.');
            }
        }
    });
    
    // preview button
    document.getElementById("previewButton").addEventListener("click", function () {
        const formElement = document.getElementById("MasterBundler");
        
        const fields = formElement.querySelectorAll('[readonly], [disabled]');
        fields.forEach(field => {
            field.removeAttribute('readonly');
            field.removeAttribute('disabled');
        });

        const formData = new FormData(formElement);

        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch( base_url + "index.php/MasterBundler/generate_pdf_preview", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');

            iframe.src = blobUrl;
            modal.style.display = 'flex';

            document.getElementById('closeModal').addEventListener('click', () => {
                modal.style.display = 'none';
                iframe.src = '';
                URL.revokeObjectURL(blobUrl);
            });
            // window.open(url);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the PDF. Please try again.');
        });

        fields.forEach(field => {
            field.setAttribute('readonly', true);
            field.setAttribute('disabled', true);
        });
    });

    // get nama viewer ketika NIK viewer diinput
    document.addEventListener("DOMContentLoaded", function() {
        const viewerInput = document.getElementById("viewer");

        let typingTimer;
        const typingDelay = 500;

        viewerInput.addEventListener("input", function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchApproverData, typingDelay);
        });

        function fetchApproverData() {
            const viewer = viewerInput.value.trim();

            if (viewer) {
        
                const url = base_url + "index.php/MasterBundler/get_viewer_data";

                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `NIK=${encodeURIComponent(viewer)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data) {
                        document.getElementById("viewer_name").value = data.data.Nama;
                    } else {
                        alert("Viewer not found.");
                        document.getElementById("viewer_name").value = "";
                    }
                })
                .catch(() => {
                    alert("Failed to load data");
                });
            }
        }
    });

    // Mengecek apakah user berada di halaman "detailsPage" berdasarkan URL
    const isDetailsPage = window.location.pathname.includes("detailsPage");

    // get tombol submit berdasarkan ID
    const submitButton = document.getElementById('submitButton');

    if (isDetailsPage) {
        // Jika di halaman detail, ubah teks tombol menjadi "Go Back"
        submitButton.textContent = 'Go Back';
        
        // Mengganti kelas tombol untuk mengubah tampilannya
        submitButton.classList.remove('btn-primary');
        submitButton.classList.add('btn-secondary');

        // Tambahkan event listener untuk kembali ke halaman sebelumnya
        submitButton.addEventListener('click', function() {
            window.history.back(); // Menggunakan history API untuk navigasi mundur
        });
    } else {
        // Jika bukan di halaman detail, ubah teks tombol menjadi "Submit"
        submitButton.textContent = 'Submit';

        // Tambahkan event listener untuk mengirim form
        submitButton.addEventListener('click', function() {
            // Dapatkan form berdasarkan ID
            const form = document.getElementById('MasterBundler');
            // Trigger event "submit" pada form
            form.dispatchEvent(new Event('submit'));
        });
    }

    function updateImagePreview(selectElement, imageType) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const previewImageId = `${imageType}ImagePreview`;
        const previewImage = document.getElementById(previewImageId);

        if (selectedOption && selectedOption.value) {
            const imageUrl = selectedOption.value; // Assuming the `value` holds the image URL
            previewImage.src = base_url + imageUrl;
            previewImage.alt = `Preview of ${imageType}`;
            previewImage.style.display = "block";
        } else {
            previewImage.src = "";
            previewImage.alt = "No image selected";
            previewImage.style.display = "none";
        }
    }

    // function updateCapPreview(selectElement) {
    //     const previewImage = document.getElementById('stampImagePreview');
    //     const selectedOption = selectElement.options[selectElement.selectedIndex];

    //     // Access gambar_cap from the dataset of the selected option
    //     const imageUrl = selectedOption.dataset.gambar_cap;

    //     if (imageUrl) {
    //         previewImage.src = base_url + imageUrl; // Set the image source to gambar_cap
    //         previewImage.style.display = 'block'; // Show the image preview
    //     } else {
    //         previewImage.src = ''; // Clear the image preview if no image is found
    //         previewImage.style.display = 'none'; // Hide the image preview
    //     }
    // }


</script>