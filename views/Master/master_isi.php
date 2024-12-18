<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h1 style="margin: 0; font-size: 24px;">Master Isi</h1>
                </div>

                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="searchInput" placeholder="Search..." style="padding: 5px; width: 200px;">
                    <button id="searchButton" style="padding: 5px;">Search</button>
                </div>

                <table class="table table-striped table-bordered table-hover" id="TableDataPT" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Details</th>
                            <th>Attachment 1</th>
                            <th>Attachment 2</th>
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

                <div style="margin: 20px">
                    <form id="TemplateMaster">
                        <label for="title">Insert judul :</label>
                        <input type="text" class="span5" name="title" id="title" style="width: 100%; height: 50%; resize: none;" required><br><br>

                        <label for="Isi">Isi :</label>
                        <textarea class="span5" name="Isi" id="Isi" style="width: 100%; height: 50%; resize: none;" required></textarea><br><br>

                        <label for="Lampiran1">Lampiran 1 :</label>
                        <input type="file" name="Lampiran1" id="Lampiran1"><br><br>

                        <label for="Lampiran2">Lampiran 2 :</label>
                        <input type="file" name="Lampiran2" id="Lampiran2"><br><br>
                        
                        <button type="button" id="previewButton" class="btn btn-secondary">Preview</button>
                        <button type="submit" id="submitButton" name="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</section>

<script src="<?php echo base_url('ckeditor/ckeditor.js'); ?>"></script>

<script>
    const base_url = "<?php echo base_url(); ?>";
    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const rowsPerPage = 5;


    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('detailsButton')) {
            const id_isi = event.target.getAttribute('data-id');
            const mode = 'view';

            window.location.href = base_url + `index.php/MasterIsi/detailsPage?id_isi=${id_isi}&mode=${mode}`;
        }

        if (event.target.classList.contains('editButton')) {
            const id_isi = event.target.getAttribute('data-id');
            const mode = 'edit';

            window.location.href = base_url + `index.php/MasterIsi/editDetails?id_isi=${id_isi}&mode=${mode}`;
        }
    });

     document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const id_isi = urlParams.get('id_isi');
        const mode = urlParams.get('mode');

        if (id_isi && mode === 'view') {
            const detailsUrl = base_url + `index.php/MasterIsi/detailsData?id_isi=${id_isi}`;
            fetch(detailsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data) {
                        const { isi, Judul, lampiran1, lampiran2 } = data.data;

                        document.getElementById("title").value = Judul || "";
                        document.getElementById("title").readOnly = true;
                        CKEDITOR.instances.Isi.setData(isi || "");
                        document.getElementById("Isi").readOnly = true;
                        document.getElementById("Lampiran1").value = lampiran1 || "";
                        document.getElementById("Lampiran2").value = lampiran2 || "";

                        console.log("Form populated with data:", data.data);

                        document.getElementById("previewButton").addEventListener("click", function () {
                            const formElement = document.getElementById("TemplateMaster");

                            const formData = new FormData(formElement);

                            // Extract the content from CKEditor for preview as well
                            const editorData = CKEDITOR.instances['Isi'].getData();
                            formData.set('Isi', editorData);

                            formData.append('mode', 'view');

                            console.log("Data being sent to the server:");
                            formData.forEach((value, key) => {
                                console.log(`${key}: ${value}`);
                            });

                            fetch(base_url + "index.php/MasterIsi/generate_pdf_preview", {
                                method: "POST",
                                body: formData,
                            })
                            .then(response => {
                                if (!response.ok) throw new Error("Failed to generate preview");
                                return response.blob();
                            })
                            .then(blob => {
                                const url = URL.createObjectURL(blob);
                                window.open(url);
                            })
                            .catch(error => console.error("Error generating preview:", error));
                        });
                    } else {
                        alert("Failed to load additional data.");
                    }
                })
                .catch(error => {
                    console.error("Error fetching additional data:", error);
                });
        }

        const form = document.getElementById('TemplateMaster');
        const submitButton = document.getElementById('submitButton');

        let isSubmitting = false;

        // Check if in 'edit' mode and populate fields if true
        if (id_isi && mode === 'edit') {
            const detailsUrl = base_url + `index.php/MasterIsi/detailsData?id_isi=${id_isi}`;
            fetch(detailsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data) {
                        const { isi, Judul, lampiran1, lampiran2 } = data.data;

                        document.getElementById("title").value = Judul || "";
                        CKEDITOR.instances.Isi.setData(isi || "");
                        document.getElementById("Lampiran1").value = lampiran1 || "";
                        document.getElementById("Lampiran2").value = lampiran2 || "";

                        // Change submit button text to "Update" for editing
                        if (submitButton) {
                            submitButton.textContent = "Update";
                        }
                    } else {
                        alert("Failed to load additional data.");
                    }
                })
                .catch(error => {
                    console.error("Error fetching additional data:", error);
                });
        }

        // General submit event handler for both add and edit modes
        function handleFormSubmit(event) {
            event.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;

            const formData = new FormData(form);

            const editorData = CKEDITOR.instances['Isi'].getData();
            formData.set('Isi', editorData);

            console.log(editorData);

            // Client-side validation
            const title = formData.get('title');
            if (!title || !editorData) {
                alert('Please fill in all required fields (Title and Isi).');
                isSubmitting = false;
                return;
            }

            // Determine the mode (add or edit) based on submit button text
            const mode = submitButton.textContent.trim() === 'Update' ? 'edit' : 'add';
            formData.append('mode', mode);

            // If it's in 'edit' mode, add the id_isi to the form data
            if (mode === 'edit') {
                formData.append('id_isi', id_isi); // Include id_isi in the formData
            }

            console.log("FormData being sent:");
            formData.forEach((value, key) => {
                console.log(`${key}:`, value instanceof File ? value.name : value);
            });

            const addUrl = base_url + "index.php/MasterIsi/process_form";
            fetch(addUrl, {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        // location.reload();
                    } else {
                        alert(data.message || 'Error submitting the form');
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error submitting the form");
                })
                .finally(() => {
                    isSubmitting = false;
                });
        }

        // Attach the form submit handler
        form.removeEventListener('submit', handleFormSubmit); // Remove any existing listeners
        form.addEventListener('submit', handleFormSubmit);

        // Preview button logic for both add and edit modes
        document.getElementById("previewButton").addEventListener("click", function () {
            const formElement = document.getElementById("TemplateMaster");

            const formData = new FormData(formElement);

            const editorData = CKEDITOR.instances['Isi'].getData();
            formData.set('Isi', editorData);

            // Append mode based on the current state
            const mode = submitButton.textContent.trim() === 'Update' ? 'edit' : 'add';
            formData.append('mode', mode);

            console.log("Data being sent to the server:");
            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
            });

            fetch(base_url + "index.php/MasterIsi/generate_pdf_preview", {
                method: "POST",
                body: formData,
            })
            .then(response => {
                if (!response.ok) throw new Error("Failed to generate preview");
                return response.blob();
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                window.open(url);
            })
            .catch(error => console.error("Error generating preview:", error));
        });
    });

    const isDetailsPage = window.location.pathname.includes("detailsPage");

    const submitButton = document.getElementById('submitButton');

    if (isDetailsPage) {
        submitButton.textContent = 'Go Back';
        submitButton.classList.remove('btn-primary');
        submitButton.classList.add('btn-secondary');

        submitButton.addEventListener('click', function() {
            window.history.back();
        });
    } else {
        submitButton.textContent = 'Submit';
        submitButton.addEventListener('click', function() {
            const form = document.getElementById('TemplateMaster');
            form.dispatchEvent(new Event('submit'));
        });
    }

    // Meload data tabel
    function loadTableData(searchQuery = '') {
        const url = base_url + `index.php/MasterIsi/load_data?page=${currentPage}&limit=${rowsPerPage}&search=${encodeURIComponent(searchQuery)}`;
        
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

    document.addEventListener("DOMContentLoaded", function () {
        CKEDITOR.replace('Isi');
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('toggle-btn')) {
                const button = e.target;
                const id = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');
                const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

                fetch(base_url + 'index.php/MasterIsi/update_status', {
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

    function toggleButtons(id, newStatus) {
        const button = document.querySelector(`button[data-id="${id}"]`);
        
        if (button) {
            button.textContent = newStatus;
            button.setAttribute('data-status', newStatus);
        }
    }
</script>
