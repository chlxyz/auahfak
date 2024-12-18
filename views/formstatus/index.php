<section id="main">
    <section id="content">
        <div class="container">
            <div class="card">
                <div style="margin-bottom: 20px; padding-top: 20px; margin: 20px;">
                    <h2 style="margin: 0; font-size: 24px;">Status Approval</h2>
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
                            <th>Keterangan</th>
                            <th>Approval 1</th>
                            <th>Approval 2</th>
                            <th>Overall Status</th>
                            <!-- <th>Preview PDF</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history_data)): ?>
                            <?php echo $history_data; ?> <!-- Render data dari controller -->
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center;">No Request</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</section>
