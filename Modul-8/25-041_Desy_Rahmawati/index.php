<?php
$halaman = 'home';
$judul = 'Home';
require_once 'koneksi.php';
require_once 'navigasi.php';

$total_barang = mysqli_fetch_assoc(query("SELECT COUNT(*) AS n FROM barang"))['n'];
$total_pelanggan = mysqli_fetch_assoc(query("SELECT COUNT(*) AS n FROM pelanggan"))['n'];
$total_transaksi = mysqli_fetch_assoc(query("SELECT COUNT(*) AS n FROM transaksi"))['n'];
$total_pendapatan = mysqli_fetch_assoc(query("SELECT COALESCE(SUM(total), 0) AS n FROM transaksi"))['n'];
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1">Selamat Datang, <?= e($nama_user) ?></h4>
    <p class="text-muted">Masuk sebagai <strong><?= $level===1?'Owner (Level 1)':'Kasir (Level 2)' ?></strong></p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Total Barang</div>
            <div class="fs-4 fw-bold text-primary"><?= (int)$total_barang ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Total Pelanggan</div>
            <div class="fs-4 fw-bold text-success"><?= (int)$total_pelanggan ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Total Transaksi</div>
            <div class="fs-4 fw-bold text-warning"><?= (int)$total_transaksi ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Total Pendapatan</div>
            <div class="fs-4 fw-bold text-danger"><?= rupiah($total_pendapatan) ?></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 fw-bold">5 Transaksi Terakhir</div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = query("SELECT t.*, p.nama AS np FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id ORDER BY t.id DESC LIMIT 5");
                while ($b = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td>#<?= (int)$b['id'] ?></td>
                    <td><?= tgl($b['waktu_transaksi']) ?></td>
                    <td><?= e($b['np'] ?? '-') ?></td>
                    <td class="fw-bold"><?= rupiah($b['total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
