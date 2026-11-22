<?php
$halaman = 'master';
$judul = 'Data Pelanggan';
require_once 'koneksi.php';
require_once 'navigasi.php';
proteksi_owner();

$aksi = $_GET['aksi'] ?? 'list';
$id = trim($_GET['id'] ?? '');

if ($aksi === 'hapus' && $id !== '') {
    query("DELETE FROM pelanggan WHERE id = ?", [$id], "s");
    header("Location: pelanggan.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pid = trim($_POST['id'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jk = trim($_POST['jenis_kelamin'] ?? 'L');
    $telp = trim($_POST['telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($aksi === 'ubah' && $id !== '') {
        query("UPDATE pelanggan SET nama=?, jenis_kelamin=?, telp=?, alamat=? WHERE id=?", [$nama, $jk, $telp, $alamat, $id], "sssss");
    } else {
        query("INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)", [$pid, $nama, $jk, $telp, $alamat], "sssss");
    }
    header("Location: pelanggan.php");
    exit;
}

$data = null;
if ($aksi === 'ubah' && $id !== '') {
    $res = query("SELECT * FROM pelanggan WHERE id = ?", [$id], "s");
    $data = $res ? mysqli_fetch_assoc($res) : null;
    if (!$data) { header("Location: pelanggan.php"); exit; }
}
?>

<?php if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 fw-bold"><?= $aksi==='ubah'?'Ubah':'Tambah' ?> Pelanggan</div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ID Pelanggan</label>
                        <?php if ($aksi === 'ubah'): ?>
                            <input type="text" class="form-control" value="<?= e($data['id'] ?? '') ?>" readonly>
                            <input type="hidden" name="id" value="<?= e($data['id'] ?? '') ?>">
                        <?php else: ?>
                            <input type="text" name="id" class="form-control" placeholder="Contoh: PLG006" required>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="nama" class="form-control" value="<?= e($data['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="L" <?= (($data['jenis_kelamin'] ?? '') === 'L') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= (($data['jenis_kelamin'] ?? '') === 'P') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telp" class="form-control" value="<?= e($data['telp'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= e($data['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Data Pelanggan</h4>
    <a href="pelanggan.php?aksi=tambah" class="btn btn-primary">Tambah Pelanggan</a>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>ID</th><th>Nama</th><th>JK</th><th>Telepon</th><th>Alamat</th><th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $q = query("SELECT * FROM pelanggan ORDER BY id ASC");
                while ($p = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($p['id']) ?></td>
                    <td><?= e($p['nama']) ?></td>
                    <td><?= $p['jenis_kelamin']==='L'?'Laki-laki':'Perempuan' ?></td>
                    <td><?= e($p['telp'] ?? '-') ?></td>
                    <td><?= e($p['alamat'] ?? '-') ?></td>
                    <td>
                        <a href="pelanggan.php?aksi=ubah&id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-warning">Ubah</a>
                        <a href="pelanggan.php?aksi=hapus&id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelanggan?')">Hapus</a>
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
