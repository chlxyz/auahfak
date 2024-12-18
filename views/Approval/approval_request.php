<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Approval Request</h1>
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
                            <th>Approval 1 <br><span>( Paraf )</span></th>
                            <th>Approval 2 <br><span>( TTD )</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</section>

<div id="approvalModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;">
    <div style="background: white; margin: 50px auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 1000px; position: relative; display: flex; gap: 20px;">

        <div style="flex: 1;">
            <h3 style="color: black;">Approval Details</h3>
            <form id="approvalForm">
                <div class="form-group">
                    <label for="requesterNik">Requester NIK</label>
                    <input type="text" id="requesterNik" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="requesterName">Requester Name</label>
                    <input type="text" id="requesterName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="requesterPersareaNumber">Requester Persarea Number</label>
                    <input type="text" id="requesterPersareaNumber" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <input type="hidden" id="transactionId">
                </div>
                <div class="form-group">
                    <label for="requesterPersarea">Requester Persarea</label>
                    <input type="text" id="requesterPersarea" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="requestDate">Request Date</label>
                    <input type="text" id="requestDate" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="letterType">Letter Type</label>
                    <input type="text" id="letterType" class="form-control" readonly>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <button type="button" id="previewBtn" class="btn btn-info previewBtn">Preview</button>
                    <button type="button" id="approveBtn" class="btn btn-success approveBtn">Approve</button>
                    <button type="button" id="rejectBtn" class="btn btn-warning rejectBtn">Reject</button>
                    <button type="button" id="closeModal" class="btn btn-secondary">Close</button>
                </div>
                <div class="form-group" style="display: none;">
                    <label for="rejectionReason">Reason: </label>
                    <input type="text-area" id="rejectionReason" class="form-control">
                </div>
                <div class="form-group" style="display: none;">
                    <button type="button" id="confirmBtn" class="btn btn-info confirmBtn">Confirm</button>
                </div>

            </form>
        </div>

        <div id="pdfPreviewContainer" style="flex: 1; background: #333; display: flex; align-items: center; justify-content: center; color: white; border: 1px solid #555; border-radius: 8px;">
            <p style="text-align: center; font-size: 14px;"></p>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        loadTableData();

        function loadTableData() {
            const base_url = "<?php echo base_url(); ?>";
            const url = base_url + "index.php/Approval/load_data";

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.querySelector('table tbody').innerHTML = data;

                    document.querySelectorAll('.actionButton ').forEach(button => {
                        button.addEventListener('click', function () {
                            const nik = this.getAttribute('data-id');
                            const name = this.getAttribute('data-name');
                            const persarea = this.getAttribute('data-persarea');
                            const persareanumber = this.getAttribute('data-persarea-number');
                            const transactionId = this.getAttribute('data-transaction');
                            const row = this.closest('tr');
                            const requestDate = row.children[1].innerText;
                            const letterType = row.children[5].innerText;

                            document.getElementById('requesterNik').value = nik;
                            document.getElementById('requesterName').value = name;
                            document.getElementById('requesterPersarea').value = persarea;
                            document.getElementById('requesterPersareaNumber').value = persareanumber;
                            document.getElementById('requestDate').value = requestDate;
                            document.getElementById('letterType').value = letterType;
                            document.getElementById('transactionId').value = transactionId;

                            document.getElementById('approvalModal').style.display = 'block';
                        });
                    });

                    document.querySelectorAll('.approveBtn').forEach(button => {
                        button.addEventListener('click', function () {
                            console.log('Approve button clicked');

                            const nikRequester = document.getElementById('requesterNik').value;
                            const letterType = document.getElementById('letterType').value;
                            const transactionId = document.getElementById('transactionId').value;
                            const persArea = document.getElementById('requesterPersarea').value;

                            console.log('Data being sent:', {
                                NIKRequester: nikRequester,
                                letterType: letterType,
                                transactionId: transactionId
                            });

                            fetch('Approval/approve', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    NIKRequester: nikRequester,
                                    letterType: letterType,
                                    transactionId: transactionId,
                                    persArea: persArea
                                }),
                            })
                            .then(async response => {
                                console.log('Raw response:', response);
                                const contentType = response.headers.get('Content-Type');
                                const rawText = await response.text();

                                if (contentType && contentType.includes('application/json')) {
                                    try {
                                        return JSON.parse(rawText);
                                    } catch (jsonError) {
                                        console.error('Failed to parse JSON:', jsonError, rawText);
                                        throw new Error('Invalid JSON response');
                                    }
                                } else {
                                    console.error('Unexpected response format:', rawText);
                                    throw new Error('Server returned an unexpected response format');
                                }
                            })
                            .then(data => {
                                console.log('Parsed server response:', data);
                                if (data.success) {
                                    alert(data.message || 'Approval successfully processed');
                                    button.disabled = true;
                                    button.classList.add('btn-disabled');
                                } else {
                                    alert(data.message || 'Approval failed');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                        });
                    });

                    document.querySelectorAll('.rejectBtn').forEach(button => {
                        button.addEventListener('click', function () {
                            console.log('Reject button clicked');
                            const rejectionReasonGroup = document.querySelector('#approvalForm .form-group:nth-child(9)');
                            const confirmButtonGroup = document.querySelector('#approvalForm .form-group:nth-child(10)');

                            rejectionReasonGroup.style.display = 'block';
                            confirmButtonGroup.style.display = 'block';
                        });
                    });

                    document.getElementById('confirmBtn').addEventListener('click', function () {
                        const rejectionReason = document.getElementById('rejectionReason').value;
                        const nikRequester = document.getElementById('requesterNik').value;
                        const letterType = document.getElementById('letterType').value;
                        const transactionId = document.getElementById('transactionId').value;

                        if (!rejectionReason) {
                            alert('Please provide a rejection reason.');
                            return;
                        }

                        console.log('Rejection reason submitted:', rejectionReason);

                        fetch('Approval/approve', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                NIKRequester: nikRequester,
                                letterType: letterType,
                                rejectionReason: rejectionReason,
                                transactionId: transactionId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Server response:', data);
                            if (data.success) {
                                alert(data.message || 'Rejection successfully processed');
                                document.querySelector('.approveBtn').disabled = true;
                                document.querySelector('.approveBtn').classList.add('btn-disabled');
                            } else {
                                alert(data.message || 'Rejection failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });

                        document.getElementById('approvalModal').style.display = 'none';
                    });

                                
                    document.querySelectorAll('.previewBtn').forEach(button => {
                        button.addEventListener('click', function () {
                            const persAreaNumber = document.getElementById('requesterPersareaNumber').value;
                            const transactionId = document.getElementById('transactionId').value;
                            const jenisSurat = document.getElementById('letterType').value;
                            const NIKRequester = document.getElementById('requesterNik').value;

                            console.log('persArea:', persAreaNumber, 'jenisSurat:', jenisSurat, 'NIKRequester:', NIKRequester, 'TransactionId:', transactionId);

                            fetch('Approval/generate_pdf_preview', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({persAreaNumber, jenisSurat, NIKRequester, transactionId }),
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.blob();
                                })
                                .then(blob => {
                                    console.log('PDF Blob received');

                                    const blobUrl = URL.createObjectURL(blob);

                                    console.log('Blob URL:', blobUrl);

                                    // Check if the container already exists
                                    let pdfContainer = document.getElementById('pdfPreviewContainer');
                                    if (!pdfContainer) {
                                        pdfContainer = document.createElement('div');
                                        pdfContainer.id = 'pdfPreviewContainer';
                                        pdfContainer.style.marginTop = '20px';
                                        pdfContainer.style.width = '100%';
                                        pdfContainer.style.height = '500px';
                                        pdfContainer.style.border = '1px solid #ddd';
                                        document.querySelector('#approvalForm').appendChild(pdfContainer);
                                    }

                                    // Clear and add iframe to the container
                                    pdfContainer.innerHTML = '';
                                    const iframe = document.createElement('iframe');
                                    iframe.style.width = '100%';
                                    iframe.style.height = '100%';
                                    iframe.style.border = 'none';
                                    iframe.src = blobUrl; // Set the source here
                                    pdfContainer.appendChild(iframe);

                                    // Add close modal event
                                    // document.getElementById('closeModal').addEventListener('click', () => {
                                    //     URL.revokeObjectURL(blobUrl);
                                    //     pdfContainer.remove();
                                    // });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred. Please try again.');
                                });
                        });
                    });

                })
                .catch(() => {
                    alert("Failed to load data");
                });
        }
        
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('approvalModal').style.display = 'none';
            resetModal();
        });

        function resetModal() {
            document.getElementById('approvalModal').style.display = 'none';

            document.querySelector('#approvalForm .form-group:nth-child(9)').style.display = 'none';
            document.querySelector('#approvalForm .form-group:nth-child(10)').style.display = 'none';

            document.getElementById('rejectionReason').value = '';
            document.getElementById('pdfPreviewContainer').innerHTML = '';
        }

        window.addEventListener('click', function (event) {
            const modal = document.getElementById('approvalModal');
            if (event.target === modal) {
                modal.style.display = 'none';
                resetModal();
            }
        });
    });
</script>