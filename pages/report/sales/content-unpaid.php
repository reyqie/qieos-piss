<div id="unpaidContent" class="section-card report-panel">

    <div class="panel-header panel-primary mb-4">
        <div class="panel-left">
            <div class="panel-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>

            <div>
                <div class="panel-title">
                    Laporan Piutang
                </div>

                <div class="panel-subtitle">
                    Menampilkan nota yang belum dibayar (status waiting).
                </div>
            </div>
        </div>
    </div>

    <div class="report-filter">
        <div class="row">

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="first_date_unpaid">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="last_date_unpaid">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-success me-2 w-100" type="button" onclick="printExcel('unpaid')">
                    <i class="fas fa-file-excel me-2"></i>
                    Export Excel
                </button>

                <button class="btn btn-danger w-100" type="button" onclick="printPDF('unpaid')">
                    <i class="fas fa-file-pdf me-2"></i>
                    Print PDF
                </button>
            </div>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table align-middle" id="reportTableUnpaid">
            <thead>
                <tr>
                    <th class="text-center" width="60">No</th>
                    <th class="text-center">Kode Pesanan</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Kasir</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            <tbody id="reportUnpaidBody">
                <tr>
                    <td colspan="6">
                        <div class="loading-box">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <h5>Memuat Data</h5>
                            <span>
                                Sedang memuat daftar piutang...
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="reportUnpaidSummaryCard" class="mt-4" style="display:none;">
        <div class="d-flex align-items-center justify-content-between px-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-size:18px; color:#fb7185;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#fecdd3; font-weight:700;">Total Piutang</div>
                    <div style="font-size:12px; color:#94a3b8;">Jumlah nota yang belum dibayar</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fb7185; font-size: 18px; font-weight: 800; padding: 8px 22px; border-radius: 14px; letter-spacing: 0.5px;" id="totalUnpaidAmountLabel">
                Rp 0
            </div>
        </div>
    </div>

</div>
