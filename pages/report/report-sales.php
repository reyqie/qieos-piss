<?php
    include '../../sessions/session.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/report-sales.css?v=<?= filemtime(__DIR__ . '/../../css/pages/report-sales.css') ?>">
</head>

<body>
<?php include '../components/sidebar.php'; ?>
<main class="content">
    <?php include '../components/navbar.php'; ?>

    <div class="container-fluid px-0 mt-5">

        <div id="mainContent" class="row mb-4 report-sales-card">

            <!-- Sidebar -->
            <div class="col-lg-3">

                <div class="section-card">

                    <div class="panel-header panel-primary mb-4">
                        <div class="panel-left">
                            <div class="panel-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>

                            <div>
                                <div class="panel-title">
                                    Jenis Laporan
                                </div>
                                <div class="panel-subtitle">
                                    Pilih laporan yang ingin ditampilkan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="report-menu" id="reportMenu" data-active="keuangan">

                        <div class="report-menu-toggle" role="tablist" aria-label="Jenis Laporan">
                            <span class="report-menu-toggle-pill" aria-hidden="true"></span>

                            <button type="button" class="report-menu-toggle-btn active"
                                data-group="keuangan" role="tab" aria-selected="true"
                                onclick="switchReportGroup('keuangan', this)">
                                <i class="fas fa-wallet"></i>
                                <span>Keuangan</span>
                            </button>

                            <button type="button" class="report-menu-toggle-btn"
                                data-group="penjualan" role="tab" aria-selected="false"
                                onclick="switchReportGroup('penjualan', this)">
                                <i class="fas fa-receipt"></i>
                                <span>Penjualan</span>
                            </button>
                        </div>

                        <button class="report-menu-item active"
                            data-group="keuangan" data-tab="summary"
                            onclick="switchReportTab('summary', this)">
                            <i class="fas fa-wallet"></i>

                            <div>
                                <h6>Ringkasan Keuangan</h6>
                                <small>Omzet, pengeluaran, dan laba</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="keuangan" data-tab="omzet"
                            onclick="switchReportTab('omzet', this)">
                            <i class="fas fa-coins"></i>

                            <div>
                                <h6>Omzet Harian</h6>
                                <small>Pendapatan penjualan per hari</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="keuangan" data-tab="expense"
                            onclick="switchReportTab('expense', this)">
                            <i class="fas fa-cart-shopping"></i>

                            <div>
                                <h6>Pengeluaran</h6>
                                <small>Biaya belanja stok</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="keuangan" data-tab="profit"
                            onclick="switchReportTab('profit', this)">
                            <i class="fas fa-chart-pie"></i>

                            <div>
                                <h6>Laba & Rugi</h6>
                                <small>Omzet dikurangi pengeluaran</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="keuangan" data-tab="margin"
                            onclick="switchReportTab('margin', this)">
                            <i class="fas fa-percent"></i>

                            <div>
                                <h6>Margin Produk</h6>
                                <small>Keuntungan harga jual vs beli</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="all"
                            onclick="switchReportTab('all', this)">
                            <i class="fas fa-receipt"></i>

                            <div>
                                <h6>Semua Transaksi</h6>
                                <small>Seluruh pesanan penjualan</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="product"
                            onclick="switchReportTab('product', this)">
                            <i class="fas fa-box"></i>

                            <div>
                                <h6>Per Produk</h6>
                                <small>Laporan berdasarkan produk</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="category"
                            onclick="switchReportTab('category', this)">
                            <i class="fas fa-tags"></i>

                            <div>
                                <h6>Per Kategori</h6>
                                <small>Omzet makanan, minuman, jajanan</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="cashier"
                            onclick="switchReportTab('cashier', this)">
                            <i class="fas fa-cash-register"></i>

                            <div>
                                <h6>Per Kasir</h6>
                                <small>Performa kasir yang bertugas</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="best"
                            onclick="switchReportTab('best', this)">
                            <i class="fas fa-trophy"></i>

                            <div>
                                <h6>Produk Terlaris</h6>
                                <small>Peringkat penjualan terbanyak</small>
                            </div>
                        </button>

                        <button class="report-menu-item"
                            data-group="penjualan" data-tab="unpaid"
                            onclick="switchReportTab('unpaid', this)">
                            <i class="fas fa-file-invoice-dollar"></i>

                            <div>
                                <h6>Piutang</h6>
                                <small>Nota yang belum terbayar</small>
                            </div>
                        </button>

                    </div>

                </div>

            </div>


            <!-- Content -->
            <div class="col-lg-9">
                <?php include 'sales/content-summary.php' ?>
                <?php include 'sales/content-omzet.php' ?>
                <?php include 'sales/content-expense.php' ?>
                <?php include 'sales/content-profit.php' ?>
                <?php include 'sales/content-margin.php' ?>
                <?php include 'sales/content-all.php' ?>
                <?php include 'sales/content-product.php' ?>
                <?php include 'sales/content-category.php' ?>
                <?php include 'sales/content-cashier.php' ?>
                <?php include 'sales/content-bestseller.php' ?>
                <?php include 'sales/content-unpaid.php' ?>
            </div>

        </div>
    </div>
</main>

<?php include '../../script/footscript.php'; ?>

<script>
    let currentSubTab = "summary";

    const reportTabs = {
        summary:  { body: "#reportSummaryBody",  card: "#reportSummarySummaryCard",  label: "#totalSummaryAmountLabel",  url: "sales/report-summary.php",  colspan: 4, icon: "fa-wallet",          empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan ringkasan keuangan." },
        omzet:    { body: "#reportOmzetBody",    card: "#reportOmzetSummaryCard",    label: "#totalOmzetAmountLabel",    url: "sales/report-omzet.php",    colspan: 6, icon: "fa-coins",           empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan omzet harian." },
        expense:  { body: "#reportExpenseBody",  card: "#reportExpenseSummaryCard",  label: "#totalExpenseAmountLabel",  url: "sales/report-expense.php",  colspan: 5, icon: "fa-cart-shopping",   empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan pengeluaran belanja." },
        profit:   { body: "#reportProfitBody",   card: "#reportProfitSummaryCard",   label: "#totalProfitAmountLabel",   url: "sales/report-profit.php",   colspan: 6, icon: "fa-chart-pie",       empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan laba dan rugi." },
        margin:   { body: "#reportMarginBody",   card: "#reportMarginSummaryCard",   label: "#totalMarginAmountLabel",   url: "sales/report-margin.php",   colspan: 7, icon: "fa-percent",         empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan margin produk." },
        all:      { body: "#reportAllBody",      card: "#reportAllSummaryCard",      label: "#totalAllAmountLabel",      url: "sales/report-all.php",      colspan: 6, icon: "fa-receipt",         empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan laporan transaksi." },
        product:  { body: "#reportProductBody",  card: "#reportProductSummaryCard",  label: "#totalProductAmountLabel",  url: "sales/report-product.php",  colspan: 6, icon: "fa-box",             empty: "Pilih produk terlebih dahulu untuk melihat laporan penjualan." },
        category: { body: "#reportCategoryBody", card: "#reportCategorySummaryCard", label: "#totalCategoryAmountLabel", url: "sales/report-category.php", colspan: 5, icon: "fa-tags",            empty: "Pilih kategori terlebih dahulu untuk melihat laporan penjualan." },
        cashier:  { body: "#reportCashierBody",  card: "#reportCashierSummaryCard",  label: "#totalCashierAmountLabel",  url: "sales/report-cashier.php",  colspan: 5, icon: "fa-cash-register",   empty: "Pilih kasir terlebih dahulu untuk melihat laporan penjualan." },
        best:     { body: "#reportBestBody",     card: "#reportBestSummaryCard",     label: "#totalBestAmountLabel",     url: "sales/report-best.php",     colspan: 5, icon: "fa-trophy",          empty: "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan produk terlaris." },
        unpaid:   { body: "#reportUnpaidBody",   card: "#reportUnpaidSummaryCard",   label: "#totalUnpaidAmountLabel",   url: "sales/report-unpaid.php",   colspan: 6, icon: "fa-file-invoice-dollar", empty: "Tidak ada piutang." }
    };

    const panelMap = {
        summary: "#summaryContent",
        omzet: "#omzetContent",
        expense: "#expenseContent",
        profit: "#profitContent",
        margin: "#marginContent",
        all: "#allContent",
        product: "#productContent",
        category: "#categoryContent",
        cashier: "#cashierContent",
        best: "#bestContent",
        unpaid: "#unpaidContent"
    };

    function emptyHtml(cfg, message){
        return `
            <tr>
                <td colspan="${cfg.colspan}">
                    <div class="loading-box">
                        <i class="fas ${cfg.icon}"></i>
                        <h5>Belum Ada Data</h5>
                        <span>${message}</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function applyFoot(cfg, footText){
        if (cfg === reportTabs.summary) {
            try {
                let data = JSON.parse(footText.trim());
                $("#summaryOmzetLabel").text(data.omzet || "Rp 0");
                $("#summaryExpenseLabel").text(data.expense || "Rp 0");
                $("#summaryProfitLabel").text(data.profit || "Rp 0");
                $(cfg.label).text(data.profit || "Rp 0");
                $(cfg.card).show();
                return;
            } catch (e) {
                $(cfg.card).hide();
                return;
            }
        }

        if (cfg === reportTabs.best || cfg === reportTabs.category || cfg === reportTabs.product) {
            try {
                let parsed = JSON.parse(footText || "{}");
                let qLabel = cfg === reportTabs.best ? "#totalBestQtyLabel"
                    : cfg === reportTabs.category ? "#totalCategoryQtyLabel"
                    : "#totalProductQtyLabel";
                let aLabel = cfg === reportTabs.best ? "#totalBestAmountLabel"
                    : cfg === reportTabs.category ? "#totalCategoryAmountLabel"
                    : "#totalProductAmountLabel";
                $(qLabel).text((parsed.qty || "0") + " Unit");
                $(aLabel).text(parsed.omzet || "Rp 0");
                $(cfg.card).show();
            } catch(e) {
                $(cfg.card).hide();
            }
            return;
        }

        let match = (footText || "").match(/Rp\s*[\d\.\,-]+/i);
        if (match) {
            $(cfg.label).text(match[0]);
            $(cfg.card).show();
        } else {
            $(cfg.card).hide();
        }
    }

    function loadReport(tab){
        let cfg = reportTabs[tab];
        if (!cfg) return;

        let first = $("#first_date_" + tab).val();
        let last  = $("#last_date_" + tab).val();
        let params = { first_date: first, last_date: last };

        if (tab === "product") {
            let id = $("#product_id").val();
            if (!id) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Pilih produk terlebih dahulu untuk melihat laporan penjualan."));
                return;
            }
            if (!first || !last) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan laporan."));
                return;
            }
            params.product_id = id;
        } else if (tab === "category") {
            let category = $("#category_id").val();
            if (!category) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Pilih kategori terlebih dahulu untuk melihat laporan penjualan."));
                return;
            }
            if (!first || !last) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan laporan."));
                return;
            }
            params.category = category;
        } else if (tab === "cashier") {
            let id = $("#cashier_id").val();
            if (!id) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Pilih kasir terlebih dahulu untuk melihat laporan penjualan."));
                return;
            }
            if (!first || !last) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, "Silakan pilih tanggal awal dan tanggal akhir untuk menampilkan laporan."));
                return;
            }
            params.cashier_id = id;
        } else if (tab === "best") {
            if (!first || !last) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, cfg.empty));
                return;
            }
            params.limit = $("#best_limit").val() || 10;
        } else {
            if (!first || !last) {
                $(cfg.card).hide();
                $(cfg.body).html(emptyHtml(cfg, cfg.empty));
                if (tab === "summary") {
                    $("#summaryOmzetLabel, #summaryExpenseLabel, #summaryProfitLabel").text("Rp 0");
                }
                return;
            }
        }

        $.get(cfg.url, params, function(res){
            let parts = res.split("<!--SPLIT_FOOT-->");
            $(cfg.body).html(parts[0]);
            applyFoot(cfg, parts[1] || "");
        });
    }

    function setReportGroup(group){
        if (!group) return;

        $("#reportMenu").attr("data-active", group);

        $(".report-menu-toggle-btn").removeClass("active").attr("aria-selected", "false");
        $('.report-menu-toggle-btn[data-group="' + group + '"]').addClass("active").attr("aria-selected", "true");
    }

    function switchReportGroup(group, el){
        setReportGroup(group);

        // Jika laporan yang sedang aktif bukan bagian grup ini,
        // otomatis pindah ke laporan pertama grup tersebut.
        let active = $(".report-menu-item.active");
        if (!active.length || active.data("group") !== group) {
            let first = $('.report-menu-item[data-group="' + group + '"]').first();
            if (first.length) {
                switchReportTab(first.data("tab"), first[0]);
            }
        }
    }

    function switchReportTab(tab, el){
        currentSubTab = tab;

        $(".report-menu-item").removeClass("active");
        $(el).addClass("active");

        setReportGroup($(el).data("group"));

        $("#summaryContent, #omzetContent, #expenseContent, #profitContent, #marginContent, #allContent, #productContent, #categoryContent, #cashierContent, #bestContent, #unpaidContent").hide();
        $(panelMap[tab] || "#allContent").show();

        loadReport(tab);
    }

    $(function(){
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, "0");
        const d = String(now.getDate()).padStart(2, "0");
        const firstDay = `${y}-${m}-01`;
        const today = `${y}-${m}-${d}`;

        $("input[type='date']").each(function(){
            if (!$(this).val()) {
                if (this.id.startsWith("first_date_")) {
                    $(this).val(firstDay);
                } else if (this.id.startsWith("last_date_")) {
                    $(this).val(today);
                }
            }
        });

        $("#first_date_summary, #last_date_summary").on("change", function(){ loadReport("summary"); });
        $("#first_date_omzet, #last_date_omzet").on("change", function(){ loadReport("omzet"); });
        $("#first_date_expense, #last_date_expense").on("change", function(){ loadReport("expense"); });
        $("#first_date_profit, #last_date_profit").on("change", function(){ loadReport("profit"); });
        $("#first_date_margin, #last_date_margin").on("change", function(){ loadReport("margin"); });
        $("#first_date_all, #last_date_all").on("change", function(){ loadReport("all"); });
        $("#product_id, #first_date_product, #last_date_product").on("change", function(){ loadReport("product"); });
        $("#category_id, #first_date_category, #last_date_category").on("change", function(){ loadReport("category"); });
        $("#cashier_id, #first_date_cashier, #last_date_cashier").on("change", function(){ loadReport("cashier"); });
        $("#first_date_best, #last_date_best, #best_limit").on("change", function(){ loadReport("best"); });
        $("#first_date_unpaid, #last_date_unpaid").on("change", function(){ loadReport("unpaid"); });

        let activeItem = $(".report-menu-item.active").first();
        let defaultSub = activeItem.data("tab") || "all";
        switchReportTab(defaultSub, activeItem[0] || $(".report-menu-item")[0]);
    });

    function printPDF(tab){
        let url = "sales/print-pdf.php?tab=" + tab;
        url += "&first_date=" + ($("#first_date_" + tab).val() || "");
        url += "&last_date=" + ($("#last_date_" + tab).val() || "");

        if (tab === "product") url += "&product_id=" + ($("#product_id").val() || "");
        if (tab === "category") url += "&category=" + ($("#category_id").val() || "");
        if (tab === "cashier") url += "&cashier_id=" + ($("#cashier_id").val() || "");
        if (tab === "best") url += "&limit=" + ($("#best_limit").val() || 10);

        window.open(url, "_blank");
    }

    function printExcel(tab){
        let url = "sales/export-excel.php?tab=" + tab;
        url += "&first_date=" + ($("#first_date_" + tab).val() || "");
        url += "&last_date=" + ($("#last_date_" + tab).val() || "");

        if (tab === "product") url += "&product_id=" + ($("#product_id").val() || "");
        if (tab === "category") url += "&category=" + ($("#category_id").val() || "");
        if (tab === "cashier") url += "&cashier_id=" + ($("#cashier_id").val() || "");
        if (tab === "best") url += "&limit=" + ($("#best_limit").val() || 10);

        if ($("#" + tab + "_detail").is(":checked")) url += "&detail=1";

        window.open(url, "_self");
    }

    // Mode Detail (khusus tab Per Kategori, Ringkasan Keuangan, Produk Terlaris):
    // hanya Excel yang tersedia, PDF disembunyikan, teks "Export Excel" muncul.
    function toggleReportDetail(tab){
        let checked = $("#" + tab + "_detail").is(":checked");
        $("#" + tab + "PdfBtn").toggle(!checked);
        $("#" + tab + "ExcelBtn").toggleClass("is-detail", checked);
    }
</script>

</body>
</html>
