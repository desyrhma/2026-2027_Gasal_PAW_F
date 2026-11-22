<?php
$halaman = 'master';
$judul = 'Data Barang';
require_once 'koneksi.php';
require_once 'navigasi.php';
proteksi_owner();

$aksi = $_GET['aksi'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'hapus' && $id > 0) {
    query("DELETE FROM barang WHERE id = ?", [$id], "i");
    header("Location: barang.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST['kode_barang'] ?? '');
    $nama = trim($_POST['nama_barang'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $sid = (int)($_POST['supplier_id'] ?? 0) ?: null;

    if ($aksi === 'ubah' && $id > 0) {
        query("UPDATE barang SET kode_barang=?, nama_barang=?, harga=?, stok=?, supplier_id=? WHERE id=?", [$kode, $nama, $harga, $stok, $sid, $id], "ssiiii");
    } else {
        query("INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES (?, ?, ?, ?, ?)", [$kode, $nama, $harga, $stok, $sid], "ssiii");
    }
    header("Location: barang.php");
    exit;
}

$data = null;
if ($aksi === 'ubah' && $id > 0) {
    $res = query("SELECT * FROM barang WHERE id = ?", [$id], "i");
    $data = $res ? mysqli_fetch_assoc($res) : null;
    if (!$data) { header("Location: barang.php"); exit; }
}

$daftar_supplier = query("SELECT * FROM supplier ORDER BY nama ASC");
?>

<?php if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 fw-bold"><?= $aksi==='ubah'?'Ubah':'Tambah' ?> Barang</div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control" value="<?= e($data['kode_barang'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= e($data['nama_barang'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= e($data['harga'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= e($data['stok'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="0">-- Pilih Supplier --</option>
                            <?php while ($s = mysqli_fetch_assoc($daftar_supplier)): ?>
                                <option value="<?= (int)$s['id'] ?>" <?= (($data['supplier_id'] ?? 0) == $s['id']) ? 'selected' : '' ?>><?= e($s['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="barang.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Data Barang</h4>
    <a href="barang.php?aksi=tambah" class="btn btn-primary">Tambah Barang</a>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Supplier</th><th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $q = query("SELECT b.*, s.nama AS ns FROM barang b LEFT JOIN supplier s ON b.supplier_id=s.id ORDER BY b.id DESC");
                while ($b = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($b['kode_barang']) ?></td>
                    <td><?= e($b['nama_barang']) ?></td>
                    <td><?= rupiah($b['harga']) ?></td>
                    <td><?= (int)$b['stok'] ?></td>
                    <td><?= e($b['ns'] ?? '-') ?></td>
                    <td>
                        <a href="barang.php?aksi=ubah&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-warning">Ubah</a>
                        <a href="barang.php?aksi=hapus&id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang?')">Hapus</a>
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
