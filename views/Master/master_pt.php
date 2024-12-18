<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master Bundler</h1>
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
                            <th>Pers Area</th>
                            <th>Area Name</th>
                            <th>Check Letter Details</th>
                            <th>Letter Type</th>
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
            </div>
        </div>
    </section>
</section>


<script>
    const base_url = "<?php echo base_url(); ?>";
    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const rowsPerPage = 5;

    // redirect user ke details berdasarkan persarea dan jenis surat dengan mode 
    document.addEventListener('click', function(event) {
        // - Untuk 'detailsButton': Mengarahkan pengguna ke halaman detail dalam mode view, 
        //   dengan mengirimkan 'persArea' dan 'id_surat' sebagai parameter query.
        if (event.target.classList.contains('detailsButton')) {
            const persArea = event.target.getAttribute('data-id');
            const idSurat = event.target.getAttribute('data-name');
            const mode = 'view';

            window.location.href = base_url + `index.php/MasterBundler/detailsPage?persArea=${persArea}&id_surat=${idSurat}&mode=${mode}`;
        }

        // - Untuk 'editButton': Mengarahkan pengguna ke halaman edit detail dalam mode edit, 
        //   dengan mengirimkan 'persArea' dan 'id_surat' sebagai parameter query.
        if (event.target.classList.contains('editButton')) {
            const persArea = event.target.getAttribute('data-id');
            const idSurat = event.target.getAttribute('data-name');
            const mode = 'edit';

            window.location.href = base_url + `index.php/MasterBundler/editDetails?persArea=${persArea}&id_surat=${idSurat}&mode=${mode}`;
        }
    });

    // redirect user ke halaman add dengan mode add ketika user menglick tombol 'Add New'
    document.getElementById('addButton').addEventListener('click', function() {
        const mode = 'add'; 
        window.location.href = base_url + `index.php/MasterBundler/add?mode=${mode}`;
    });

   // Meload data tabel
   function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/MasterPT/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
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
</script>
