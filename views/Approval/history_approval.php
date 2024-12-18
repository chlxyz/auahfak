<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <est style="margin: 0; font-size: 24px;">History Approval</h1>
                </div>

                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="searchInput" placeholder="Search..." style="padding: 5px; width: 200px;">
                    <button id="searchButton" style="padding: 5px;">Search</button>
                </div>

                <table class="table table-striped table-bordered table-hover" id="TableDataPT" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Request Date</th>
                            <th>NIK</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Letter Type</th>
                            <th>Letter Detail</th>
                            <th>Description</th>
                            <th>Approval 1</th>
                            <th>Approval 2</th>
                            <th>Reason of rejection</th>
                            <th>PDF</th>
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

<div id="pdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center;">
    <div style="position: relative; width: 80%; height: 80%; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); overflow: hidden;">
        <button id="closeModal" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px; font-weight: bold;">&times;</button>
        <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
</div>

<script>
    const base_url = "<?php echo base_url(); ?>";

    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const rowsPerPage = 5;

    function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/Approval/get_history?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.querySelector('table tbody').innerHTML = data;

                document.querySelectorAll('.previewBtn').forEach(button => {
                    button.addEventListener('click', function () {
                        const NIKRequester = this.getAttribute('data-id');
                        const persAreaNumber = this.getAttribute('data-persarea-number');
                        const jenisSurat = this.getAttribute('data-letter');
                        const transactionId = this.getAttribute('data-transaction-id');

                        console.log('persArea:', persAreaNumber, 'jenisSurat:', jenisSurat, 'NIKRequester:', NIKRequester, 'transactionId:', transactionId);

                        fetch('Approval/generate_pdf_preview_history', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ persAreaNumber, jenisSurat, NIKRequester, transactionId}),
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
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while loading the PDF. Please try again.');
                            });
                    });
                });
            })
            .catch(() => {
                alert("Failed to load data");
            });
    }
    document.addEventListener("DOMContentLoaded", () => loadTableData());

    document.getElementById('searchButton').addEventListener('click', () => {
        const searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        loadTableData(searchQuery);
    });

    document.getElementById('searchInput').addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchQuery = event.target.value;
            currentPage = 1;
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