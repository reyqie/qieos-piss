<?php
include '../../sessions/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$q = mysqli_query($conn, "
    SELECT 
        pi.qty_stock,
        pi.remaining_qty,
        p.date,
        pi.unit_stock,
        pi.price_unit,
        p.form
    FROM purchase_items pi
    LEFT JOIN purchases p ON p.id = pi.purchase_id
    WHERE pi.product_id = $id 
    AND pi.deleted_at IS NULL
    AND pi.remaining_qty > 0
    ORDER BY pi.id ASC
");

$rows = [];
while($d = mysqli_fetch_assoc($q)){
    $rows[] = $d;
}

$hasRow = !empty($rows);
$rowNum = 0;

$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];
?>

<?php if(!$hasRow): ?>

    <div class="fifo-detail-empty-state">
        <div class="fifo-detail-empty-icon">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <div class="fifo-detail-empty-title">Belum ada layer stok</div>
        <div class="fifo-detail-empty-sub">
            Produk ini belum memiliki riwayat pembelian dengan sisa stok
        </div>
    </div>

<?php else: ?>

<div class="stock-wrapper">

    <div class="stock-header-detail mb-3">
        <i class="fa-solid fa-boxes-stacked"></i>
        Detail Stok Masuk Barang
    </div>

    <div class="table-responsive">
        <table id="tableStock" class="table table-hover table-stock mb-0">

            <thead>
                <tr>
                    <th><i class="fa-regular fa-calendar"></i> Tanggal</th>
                    <th><i class="fa-solid fa-scale-balanced"></i> Qty Masuk</th>
                    <th><i class="fa-solid fa-box"></i> Sisa Stok</th>
                    <th><i class="fa-solid fa-tags"></i> Harga Beli</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach($rows as $d): ?>

                <?php
                    $rowNum++;
                    $tgl = strtotime($d['date']);
                    $tanggal = date('d', $tgl).' '.$bulan[(int)date('m',$tgl)].' '.date('Y',$tgl);

                    $remaining = $d['remaining_qty'] ? $d['remaining_qty'] : 0;
                    $form = $d['form'] ? $d['form'] : '-';
                ?>

                <tr<?= $rowNum === 1 ? ' class="fifo-first"' : '' ?>>

                    <!-- TANGGAL + FORM -->
                    <td>
                        <div class="date-block">

                            <div class="date-main">
                                <i class="fa-regular fa-calendar"></i>
                                <?= $tanggal ?>
                            </div>

                            <div class="form-badge">
                                <i class="fa-solid fa-file-lines"></i>
                                <?= htmlspecialchars($form) ?>
                            </div>

                            <?php if($rowNum === 1): ?>
                                <!-- Batch tertua: digunakan/dihabiskan terlebih dahulu (FIFO) -->
                                <div class="fifo-use-tag" title="Batch tertua, digunakan terlebih dahulu (FIFO)">
                                    <i class="fa-solid fa-fire"></i>
                                    Digunakan Terlebih Dahulu
                                </div>
                            <?php endif; ?>

                        </div>
                    </td>

                    <!-- QTY -->
                    <td>
                        <span class="badge-qty" style="padding-top: 0.25rem; padding-bottom: 0.15rem;">
                            <span class="badge-label">Masuk</span>
                            <span class="badge-value"><?= (int)$d['qty_stock'] ?> <?= htmlspecialchars($d['unit_stock']) ?></span>
                        </span>
                    </td>

                    <!-- REMAINING -->
                    <td>
                        <span class="badge-remain">
                            <span class="badge-label">Sisa</span>
                            <span class="badge-value"><?= (int)$remaining ?> <?= htmlspecialchars($d['unit_stock']) ?></span>
                        </span>
                    </td>

                    <!-- PRICE -->
                    <td>
                        <span class="badge-price" style="padding-top: 0.25rem; padding-bottom: 0.15rem;">
                            <span class="badge-label">Harga Beli</span>
                            <span class="badge-value">Rp <?= number_format($d['price_unit'], 0, ',', '.') ?></span>
                        </span>
                    </td>

                </tr>

            <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<?php endif; ?>