<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master Surat</h1>
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
                            <th>Surat</th>
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
                    <div style="background: #fff; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
                        <button id="closeModal" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                        
                        <h5 style="margin: 0 0 20px; font-size: 18px;">Add/Edit Surat</h5>
                        
                        <form id="TemplateMaster">
                            <input type="hidden" id="id_surat" name="id_surat">
                            <input type="hidden" id="mode" name="mode">

                            <div style="padding-top: 5px; text-align: left;">
                                <label for="surat_text" style="display: block; margin-bottom: 5px;">Surat Text :</label>
                                <input type="text" id="surat_text" name="surat_text" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 4px;" required>
                                <!-- <select style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;" name="surat_text" id="surat_text" required>
                                    <option value="SIM">SIM</option>
                                    <option value="VISA">VISA</option>
                                    <option value="KPR">KPR</option>
                                </select> -->
                            </div>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button type="submit" name="submit" id="submitButton" style="visibility: hidden; padding: 8px 16px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Submit</button>
                                <button type="button" id="cancelButton" style="padding: 8px 16px; margin-left: 10px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                            </div>
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
        document.getElementById('TemplateMaster').reset();
        document.getElementById('mode').value = 'add';
        document.getElementById('surat_text').value = '';
        document.getElementById('TemplateMaster').style.display = 'block';
        modal.style.display = 'flex';
    });

    // Meload data tabel
    function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/MasterSurat/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
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

    // Ketika tombol edit diklik, data akan diisi di form untuk diedit
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editButton')) {
            const id = event.target.getAttribute('data-id');
            const name = event.target.getAttribute('data-name');

            document.getElementById('id_surat').value = id;
            document.getElementById('surat_text').value = name;
            document.getElementById('mode').value = 'edit';

            modal.style.display = 'flex';
        }
    });

    // Mengirim data form saat form disubmit
    document.getElementById('TemplateMaster').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        const url = base_url + "index.php/MasterSurat/process_form";

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

    // Fungsi untuk toggle status (Active/Inactive) surat
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('toggle-btn')) {
                const button = e.target;
                const id = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');
                const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

                fetch(base_url + 'index.php/MasterSurat/update_status', {
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

    // Mengecek apakah input "surat_text" valid atau sudah ada
    document.addEventListener("DOMContentLoaded", function () {
        const suratTextInput = document.getElementById("surat_text");
        const submitButton = document.getElementById("submitButton"); // button submit default diset hidden

        let typingTimer;
        const typingDelay = 500;

        suratTextInput.addEventListener("input", function () {
            clearTimeout(typingTimer);
            submitButton.style.visibility = "hidden"; // button submit diset hidden saat user input NIK
            typingTimer = setTimeout(checkSuratData, typingDelay);
        });

        function checkSuratData() {
            const suratText = suratTextInput.value.trim();

            if (suratText) {
                const url = base_url + "index.php/MasterSurat/get_surat_data";

                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `suratText=${encodeURIComponent(suratText)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "error" && data.message === "Data already exists") {
                            alert("Data already exists. Please use a different value.");
                            submitButton.style.visibility = "hidden"; // jika data sudat ada, button akan tetap hidden
                        } else if (data.status === "success") {
                            alert(data.message); // Show success message
                            submitButton.style.visibility = "visible"; // jika data tidak data, button submit akan muncul
                        } else {
                            alert(data.message || "Unexpected response from the server.");
                            submitButton.style.visibility = "hidden"; // jika unexpected response, button akan tetap hidden
                        }
                    })
                    .catch(() => {
                        alert("Failed to check or insert data");
                        submitButton.style.visibility = "hidden"; // jika failed, button akan tetap hidden
                    });
            }
        }
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
