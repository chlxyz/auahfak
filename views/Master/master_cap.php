<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master Cap</h1>
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
                            <th>Nama Cap</th>
                            <th>Gambar Cap</th>
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
                        <h5 style="margin: 0 0 20px; font-size: 18px;">Add/Edit Cap</h5>

                        <form id="TemplateMaster">
                            <input type="hidden" name="id_cap" id="id_cap">
                            <input type="hidden" name="mode" id="mode" value="add">

                            <label for="StampName" class="control-label">Stamp name:</label>
                            <input type="text" class="span5" name="StampName" id="StampName" required><br><br>

                            <label for="Stamp" class="control-label">Upload stamp:</label>
                            <input type="file" class="span5" name="Stamp" id="Stamp" required><br><br>
                            <img id="StampPreview" src="" alt="Stamp Preview" style="display: none; max-width: 200px; max-height: 200px;"><br><br>

                            <button type="submit" class="btn btn-primary">Submit</button>
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

    // menampilkan modal jika mengclick button add, base mode add
    addButton.addEventListener('click', function() {
        document.getElementById('TemplateMaster').reset();
        document.getElementById('StampPreview').style.display = 'none';
        document.getElementById('mode').value = 'add';
        document.getElementById('id_cap').value = '';
        document.getElementById('TemplateMaster').style.display = 'block';
        modal.style.display = 'flex';
    });

    // Meload data tabel
   function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/MasterCap/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
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
    
    // menutup modal jika mengclick button close
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // menuutp modal jika mengclick button cancel
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
    

    // menutup modal jika klik diluar modal
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // edit button, mode edit
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editButton')) {
            const id = event.target.getAttribute('data-id');
                const name = event.target.getAttribute('data-name');
                const image = event.target.getAttribute('data-image');

                document.getElementById('id_cap').value = id;
                document.getElementById('StampName').value = name;
                document.getElementById('mode').value = 'edit';

                const preview = document.getElementById('StampPreview');
                preview.src = image;
                preview.style.display = 'block';
                
                document.getElementById('TemplateMaster').style.display = 'block';
                modal.style.display = 'flex';
        }
    });

    // preview gambar
    document.getElementById('Stamp').addEventListener('change', function() {
        previewImage(this, 'StampPreview');
    });

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

    // submit data ke controller
    document.getElementById('TemplateMaster').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        const base_url = "<?php echo base_url(); ?>";
        const url = base_url + "index.php/MasterCap/process_form";

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

    // button active dan inactive
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('toggle-btn')) {
                const button = e.target;
                const id = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');
                const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
                const base_url = "<?php echo base_url(); ?>";

                fetch(base_url + 'index.php/MasterCap/update_status', {
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

    // update texxtt button dan attribute ketika active dan inactive
    function toggleButtons(id, newStatus) {
        const button = document.querySelector(`button[data-id="${id}"]`);
        
        if (button) {
            button.textContent = newStatus;
            button.setAttribute('data-status', newStatus);
        }
    }
</script>
