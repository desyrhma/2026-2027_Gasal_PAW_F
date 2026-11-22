<?php
$halaman = 'transaksi';
$judul = 'Transaksi';
require_once 'koneksi.php';
require_once 'navigasi.php';

$aksi = $_GET['aksi'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'hapus' && $id > 0) {
    query("DELETE FROM transaksi_detail WHERE transaksi_id = ?", [$id], "i");
    query("DELETE FROM pembayaran WHERE transaksi_id = ?", [$id], "i");
    query("DELETE FROM transaksi WHERE id = ?", [$id], "i");
    header("Location: transaksi.php");
    exit;
}

if ($aksi === 'hapus_item') {
    $bid = (int)($_GET['bid'] ?? 0);
    $tid = (int)($_GET['tid'] ?? 0);
    if ($bid > 0 && $tid > 0) {
        query("DELETE FROM transaksi_detail WHERE transaksi_id=? AND barang_id=?", [$tid, $bid], "ii");
        $tot = (int)(mysqli_fetch_assoc(query("SELECT COALESCE(SUM(harga*qty), 0) AS t FROM transaksi_detail WHERE transaksi_id=?", [$tid], "i"))['t'] ?? 0);
        query("UPDATE transaksi SET total=? WHERE id=?", [$tot, $tid], "ii");
    }
    header("Location: transaksi.php?aksi=detail&id=$tid");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($aksi === 'tambah') {
        $tgl = trim($_POST['waktu_transaksi'] ?? date('Y-m-d'));
        $pid = trim($_POST['pelanggan_id'] ?? '');
        $ket = trim($_POST['keterangan'] ?? '');
        query("INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id) VALUES (?, ?, 0, ?)", [$tgl, $ket, $pid], "sss");
        header("Location: transaksi.php?aksi=detail&id=" . mysqli_insert_id($conn));
        exit;
    }

    if ($aksi === 'detail' && $id > 0) {
        $bid = (int)($_POST['barang_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 0);
        $brg = mysqli_fetch_assoc(query("SELECT harga FROM barang WHERE id = ?", [$bid], "i"));

        if ($brg && $qty > 0) {
            $ada = query("SELECT qty FROM transaksi_detail WHERE transaksi_id=? AND barang_id=?", [$id, $bid], "ii");
            if (mysqli_num_rows($ada) > 0) {
                query("UPDATE transaksi_detail SET qty=qty+? WHERE transaksi_id=? AND barang_id=?", [$qty, $id, $bid], "iii");
            } else {
                query("INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES (?, ?, ?, ?)", [$id, $bid, (int)$brg['harga'], $qty], "iiii");
            }
            $tot = (int)(mysqli_fetch_assoc(query("SELECT COALESCE(SUM(harga*qty), 0) AS t FROM transaksi_detail WHERE transaksi_id=?", [$id], "i"))['t'] ?? 0);
            query("UPDATE transaksi SET total=? WHERE id=?", [$tot, $id], "ii");
        }
        header("Location: transaksi.php?aksi=detail&id=$id");
        exit;
    }
}
?>

<?php if ($aksi === 'tambah'): ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 fw-bold">Tambah Transaksi</div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" name="waktu_transaksi" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <select name="pelanggan_id" class="form-select" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php $qp = query("SELECT * FROM pelanggan ORDER BY nama ASC"); while ($p = mysqli_fetch_assoc($qp)): ?>
                                <option value="<?= e($p['id']) ?>"><?= e($p['nama']) ?> (<?= e($p['id']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="transaksi.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($aksi === 'detail' && $id > 0): ?>
<?php
$res_t = query("SELECT t.*, p.nama AS np FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id WHERE t.id = ?", [$id], "i");
$transaksi = $res_t ? mysqli_fetch_assoc($res_t) : null;
if (!$transaksi) { header("Location: transaksi.php"); exit; }
$daftar_detail = query("SELECT td.*, b.kode_barang, b.nama_barang FROM transaksi_detail td JOIN barang b ON td.barang_id=b.id WHERE td.transaksi_id = ?", [$id], "i");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Detail Transaksi #<?= (int)$transaksi['id'] ?></h4>
    <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Transaksi</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted" style="width: 120px;">Tanggal</td><td>: <?= tgl($transaksi['waktu_transaksi']) ?></td></tr>
                <tr><td class="text-muted">Pelanggan</td><td>: <?= e($transaksi['np'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Keterangan</td><td>: <?= e($transaksi['keterangan'] ?? '-') ?></td></tr>
                <tr><td class="text-muted">Total</td><td class="fw-bold text-primary fs-5">: <?= rupiah($transaksi['total']) ?></td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 border-0 shadow-sm bg-white">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Tambah Barang Belanja</h6>
            <form method="POST">
                <div class="mb-2">
                    <select name="barang_id" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php $qb = query("SELECT * FROM barang ORDER BY nama_barang ASC"); while ($b = mysqli_fetch_assoc($qb)): ?>
                            <option value="<?= (int)$b['id'] ?>"><?= e($b['nama_barang']) ?> - <?= rupiah($b['harga']) ?> (Stok: <?= (int)$b['stok'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="number" name="qty" class="form-control" value="1" min="1" placeholder="Jumlah (Qty)" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Tambah ke Transaksi</button>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 fw-bold">Rincian Barang</div>
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>Kode</th><th>Nama Barang</th><th>Harga Satuan</th><th>Qty</th><th>Subtotal</th><th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($item = mysqli_fetch_assoc($daftar_detail)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($item['kode_barang']) ?></td>
                    <td><?= e($item['nama_barang']) ?></td>
                    <td><?= rupiah($item['harga']) ?></td>
                    <td><?= (int)$item['qty'] ?></td>
                    <td class="fw-bold"><?= rupiah($item['harga'] * $item['qty']) ?></td>
                    <td><a href="transaksi.php?aksi=hapus_item&tid=<?= (int)$id ?>&bid=<?= (int)$item['barang_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item?')">Hapus</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Data Transaksi</h4>
    <a href="transaksi.php?aksi=tambah" class="btn btn-primary">Tambah Transaksi</a>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th>Total</th><th style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = query("SELECT t.*, p.nama AS np FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id ORDER BY t.id DESC");
                while ($b = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td>#<?= (int)$b['id'] ?></td>
                    <td><?= tgl($b['waktu_transaksi']) ?></td>
                    <td><?= e($b['np'] ?? '-') ?></td>
                    <td><?= e($b['keterangan'] ?? '-') ?></td>
                    <td class="fw-bold"><?= rupiah($b['total']) ?></td>
                    <td>
                        <a href="transaksi.php?aksi=detail&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-info text-white">Detail</a>
                        <a href="transaksi.php?aksi=hapus&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus transaksi?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
