<?php
$halaman = 'master';
$judul = 'Data Supplier';
require_once 'koneksi.php';
require_once 'navigasi.php';
proteksi_owner();

$aksi = $_GET['aksi'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'hapus' && $id > 0) {
    query("DELETE FROM supplier WHERE id = ?", [$id], "i");
    header("Location: supplier.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST['nama'] ?? '');
    $telp = trim($_POST['telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($aksi === 'ubah' && $id > 0) {
        query("UPDATE supplier SET nama=?, telp=?, alamat=? WHERE id=?", [$nama, $telp, $alamat, $id], "sssi");
    } else {
        query("INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)", [$nama, $telp, $alamat], "sss");
    }
    header("Location: supplier.php");
    exit;
}

$data = null;
if ($aksi === 'ubah' && $id > 0) {
    $res = query("SELECT * FROM supplier WHERE id = ?", [$id], "i");
    $data = $res ? mysqli_fetch_assoc($res) : null;
    if (!$data) { header("Location: supplier.php"); exit; }
}
?>

<?php if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 fw-bold"><?= $aksi==='ubah'?'Ubah':'Tambah' ?> Supplier</div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="nama" class="form-control" value="<?= e($data['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="telp" class="form-control" value="<?= e($data['telp'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= e($data['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="supplier.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Data Supplier</h4>
    <a href="supplier.php?aksi=tambah" class="btn btn-primary">Tambah Supplier</a>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>Nama Supplier</th><th>Telepon</th><th>Alamat</th><th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $q = query("SELECT * FROM supplier ORDER BY id DESC");
                while ($s = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($s['nama']) ?></td>
                    <td><?= e($s['telp'] ?? '-') ?></td>
                    <td><?= e($s['alamat'] ?? '-') ?></td>
                    <td>
                        <a href="supplier.php?aksi=ubah&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-warning">Ubah</a>
                        <a href="supplier.php?aksi=hapus&id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus supplier?')">Hapus</a>
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
