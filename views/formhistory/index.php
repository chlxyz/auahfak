<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h2 style="margin: 0; font-size: 24px;">History Approval</h2>
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
                            <th>PersArea</th>
                            <th>Jenis Surat</th>
                            <th>Surat Detail</th>
                            <th>Keterangan</th>
                            <th>Approval 1</th>
                            <th>Approval 2</th>
                            <!-- <th>Reason Of Rejection</th> -->
                            <th>Overall Status</th>
                            <th>Alasan Penolakan</th>
                            <th>Preview PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history_data)): ?>
                            <?php echo $history_data; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">No Request</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div id="pagination" style="text-align: center; margin-top: 10px;">
                    <button id="prevPage" disabled>Previous</button>
                    <span id="pageInfo"></span>
                    <button id="nextPage">Next</button>
                </div>

                <div id="rowModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);">
                    <div style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:80%;">
                        <span id="closeRowModal" style="color:red; float:right; font-size:28px; font-weight:bold;">&times;</span>
                        <h2>Request Detail</h2>
                        <div id="modalContent"> <!-- Content will be filled dynamically --> </div>
                    </div>
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

    document.querySelectorAll('.previewBtn').forEach(button => {
        button.addEventListener('click', function () {
            const NIKRequester = this.getAttribute('data-id');
            const persAreaNumber = this.getAttribute('data-persarea-number');
            const jenisSurat = this.getAttribute('data-letter');
            const transactionId = this.getAttribute('data-transaction-id');

            console.log('persArea:', persAreaNumber, 'jenisSurat:', jenisSurat, 'NIKRequester:', NIKRequester, 'Transaction ID:', transactionId);

            fetch('Formhistory/generate_pdf_preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ persAreaNumber, jenisSurat, NIKRequester, transactionId }),
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

    var modal = document.getElementById("rowModal");

    var span = document.getElementById("closeRowModal");

    function openModal(rowData) {
        document.getElementById("modalContent").innerHTML = rowData;
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>