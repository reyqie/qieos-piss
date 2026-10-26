<?php

include '../../../sessions/session.php';
require '../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function sPdfDateId($date)
{
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $t = strtotime($date);
    if ($t === false) {
        return '-';
    }
    return date('d', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function sPdfEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sPdfRp($amount)
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

$tab        = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$first_date = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last_date  = isset($_GET['last_date']) ? $_GET['last_date'] : '';
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
$category   = isset($_GET['category']) ? $_GET['category'] : '';
$cashier_id = isset($_GET['cashier_id']) ? (int) $_GET['cashier_id'] : 0;
$limit      = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

$escFirst = mysqli_real_escape_string($conn, $first_date);
$escLast  = mysqli_real_escape_string($conn, $last_date);

$tabLabels = [
    'summary'  => 'RINGKASAN KEUANGAN',
    'omzet'    => 'LAPORAN OMZET HARIAN',
    'expense'  => 'LAPORAN PENGELUARAN',
    'profit'   => 'LAPORAN LABA & RUGI',
    'margin'   => 'LAPORAN MARGIN PRODUK',
    'all'      => 'LAPORAN SEMUA TRANSAKSI',
    'product'  => 'LAPORAN PENJUALAN PER PRODUK',
    'category' => 'LAPORAN PENJUALAN PER KATEGORI',
    'cashier'  => 'LAPORAN PENJUALAN PER KASIR',
    'best'     => 'LAPORAN PRODUK TERLARIS',
    'unpaid'   => 'LAPORAN PIUTANG'
];

$tabTitles = [
    'summary'  => 'Ringkasan Keuangan',
    'omzet'    => 'Laporan Omzet Harian',
    'expense'  => 'Laporan Pengeluaran',
    'profit'   => 'Laporan Laba & Rugi',
    'margin'   => 'Laporan Margin Produk',
    'all'      => 'Laporan Semua Transaksi',
    'product'  => 'Laporan Penjualan Per Produk',
    'category' => 'Laporan Penjualan Per Kategori',
    'cashier'  => 'Laporan Penjualan Per Kasir',
    'best'     => 'Laporan Produk Terlaris',
    'unpaid'   => 'Laporan Piutang'
];

$title  = isset($tabLabels[$tab]) ? $tabLabels[$tab] : 'LAPORAN PENJUALAN';
$title2 = isset($tabTitles[$tab]) ? $tabTitles[$tab] : 'Laporan Penjualan';

$periodLabel = ($first_date !== '' && $last_date !== '')
    ? sPdfDateId($first_date) . '  —  ' . sPdfDateId($last_date)
    : 'Semua periode';

$printedAt  = sPdfDateId(date('Y-m-d')) . ' · ' . date('H:i') . ' WIB';
$printedBy  = isset($user['fullname']) && $user['fullname'] !== '' ? $user['fullname'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'QIEOS');
$printedRole = isset($user['role']) && $user['role'] !== '' ? ucwords((string) $user['role']) : 'Staff';

$cashierName = '';
if ($tab === 'cashier' && $cashier_id > 0) {
    $qCashierName = mysqli_query($conn, "SELECT COALESCE(NULLIF(fullname,''), username, '-') AS name FROM users WHERE id = $cashier_id LIMIT 1");
    if ($qCashierName && $cashierRow = mysqli_fetch_assoc($qCashierName)) {
        $cashierName = $cashierRow['name'];
    }
}

$productName = '';
if ($tab === 'product' && $product_id > 0) {
    $qProductName = mysqli_query($conn, "SELECT COALESCE(name, '-') AS name FROM products WHERE id = $product_id LIMIT 1");
    if ($qProductName && $productRow = mysqli_fetch_assoc($qProductName)) {
        $productName = $productRow['name'];
    }
}

$categoryName = '';
if ($tab === 'category' && $category !== '') {
    $categoryName = (strtolower($category) === 'all') ? 'Semua Kategori' : ucfirst($category);
}

// ============================
// QUERY DATA PER TAB
// ============================
$rows  = [];
$total = 0;
$extra = [];
$where = "DATE(tanggal) BETWEEN '$escFirst' AND '$escLast'";

switch ($tab) {

    case 'summary':
        $omzet = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS v FROM orders WHERE status_payment='paid' AND $where"))['v'];
        $expense = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(pi.price_buy),0) AS v FROM purchase_items pi JOIN purchases p ON pi.purchase_id=p.id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast'"))['v'];
        $orderCount = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS v FROM orders WHERE status_payment='paid' AND $where"))['v'];
        $profit = $omzet - $expense;
        $total = $profit;
        $extra = compact('omzet', 'expense', 'orderCount', 'profit');
        break;

    case 'omzet':
        $q = mysqli_query($conn, "SELECT DATE(tanggal) AS dt, COUNT(*) AS total_order, SUM(CASE WHEN status_payment='paid' THEN 1 ELSE 0 END) AS paid_count, SUM(CASE WHEN status_payment='waiting' THEN 1 ELSE 0 END) AS waiting_count, SUM(CASE WHEN status_payment='paid' THEN total ELSE 0 END) AS omzet FROM orders WHERE $where GROUP BY DATE(tanggal) ORDER BY dt DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['omzet']; }
        break;

    case 'expense':
        $q = mysqli_query($conn, "SELECT p.id, p.form, p.date, COUNT(pi.id) AS total_items, COALESCE(SUM(pi.price_buy),0) AS total_price FROM purchases p LEFT JOIN purchase_items pi ON p.id=pi.purchase_id AND pi.deleted_at IS NULL WHERE p.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' AND EXISTS (SELECT 1 FROM purchase_items pi2 WHERE pi2.purchase_id=p.id AND pi2.deleted_at IS NULL AND pi2.qty_buy IS NOT NULL) GROUP BY p.id, p.form, p.date ORDER BY p.date DESC, p.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $rows[] = $r; $total += (float) $r['total_price']; }
        break;

    case 'profit':
        $q = mysqli_query($conn, "SELECT d.dt, COALESCE(o.omzet,0) AS omzet, COALESCE(e.expense,0) AS expense FROM (SELECT DATE(tanggal) AS dt FROM orders WHERE $where UNION SELECT DATE(date) AS dt FROM purchases WHERE deleted_at IS NULL AND DATE(date) BETWEEN '$escFirst' AND '$escLast') d LEFT JOIN (SELECT DATE(tanggal) AS dt, COALESCE(SUM(total),0) AS omzet FROM orders WHERE status_payment='paid' AND $where GROUP BY DATE(tanggal)) o ON o.dt=d.dt LEFT JOIN (SELECT DATE(p.date) AS dt, COALESCE(SUM(pi.price_buy),0) AS expense FROM purchases p JOIN purchase_items pi ON p.id=pi.purchase_id WHERE p.deleted_at IS NULL AND pi.deleted_at IS NULL AND DATE(p.date) BETWEEN '$escFirst' AND '$escLast' GROUP BY DATE(p.date)) e ON e.dt=d.dt ORDER BY d.dt DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $r['profit'] = (float)$r['omzet'] - (float)$r['expense']; $rows[] = $r; $total += $r['profit']; }
        break;

    case 'margin':
        $q = mysqli_query($conn, "SELECT p.name, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(AVG(od.price),0) AS sell_price, COALESCE((SELECT AVG(pi.price_unit) FROM purchase_items pi WHERE pi.product_id=p.id AND pi.deleted_at IS NULL AND pi.price_unit>0),0) AS price, COALESCE(SUM(od.subtotal),0) AS revenue FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name ORDER BY revenue DESC, p.name ASC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $qty=(int)$r['qty_sold']; $sell=(float)$r['sell_price']; $buy=(float)$r['price']; $r['margin_pct']=$sell>0?(($sell-$buy)/$sell)*100:0; $r['profit']=($sell-$buy)*$qty; $rows[]=$r; $total+=$r['profit']; }
        break;

    case 'all':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment, COALESCE(NULLIF(u.fullname,''),u.username,'-') AS cashier_name FROM orders o LEFT JOIN users u ON o.staff_id=u.id WHERE $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'product':
        $escProduct = (int) $product_id;
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, od.qty, od.price, od.subtotal FROM order_details od JOIN orders o ON od.order_id=o.id WHERE od.product_id=$escProduct AND o.status_payment='paid' AND $where ORDER BY o.tanggal DESC, o.id DESC");
        $totalQty = 0;
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $totalQty+=(int)$r['qty']; $total+=(float)$r['subtotal']; $rows[]=$r; }
        $extra['qty'] = $totalQty;
        break;

    case 'category':
        $escCat = mysqli_real_escape_string($conn, $category);
        $catFilter = ($category !== '' && strtolower($category) !== 'all') ? "AND p.category='$escCat'" : '';
        $q = mysqli_query($conn, "SELECT p.name, p.code, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where $catFilter GROUP BY p.id, p.name, p.code ORDER BY omzet DESC, p.name ASC");
        $totalQty = 0;
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $totalQty+=(int)$r['qty_sold']; $total+=(float)$r['omzet']; $rows[]=$r; }
        $extra['qty'] = $totalQty;
        break;

    case 'cashier':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, o.status_payment FROM orders o WHERE o.staff_id=$cashier_id AND $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { if ($r['status_payment']==='paid') $total+=(float)$r['total']; $rows[]=$r; }
        break;

    case 'best':
        if ($limit <= 0) $limit = 10;
        $q = mysqli_query($conn, "SELECT p.name, p.category, COALESCE(SUM(od.qty),0) AS qty_sold, COALESCE(SUM(od.subtotal),0) AS omzet FROM order_details od JOIN orders o ON od.order_id=o.id JOIN products p ON od.product_id=p.id WHERE o.status_payment='paid' AND $where GROUP BY p.id, p.name, p.category ORDER BY qty_sold DESC, omzet DESC LIMIT $limit");
        $totalQty = 0;
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $totalQty+=(int)$r['qty_sold']; $total+=(float)$r['omzet']; $rows[]=$r; }
        $extra['qty'] = $totalQty;
        break;

    case 'unpaid':
        $q = mysqli_query($conn, "SELECT o.code, o.tanggal, o.total, COALESCE(NULLIF(u.fullname,''),u.username,'-') AS cashier_name FROM orders o LEFT JOIN users u ON o.staff_id=u.id WHERE o.status_payment='waiting' AND $where ORDER BY o.tanggal DESC, o.id DESC");
        if ($q) while ($r = mysqli_fetch_assoc($q)) { $total+=(float)$r['total']; $rows[]=$r; }
        break;
}

$count = ($tab === 'summary') ? 3 : count($rows);

// ============================
// BUILD HTML
// ============================
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= sPdfEscape($title2) ?> - Qieos</title>
    <style>
        @page { margin: 22px 48px 50px 36px; }
        * { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #0f172a; line-height: 1.45; }
        .wrap { width: 100%; }
        .brand-table { width: 100%; border-collapse: collapse; }
        .brand-cell { background: #1e1b4b; padding: 18px 20px 16px 20px; }
        .co-name { color: #ffffff; font-size: 14px; font-weight: bold; letter-spacing: 0.6px; }
        .co-sub { color: #c7d2fe; font-size: 8.5px; margin-top: 3px; letter-spacing: 0.2px; }
        .gold-bar { background: #c4a35a; height: 4px; font-size: 0; line-height: 0; }
        .title-block { padding: 16px 12px 12px 14px; }
        .report-kicker { font-size: 8px; font-weight: bold; letter-spacing: 1.6px; color: #6366f1; text-transform: uppercase; }
        .report-title { font-size: 16px; font-weight: bold; color: #1e1b4b; margin-top: 3px; letter-spacing: 0.3px; }
        .report-period { font-size: 10px; color: #64748b; margin-top: 4px; }
        .meta-table { width: 100%; border-collapse: collapse; margin: 4px 0 14px 0; }
        .meta-table td { width: 33.33%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 9px 12px; vertical-align: top; }
        .meta-label { font-size: 7.5px; font-weight: bold; letter-spacing: 1px; color: #94a3b8; text-transform: uppercase; }
        .meta-value { font-size: 10.5px; font-weight: bold; color: #1e1b4b; margin-top: 2px; }
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kpi-table td { width: 33.33%; padding: 11px 12px; border: 1px solid #e2e8f0; vertical-align: top; }
        .kpi-1 { background: #eef2ff; }
        .kpi-2 { background: #fef2f2; }
        .kpi-3 { background: #ecfdf5; }
        .kpi-label { font-size: 7.5px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #64748b; }
        .kpi-value { font-size: 15px; font-weight: bold; color: #1e1b4b; margin-top: 3px; }
        .kpi-hint { font-size: 8px; color: #64748b; margin-top: 2px; }
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table thead { display: table-header-group; }
        .data-table th { background: #1e1b4b; color: #ffffff; font-size: 8px; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; padding: 8px 10px; border: 1px solid #1e1b4b; text-align: center; overflow: hidden; }
        .data-table td { padding: 7px 10px; border: 1px solid #e2e8f0; font-size: 9.5px; color: #1e293b; vertical-align: middle; overflow: hidden; }
        .data-table th:last-child,
        .data-table td:last-child { padding-right: 14px; }
        .w5  { width: 5%; }
        .w8  { width: 8%; }
        .w10 { width: 10%; }
        .w12 { width: 12%; }
        .w15 { width: 15%; }
        .w18 { width: 18%; }
        .w20 { width: 20%; }
        .w22 { width: 22%; }
        .w25 { width: 25%; }
        .w28 { width: 28%; }
        .w30 { width: 30%; }
        .w35 { width: 35%; }
        .w40 { width: 40%; }
        .row-a { background: #ffffff; }
        .row-b { background: #f8fafc; }
        .c { text-align: center; }
        .r { text-align: right; }
        .l { text-align: left; }
        .badge { display: block; width: 72px; margin: 0 auto; font-size: 8px; font-weight: bold; letter-spacing: 0.4px; padding: 3px 0; border-radius: 10px; text-align: center; }
        .badge-paid { background: #d1fae5; color: #047857; }
        .badge-waiting { background: #fef3c7; color: #b45309; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
        .badge-untung { background: #d1fae5; color: #047857; }
        .badge-rugi { background: #fee2e2; color: #b91c1c; }
        .empty-cell { padding: 28px 12px; text-align: center; color: #64748b; font-size: 11px; background: #f8fafc; }
        .total-row td { background: #1e1b4b; color: #ffffff; font-weight: bold; font-size: 10px; padding: 9px 8px; border: 1px solid #1e1b4b; }
        .total-amount { font-size: 12px; color: #c4a35a; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 28px; }
        .sign-box { width: 46%; text-align: center; vertical-align: top; font-size: 9px; color: #64748b; }
        .sign-title { font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
        .sign-line { margin-top: 48px; border-top: 1px solid #cbd5e1; padding-top: 6px; color: #1e1b4b; font-weight: bold; font-size: 9.5px; }
        .sign-role { font-size: 8px; color: #64748b; font-weight: 500; }
        .foot { margin-top: 18px; border-top: 1px solid #e2e8f0; padding: 8px 0 0 14px; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="brand-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="brand-cell">
                <div class="co-name">PT. SELARASGRIYA SARANA UTAMA</div>
                <div class="co-sub">Pasar Induk Surabaya Sidotopo &nbsp;·&nbsp; QIEOS POS Management System</div>
            </td>
        </tr>
        <tr><td class="gold-bar">&nbsp;</td></tr>
    </table>

    <div class="title-block">
        <div class="report-kicker">Laporan Penjualan</div>
        <div class="report-title"><?= sPdfEscape($title) ?></div>
        <div class="report-period">Periode <?= sPdfEscape($periodLabel) ?><?= $tab === 'cashier' && $cashierName !== '' ? '  ·  Kasir ' . sPdfEscape($cashierName) : '' ?><?= $tab === 'product' && $productName !== '' ? '  ·  Produk ' . sPdfEscape($productName) : '' ?><?= $tab === 'category' && $categoryName !== '' ? '  ·  Kategori ' . sPdfEscape($categoryName) : '' ?></div>
    </div>

    <table class="meta-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="meta-label">Jenis Laporan</div>
                <div class="meta-value"><?= sPdfEscape($title2) ?></div>
            </td>
            <td>
                <div class="meta-label"><?= $tab === 'cashier' && $cashierName !== '' ? 'Kasir' : ($tab === 'product' && $productName !== '' ? 'Produk' : ($tab === 'category' && $categoryName !== '' ? 'Kategori' : 'Cakupan')) ?></div>
                <div class="meta-value"><?= $tab === 'cashier' && $cashierName !== '' ? sPdfEscape($cashierName) : ($tab === 'product' && $productName !== '' ? sPdfEscape($productName) : ($tab === 'category' && $categoryName !== '' ? sPdfEscape($categoryName) : 'Periode ' . sPdfEscape($periodLabel))) ?></div>
            </td>
            <td>
                <div class="meta-label">Dicetak</div>
                <div class="meta-value"><?= sPdfEscape($printedAt) ?></div>
            </td>
        </tr>
    </table>

    <?php if ($tab === 'summary'): ?>
    <table class="kpi-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="kpi-1">
                <div class="kpi-label">Omzet Penjualan</div>
                <div class="kpi-value"><?= sPdfRp($extra['omzet']) ?></div>
                <div class="kpi-hint"><?= $extra['orderCount'] ?> pesanan terbayar</div>
            </td>
            <td class="kpi-2">
                <div class="kpi-label">Pengeluaran</div>
                <div class="kpi-value"><?= sPdfRp($extra['expense']) ?></div>
                <div class="kpi-hint">Dari daftar belanja stok</div>
            </td>
            <td class="kpi-3">
                <div class="kpi-label">Laba Bersih</div>
                <div class="kpi-value"><?= sPdfRp($extra['profit']) ?></div>
                <div class="kpi-hint"><?= $extra['profit'] >= 0 ? 'Untung' : 'Rugi' ?></div>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <table class="data-table" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <?php
                $colWidths = [
                    'summary'  => ['w5','w18','w40','w37'],
                    'omzet'    => ['w5','w20','w12','w12','w12','w39'],
                    'expense'  => ['w5','w18','w25','w15','w37'],
                    'profit'   => ['w5','w20','w18','w18','w20','w19'],
                    'margin'   => ['w5','w22','w12','w15','w15','w12','w19'],
                    'all'      => ['w5','w18','w17','w22','w20','w18'],
                    'product'  => ['w5','w18','w20','w10','w20','w27'],
                    'category' => ['w5','w25','w12','w15','w43'],
                    'cashier'  => ['w5','w20','w20','w27','w28'],
                    'best'     => ['w8','w28','w18','w16','w30'],
                    'unpaid'   => ['w5','w18','w17','w22','w20','w18']
                ];
                $colMap = [
                    'summary'  => ['No', 'Komponen', 'Keterangan', 'Jumlah'],
                    'omzet'    => ['No', 'Tanggal', 'Pesanan', 'Terbayar', 'Waiting', 'Omzet'],
                    'expense'  => ['No', 'Tanggal', 'Form Belanja', 'Total Item', 'Total Belanja'],
                    'profit'   => ['No', 'Tanggal', 'Omzet', 'Pengeluaran', 'Laba / Rugi', 'Status'],
                    'margin'   => ['No', 'Produk', 'Qty Terjual', 'Harga Beli', 'Harga Jual', 'Margin', 'Keuntungan'],
                    'all'      => ['No', 'Kode Pesanan', 'Tanggal', 'Kasir', 'Total', 'Status'],
                    'product'  => ['No', 'Tanggal', 'Kode Pesanan', 'Qty', 'Harga', 'Subtotal'],
                    'category' => ['No', 'Produk', 'Kode', 'Qty Terjual', 'Omzet'],
                    'cashier'  => ['No', 'Kode Pesanan', 'Tanggal', 'Total', 'Status'],
                    'best'     => ['Peringkat', 'Produk', 'Kategori', 'Qty Terjual', 'Omzet'],
                    'unpaid'   => ['No', 'Kode Pesanan', 'Tanggal', 'Kasir', 'Total', 'Status']
                ];
                $headers = isset($colMap[$tab]) ? $colMap[$tab] : $colMap['all'];
                $widths = isset($colWidths[$tab]) ? $colWidths[$tab] : $colWidths['all'];
                foreach ($headers as $idx => $h):
                ?>
                <th class="<?= isset($widths[$idx]) ? $widths[$idx] : 'w15' ?>"><?= sPdfEscape($h) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php if ($count === 0): ?>
            <tr>
                <td colspan="<?= count($headers) ?>" class="empty-cell">Tidak ada data pada periode yang dipilih.</td>
            </tr>
        <?php else: ?>
            <?php for ($i = 0; $i < $count; $i++):
                $r = isset($rows[$i]) ? $rows[$i] : null;
            ?>
            <tr class="<?= $i % 2 === 0 ? 'row-a' : 'row-b' ?>">
                <?php
                switch ($tab):

                    case 'summary':
                        $label = ['Omzet Penjualan', 'Pengeluaran Belanja', 'Laba Bersih'];
                        $note  = [$extra['orderCount'] . ' pesanan terbayar', 'Dari daftar belanja stok', 'Omzet dikurangi pengeluaran'];
                        $amt   = [$extra['omzet'], $extra['expense'], $extra['profit']];
                        $summaryData = [
                            ['label'=>$label[0], 'note'=>$note[0], 'amount'=>$amt[0]],
                            ['label'=>$label[1], 'note'=>$note[1], 'amount'=>$amt[1]],
                            ['label'=>$label[2], 'note'=>$note[2], 'amount'=>$amt[2]],
                        ];
                        if (isset($summaryData[$i])):
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="l w18"><b><?= sPdfEscape($summaryData[$i]['label']) ?></b></td>
                        <td class="l w40"><?= sPdfEscape($summaryData[$i]['note']) ?></td>
                        <td class="r w37"><?= sPdfRp($summaryData[$i]['amount']) ?></td>
                <?php
                        endif;
                        break;

                    case 'omzet':
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w20"><?= sPdfDateId($r['dt']) ?></td>
                        <td class="c w12"><?= (int) $r['total_order'] ?></td>
                        <td class="c w12"><?= (int) $r['paid_count'] ?></td>
                        <td class="c w12"><?= (int) $r['waiting_count'] ?></td>
                        <td class="r w39"><?= sPdfRp($r['omzet']) ?></td>
                <?php
                        break;

                    case 'expense':
                        $form = $r['form'];
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w18"><?= sPdfDateId($r['date']) ?></td>
                        <td class="l w25"><b><?= sPdfEscape($form) ?></b></td>
                        <td class="c w15"><?= (int) $r['total_items'] ?></td>
                        <td class="r w37"><?= sPdfRp($r['total_price']) ?></td>
                <?php
                        break;

                    case 'profit':
                        $isProfit = $r['profit'] >= 0;
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w20"><?= sPdfDateId($r['dt']) ?></td>
                        <td class="r w18"><?= sPdfRp($r['omzet']) ?></td>
                        <td class="r w18"><?= sPdfRp($r['expense']) ?></td>
                        <td class="r w20"><b><?= sPdfRp($r['profit']) ?></b></td>
                        <td class="c w19"><span class="badge <?= $isProfit ? 'badge-untung' : 'badge-rugi' ?>"><?= $isProfit ? 'Untung' : 'Rugi' ?></span></td>
                <?php
                        break;

                    case 'margin':
                        $qty = (int) $r['qty_sold'];
                        $sell = (float) $r['sell_price'];
                        $buy = (float) $r['price'];
                        $mPct = $sell > 0 ? number_format((($sell - $buy) / $sell) * 100, 1) : '0';
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="l w22"><b><?= sPdfEscape($r['name']) ?></b></td>
                        <td class="c w12"><?= $qty ?></td>
                        <td class="r w15"><?= sPdfRp($buy) ?></td>
                        <td class="r w15"><?= sPdfRp($sell) ?></td>
                        <td class="c w12"><?= $mPct ?>%</td>
                        <td class="r w19"><b><?= sPdfRp($r['profit']) ?></b></td>
                <?php
                        break;

                    case 'all':
                        $statusBadge = $r['status_payment'] === 'paid' ? 'badge-paid' : ($r['status_payment'] === 'waiting' ? 'badge-waiting' : 'badge-cancelled');
                        $statusLabel = $r['status_payment'] === 'paid' ? 'Terbayar' : ucfirst($r['status_payment']);
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w18"><b><?= sPdfEscape($r['code']) ?></b></td>
                        <td class="c w17"><?= sPdfDateId($r['tanggal']) ?></td>
                        <td class="l w22"><?= sPdfEscape($r['cashier_name']) ?></td>
                        <td class="r w20"><?= sPdfRp($r['total']) ?></td>
                        <td class="c w18"><span class="badge <?= $statusBadge ?>"><?= sPdfEscape($statusLabel) ?></span></td>
                <?php
                        break;

                    case 'product':
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w18"><?= sPdfDateId($r['tanggal']) ?></td>
                        <td class="c w20"><b><?= sPdfEscape($r['code']) ?></b></td>
                        <td class="c w10"><?= (int) $r['qty'] ?></td>
                        <td class="r w20"><?= sPdfRp($r['price']) ?></td>
                        <td class="r w27"><b><?= sPdfRp($r['subtotal']) ?></b></td>
                <?php
                        break;

                    case 'category':
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="l w25"><b><?= sPdfEscape($r['name']) ?></b></td>
                        <td class="c w12"><?= sPdfEscape($r['code']) ?></td>
                        <td class="c w15"><?= (int) $r['qty_sold'] ?></td>
                        <td class="r w43"><b><?= sPdfRp($r['omzet']) ?></b></td>
                <?php
                        break;

                    case 'cashier':
                        $statusBadge = $r['status_payment'] === 'paid' ? 'badge-paid' : ($r['status_payment'] === 'waiting' ? 'badge-waiting' : 'badge-cancelled');
                        $statusLabel = $r['status_payment'] === 'paid' ? 'Terbayar' : ucfirst($r['status_payment']);
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w20"><b><?= sPdfEscape($r['code']) ?></b></td>
                        <td class="c w20"><?= sPdfDateId($r['tanggal']) ?></td>
                        <td class="r w27"><?= sPdfRp($r['total']) ?></td>
                        <td class="c w28"><span class="badge <?= $statusBadge ?>"><?= sPdfEscape($statusLabel) ?></span></td>
                <?php
                        break;

                    case 'best':
                        $rankClass = '';
                        if ($i === 0) $rankClass = 'badge-paid';
                        elseif ($i === 1) $rankClass = 'badge-waiting';
                        elseif ($i === 2) $rankClass = 'badge-cancelled';
                ?>
                        <td class="c w8"><span class="badge <?= $rankClass ?>" style="min-width:32px;"><?= $i + 1 ?></span></td>
                        <td class="l w28"><b><?= sPdfEscape($r['name']) ?></b></td>
                        <td class="c w18"><?= sPdfEscape(ucfirst($r['category'])) ?></td>
                        <td class="c w16"><?= (int) $r['qty_sold'] ?></td>
                        <td class="r w30"><b><?= sPdfRp($r['omzet']) ?></b></td>
                <?php
                        break;

                    case 'unpaid':
                ?>
                        <td class="c w5"><?= $i + 1 ?></td>
                        <td class="c w18"><b><?= sPdfEscape($r['code']) ?></b></td>
                        <td class="c w17"><?= sPdfDateId($r['tanggal']) ?></td>
                        <td class="l w22"><?= sPdfEscape($r['cashier_name']) ?></td>
                        <td class="r w20"><?= sPdfRp($r['total']) ?></td>
                        <td class="c w18"><span class="badge badge-waiting">Belum Bayar</span></td>
                <?php
                        break;

                endswitch;
                ?>
            </tr>
            <?php endfor; ?>

            <tr class="total-row">
                <?php
                $totalCols = count($headers);
                $labelMap = [
                    'summary'  => 'LABA BERSIH',
                    'omzet'    => 'TOTAL OMZET',
                    'expense'  => 'TOTAL PENGELUARAN',
                    'profit'   => 'TOTAL LABA / RUGI',
                    'margin'   => 'TOTAL KEUNTUNGAN',
                    'all'      => 'TOTAL OMZET (TERBAYAR)',
                    'product'  => 'TOTAL',
                    'category' => 'TOTAL',
                    'cashier'  => 'TOTAL OMZET (TERBAYAR)',
                    'best'     => 'TOTAL',
                    'unpaid'   => 'TOTAL PIUTANG'
                ];
                $totalLabel = isset($labelMap[$tab]) ? $labelMap[$tab] : 'TOTAL';
                if ($tab === 'best' || $tab === 'category'):
                ?>
                <td colspan="3" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="c"><?= number_format(isset($extra['qty']) ? $extra['qty'] : 0, 0, ',', '.') ?></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <?php elseif ($tab === 'product'):
                ?>
                <td colspan="3" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="c"><?= number_format(isset($extra['qty']) ? $extra['qty'] : 0, 0, ',', '.') ?></td>
                <td></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <?php elseif ($tab === 'cashier'):
                ?>
                <td colspan="3" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <td></td>
                <?php elseif ($tab === 'all'):
                ?>
                <td colspan="4" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <td></td>
                <?php elseif ($tab === 'unpaid'):
                ?>
                <td colspan="4" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <td></td>
                <?php else: ?>
                <td colspan="<?= $totalCols - 1 ?>" class="r"><?= sPdfEscape($totalLabel) ?></td>
                <td class="r total-amount"><?= sPdfRp($total) ?></td>
                <?php endif; ?>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="sign-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="sign-box">
                <div class="sign-title">Dicetak Oleh</div>
                <div><?= sPdfEscape($printedAt) ?></div>
                <div class="sign-line"><?= sPdfEscape($printedBy) ?></div>
                <div class="sign-role"><?= sPdfEscape($printedRole) ?></div>
            </td>
            <td width="8%"></td>
            <td class="sign-box">
                <div class="sign-title">Mengetahui</div>
                <div>Surabaya, <?= sPdfDateId(date('Y-m-d')) ?></div>
                <div class="sign-line">________________</div>
                <div class="sign-role">Pimpinan / Penanggung Jawab</div>
            </td>
        </tr>
    </table>

    <div class="foot">
        Dokumen ini dicetak otomatis melalui QIEOS POS Management System · PT. Selarasgriya Sarana Utama · Bersifat resmi dan dapat digunakan sebagai arsip laporan.
    </div>

</div>
</body>
</html>
<?php

$html = ob_get_clean();

try {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $projectRoot = realpath(__DIR__ . '/../../..');
    if ($projectRoot) {
        $options->setChroot($projectRoot);
    }

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $canvas = $dompdf->getCanvas();
    $font   = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $canvas->page_text(430, 812, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 8, [0.58, 0.63, 0.72]);

    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = $title2 . ' - ' . ($first_date !== '' ? date('d M Y', strtotime($first_date)) : 'Semua') . ' s.d. ' . ($last_date !== '' ? date('d M Y', strtotime($last_date)) : 'Semua') . '.pdf';

    $dompdf->stream($filename, [
        'Attachment' => false
    ]);

} catch (Exception $e) {
    if (ob_get_length()) {
        ob_end_clean();
    }
    error_log('[QIEOS PDF Error] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    header('Content-Type: text/html; charset=utf-8');
    echo '<h3>Error Generating PDF</h3>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    exit;
}
