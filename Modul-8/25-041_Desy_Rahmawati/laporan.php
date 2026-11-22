<?php
$halaman = 'laporan';
$judul = 'Laporan Penjualan';
require_once 'koneksi.php';
require_once 'navigasi.php';

$rd = mysqli_fetch_assoc(query("SELECT MIN(waktu_transaksi) AS a, MAX(waktu_transaksi) AS b FROM transaksi"));
$mulai = trim($_GET['mulai'] ?? ($rd['a'] ?? ''));
$selesai = trim($_GET['selesai'] ?? ($rd['b'] ?? ''));

$rekap = [];
$total_keseluruhan = 0;
$jumlah_pelanggan_unik = 0;
$label_grafik = [];
$data_grafik = [];

if (isset($_GET['tampil']) && $mulai !== '' && $selesai !== '' && $mulai <= $selesai) {
    $q_rekap = query("SELECT DATE(waktu_transaksi) AS tanggal, SUM(total) AS th FROM transaksi WHERE waktu_transaksi >= ? AND waktu_transaksi <= ? GROUP BY DATE(waktu_transaksi) ORDER BY tanggal ASC", [$mulai, $selesai], "ss");
    while ($b = mysqli_fetch_assoc($q_rekap)) {
        $rekap[] = $b;
        $label_grafik[] = tgl($b['tanggal']);
        $data_grafik[] = (float)$b['th'];
        $total_keseluruhan += (float)$b['th'];
    }

    $q_pel = query("SELECT COUNT(DISTINCT pelanggan_id) AS tu FROM transaksi WHERE waktu_transaksi >= ? AND waktu_transaksi <= ?", [$mulai, $selesai], "ss");
    $jumlah_pelanggan_unik = (int)(mysqli_fetch_assoc($q_pel)['tu'] ?? 0);
}

if (isset($_GET['export']) && $mulai !== '' && $selesai !== '') {
    $nama_file = "laporan_" . preg_replace('/[^0-9\-]/', '', $mulai) . "_sd_" . preg_replace('/[^0-9\-]/', '', $selesai) . ".xls";
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$nama_file");

    echo "<table border='1'><tr><th>No</th><th>Tanggal</th><th>Total Pendapatan</th></tr>";
    $no = 1;
    foreach ($rekap as $r) {
        echo "<tr><td>$no</td><td>" . e(tgl($r['tanggal'])) . "</td><td>" . (float)$r['th'] . "</td></tr>";
        $no++;
    }
    echo "<tr><td colspan='2'><b>Pelanggan Unik</b></td><td>$jumlah_pelanggan_unik</td></tr>";
    echo "<tr><td colspan='2'><b>Total Pendapatan</b></td><td>$total_keseluruhan</td></tr></table>";
    exit;
}
?>

<h4 class="fw-bold mb-3">Laporan Penjualan Harian</h4>

<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small">Tanggal Mulai</label>
                <input type="date" name="mulai" class="form-control" value="<?= e($mulai) ?>" required>
            </div>
            <div class="col-auto">
                <label class="form-label small">Tanggal Selesai</label>
                <input type="date" name="selesai" class="form-control" value="<?= e($selesai) ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" name="tampil" value="1" class="btn btn-primary">Tampilkan</button>
            </div>
            <?php if (count($rekap) > 0): ?>
            <div class="col-auto">
                <a href="laporan.php?mulai=<?= urlencode($mulai) ?>&selesai=<?= urlencode($selesai) ?>&tampil=1&export=1" class="btn btn-success">Export Excel</a>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger" onclick="window.print()">Cetak</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (count($rekap) > 0): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Jumlah Hari Transaksi</div>
            <div class="fs-4 fw-bold text-dark"><?= count($rekap) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Pelanggan Unik</div>
            <div class="fs-4 fw-bold text-success"><?= $jumlah_pelanggan_unik ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <div class="text-muted small">Total Pendapatan</div>
            <div class="fs-4 fw-bold text-primary"><?= rupiah($total_keseluruhan) ?></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body">
        <canvas id="grafikPenjualan" height="100"></canvas>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>Tanggal Transaksi</th><th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($rekap as $b): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tgl($b['tanggal']) ?></td>
                    <td class="fw-bold"><?= rupiah($b['th']) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-primary">
                    <td colspan="2" class="fw-bold">Total Pendapatan Keseluruhan</td>
                    <td class="fw-bold"><?= rupiah($total_keseluruhan) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafikPenjualan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($label_grafik) ?>,
        datasets: [{
            label: 'Total Pendapatan',
            data: <?= json_encode($data_grafik) ?>,
            backgroundColor: '#0d6efd'
        }]
    }
});
</script>
<?php elseif (isset($_GET['tampil'])): ?>
    <div class="alert alert-warning">Tidak ada transaksi ditemukan pada rentang tanggal tersebut.</div>
<?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
