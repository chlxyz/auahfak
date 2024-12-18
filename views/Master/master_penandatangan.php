<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master PenandaTangan</h1>
                </div>

                <div style="margin-left: 20px;">
                    <button type="button" class="btn btn-success" id="addButton">+ Add New</button>
                </div>

                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="searchInput" placeholder="Search..." style="padding: 5px; width: 200px;">
                    <button id="searchButton" style="padding: 5px;">Search</button>
                </div>

                <table class="table table-striped table-bordered table-hover" id="TableDataPT" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Signature</th>
                            <th>Initials</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div id="pagination" style="text-align: center; margin-top: 10px;">
                    <button id="prevPage" disabled>Previous</button>
                    <span id="pageInfo"></span>
                    <button id="nextPage">Next</button>
                </div>

                <div id="TemplateMasterModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
                    <div style="background: #fff; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; position: relative; max-height: 80vh; overflow-y: auto;">
                        <button id="closeModal" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                        <h5 style="margin: 0 0 20px; font-size: 18px;">Add/Edit PenandaTangan</h5>
                        
                        <form id="MasterApprovers" style="display: none; margin-top: 20px;">
                            <input type="hidden" name="id_approver" id="id_approver">
                            <input type="hidden" name="mode" id="mode" value="add">
                            <div style="padding-top: 5px; text-align: left;">
                                <label class="control-label span3" for="approver" style="text-align: left; padding-top: 5px;">Approver NIK : </label>
                                <div class="controls">
                                    <input type="text" class="span5" name="approver_nik" id="approver_nik" required>
                                </div>
                            </div>
                            <div style="padding-top: 5px; text-align: left;">
                                <label class="control-label span3" for="approver_name" style="text-align: left; padding-top: 5px;">Approver Name : </label>
                                <div class="controls">
                                    <input type="text" class="span5" name="approver_name" id="approver_name" readonly>
                                </div>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label class="control-label span3" for="approver_ttd" style="text-align: left; padding-top: 5px;">Approver ttd: </label>
                                <div class="controls">
                                    <input type="file" class="span5" name="approver_ttd" id="approver_ttd" onchange="previewImage(this, 'approver_ttd_preview')" required>
                                    <img id="approver_ttd_preview" style="display:none; max-height:100px;"/>
                                </div>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label class="control-label span3" for="approver_paraf" style="text-align: left; padding-top: 5px;">Approver paraf: </label>
                                <div class="controls">
                                    <input type="file" class="span5" name="approver_paraf" id="approver_paraf" onchange="previewImage(this, 'approver_paraf_preview')" required>
                                    <img id="approver_paraf_preview" style="display:none; max-height:100px;"/>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" id="submitButton" style="visibility: hidden;">Submit</button>
                            <button type="button" class="btn btn-secondary" id="cancelButton">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<script>
    const base_url = "<?php echo base_url(); ?>";
    const modal = document.getElementById('TemplateMasterModal');
    const addButton = document.getElementById('addButton');
    const closeModalButton = document.getElementById('closeModal');
    const cancelButton = document.getElementById('cancelButton');
    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const rowsPerPage = 5;

    // saat tombol "add new" diklik, modal untuk menambah data akan terbuka
    addButton.addEventListener('click', function() {
        document.getElementById('MasterApprovers').reset();
        document.getElementById('approver_ttd_preview').style.display = 'none';
        document.getElementById('approver_paraf_preview').style.display = 'none'
        document.getElementById('MasterApprovers').style.display = 'block';
        modal.style.display = 'flex'; 
    });

    // Meload data tabel
    function loadTableData(searchQuery = '') {
            const url = base_url + `index.php/MasterPenandaTangan/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
            
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#TableDataPT tbody').innerHTML = html;
                    
                    const totalPages = 120; // Update this dynamically based on your backend response if needed.
                    
                    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
                    prevPageButton.disabled = currentPage === 1;
                    nextPageButton.disabled = currentPage === totalPages;
                })
                .catch(() => alert("Failed to load data"));
    }

    document.addEventListener("DOMContentLoaded", () => loadTableData());

    // Event listener for the search button
    document.getElementById('searchButton').addEventListener('click', () => {
        const searchQuery = document.getElementById('searchInput').value;
        currentPage = 1; // Reset to the first page on a new search
        loadTableData(searchQuery);
    });

    // Event listener for the Enter key in the search bar
    document.getElementById('searchInput').addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchQuery = event.target.value;
            currentPage = 1; // Reset to the first page on a new search
            loadTableData(searchQuery);
        }
    });

    // Menutup modal saat tombol "close" diklik
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Menutup modal saat tombol "cancel" diklik
    cancelButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    prevPageButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            loadTableData();
        }
    });

    nextPageButton.addEventListener('click', () => {
        currentPage++;
        loadTableData();
    });
    
    // Menutup modal jika user mengklik area di luar modal
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // preview gambah ttd
    document.getElementById('approver_ttd').addEventListener('change', function() {
        previewImage(this, 'approver_ttd_preview');
    });

    // preview gambar paraf
    document.getElementById('approver_paraf').addEventListener('change', function() {
        previewImage(this, 'approver_paraf_preview');
    });

    // Ketika tombol edit diklik, data akan diisi di form untuk diedit
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editButton')) {
            const id = event.target.getAttribute('data-id');
            const name = event.target.getAttribute('data-name');
            const ttd = event.target.getAttribute('data-ttd');
            const paraf = event.target.getAttribute('data-paraf');

            const approverNikInput = document.getElementById('approver_nik');
            
            // approverNikInput.readOnly = true;

            document.getElementById('approver_nik').value = id;
            document.getElementById('approver_name').value = name;
            document.getElementById('mode').value = 'edit';


            const preview = document.getElementById('approver_ttd_preview');
            preview.src = ttd;
            preview.style.display = 'block';

            const preview2 = document.getElementById('approver_paraf_preview');
            preview2.src = paraf;
            preview2.style.display = 'block';

            document.getElementById('MasterApprovers').style.display = 'block';
            document.getElementById('TemplateMasterModal').style.display = 'flex';
        }
    });

    // preview image function
    function previewImage(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }

    // fetch data approver
    document.addEventListener("DOMContentLoaded", function() {
        const approverNikInput = document.getElementById("approver_nik");
        const submitButton = document.getElementById("submitButton"); // button submit default diset hidden

        let typingTimer;
        const typingDelay = 500;

        approverNikInput.addEventListener("input", function() {
            clearTimeout(typingTimer);
            submitButton.style.visibility = "hidden"; // button submit diset hidden saat user input NIK
            typingTimer = setTimeout(fetchApproverData, typingDelay);
        });

        function fetchApproverData() {
            const approverNik = approverNikInput.value.trim();

            if (approverNik) {
                const url = base_url + "index.php/MasterPenandaTangan/get_approver_data";

                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `NIK=${encodeURIComponent(approverNik)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data) {
                        document.getElementById("approver_name").value = data.data.Nama;
                        submitButton.style.visibility = "visible"; // jika NIK valid, button submit akan muncul
                    } else {
                        alert("Approver not found.");
                        document.getElementById("approver_name").value = "";
                        submitButton.style.visibility = "hidden"; // jika NIK invalid, button submit akan tetap hidden
                    }
                })
                .catch(() => {
                    alert("Failed to load data");
                    submitButton.style.visibility = "hidden"; // jika error, button akan tetap hidden
                });
            }
        }
    });

    // Mengirim data form saat form disubmit
    document.getElementById('MasterApprovers').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        const url = base_url + "index.php/MasterPenandaTangan/process_form";

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error submitting the form');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Error submitting the form');
        });
    });

    // Fungsi untuk toggle status (Active/Inactive) approver
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            // Check if clicked button has the class 'toggle-btn'
            if (e.target && e.target.classList.contains('toggle-btn')) {
                const button = e.target;
                const id = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');
                const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

                fetch(base_url + 'index.php/MasterPenandaTangan/update_status', {
                    method: 'POST',
                    body: JSON.stringify({ id: id, status: newStatus }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toggleButtons(id, newStatus);
                        window.location.reload();
                    } else {
                        console.error('Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });

    // Mengatur tombol toggle status sesuai ID
    function toggleButtons(id, newStatus) {
        const button = document.querySelector(`button[data-id="${id}"]`);
        
        if (button) {
            // Update the button text and status
            button.textContent = newStatus;
            button.setAttribute('data-status', newStatus);
        }
    }

    
</script>
