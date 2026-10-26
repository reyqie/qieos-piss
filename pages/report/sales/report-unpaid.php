<?php
error_reporting(0);
include __DIR__ . '/../../../sessions/session.php';

$first = isset($_GET['first_date']) ? $_GET['first_date'] : '';
$last  = isset($_GET['last_date']) ? $_GET['last_date'] : '';

$where = "o.status_payment = 'waiting'";
if($first != '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $first)){
    $where .= " AND o.tanggal >= '$first'";
}
if($last != '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $last)){
    $where .= " AND o.tanggal <= '$last'";
}

$q = mysqli_query($conn, "
    SELECT o.id, o.code, o.tanggal, o.total, u.fullname AS kasir
    FROM orders o
    LEFT JOIN users u ON u.id = o.staff_id
    WHERE $where
    ORDER BY o.tanggal DESC, o.id DESC
");

$data = [];
$totalPiutang = 0;
if($q){
    while($row = mysqli_fetch_assoc($q)){
        $data[] = $row;
        $totalPiutang += (int)$row['total'];
    }
}

$bulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
function rp($n){ return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function formatDate($ts, $bulan){
    if(!$ts) return '-';
    $t = strtotime($ts);
    return date('d', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

if(empty($data)){
    echo '<tr><td colspan="6"><div class="loading-box"><i class="fas fa-smile"></i><h5>Tidak Ada Piutang</h5><span>Semua nota sudah terbayar.</span></div></td></tr>';
    echo '<!--SPLIT_FOOT-->';
    exit;
}

$no = 1;
foreach($data as $d){
    echo '<tr>';
    echo '<td class="text-center">' . $no++ . '</td>';
    echo '<td class="text-center"><b>' . htmlspecialchars($d['code']) . '</b></td>';
    echo '<td class="text-center">' . formatDate($d['tanggal'], $bulan) . '</td>';
    echo '<td class="text-center">' . htmlspecialchars($d['kasir'] ?: '-') . '</td>';
    echo '<td class="text-center"><b>' . rp($d['total']) . '</b></td>';
    echo '<td class="text-center"><span class="badge bg-warning text-dark">Belum Bayar</span></td>';
    echo '</tr>';
}

echo '<!--SPLIT_FOOT-->' . rp($totalPiutang);
