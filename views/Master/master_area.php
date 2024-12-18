<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master PT</h1>
                </div>

                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="searchInput" placeholder="Search..." style="padding: 5px; width: 200px;">
                    <button id="searchButton" style="padding: 5px;">Search</button>
                </div>

                <table class="table table-striped table-bordered table-hover" id="TableDataPT" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>PersArea</th>
                            <th>Area name</th>
                            <th>Area initial</th>
                            <th>Area address</th>
                            <th>Surat number</th>
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
                        
                        <h5 style="margin: 0 0 20px; font-size: 18px;">Edit Area</h5>
                        
                        <form id="MasterArea">
                            <div style="padding-top: 5px; text-align: left;">
                                <label for="PersArea" style="padding-top: 5px;">PersArea:</label>
                                <input type="text" name="PersArea" id="PersArea" required>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label for="AreaName" style="padding-top: 5px;">Area Name:</label>
                                <input type="text" name="AreaName" id="AreaName" required>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label for="AreaInisial" style="padding-top: 5px;">Area Initial:</label>
                                <input type="text" name="AreaInisial" id="AreaInisial" required>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label for="AreaAddress" style="padding-top: 5px;">Area Address:</label>
                                <input type="text" name="AreaAddress" id="AreaAddress" required>
                            </div>

                            <div style="padding-top: 5px; text-align: left;">
                                <label for="NomorSurat" style="padding-top: 5px;">Surat Number:</label>
                                <input type="text" name="NomorSurat" id="NomorSurat">
                            </div>

                            <button type="button" id="cancelButton">Cancel</button>
                            <button type="submit">Submit</button>
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
    const closeModalButton = document.getElementById('closeModal');
    const cancelButton = document.getElementById('cancelButton');
    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    
    let currentPage = 1;
    const rowsPerPage = 5;

    closeModalButton.addEventListener('click', () => modal.style.display = 'none');
    cancelButton.addEventListener('click', () => modal.style.display = 'none');

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

    window.addEventListener('click', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('editButton')) {
                const persarea = event.target.getAttribute('data-id');
                const name = event.target.getAttribute('data-name');
                const initial = event.target.getAttribute('data-initial');
                const address = event.target.getAttribute('data-address');
                const nomorsurat = event.target.getAttribute('data-nomorsurat');

                document.getElementById('PersArea').readOnly = true;
                document.getElementById('AreaName').readOnly = true;

                document.getElementById('PersArea').value = persarea;
                document.getElementById('AreaName').value = name;
                document.getElementById('AreaInisial').value = initial;
                document.getElementById('AreaAddress').value = address;
                document.getElementById('NomorSurat').value = nomorsurat;
                
                document.getElementById('MasterArea').style.display = 'block';
                modal.style.display = 'flex';
        }
    });

    function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/MasterArea/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
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

    document.addEventListener("DOMContentLoaded", () => loadTableData());


    document.getElementById('MasterArea').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        
        const url = base_url + "index.php/MasterArea/process_form";

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                modal.style.display = 'none';
                loadTableData();
            }
        })
        .catch(() => alert('Error submitting the form'));
    });
</script>
