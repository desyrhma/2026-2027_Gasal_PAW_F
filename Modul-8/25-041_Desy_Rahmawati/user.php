<?php
$halaman = 'master';
$judul = 'Data User';
require_once 'koneksi.php';
require_once 'navigasi.php';
proteksi_owner();

$aksi = $_GET['aksi'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'hapus' && $id > 0 && $id !== (int)$_SESSION['id_user']) {
    query("DELETE FROM user WHERE id_user = ?", [$id], "i");
    header("Location: user.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $hp = trim($_POST['hp'] ?? '');
    $level_user = (int)($_POST['level'] ?? 2);

    if ($aksi === 'ubah' && $id > 0) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            query("UPDATE user SET username=?, password=?, nama=?, alamat=?, hp=?, level=? WHERE id_user=?", [$username, $hash, $nama, $alamat, $hp, $level_user, $id], "sssssii");
        } else {
            query("UPDATE user SET username=?, nama=?, alamat=?, hp=?, level=? WHERE id_user=?", [$username, $nama, $alamat, $hp, $level_user, $id], "ssssii");
        }
        if ($id === (int)$_SESSION['id_user']) {
            $_SESSION['username'] = $username;
            $_SESSION['nama'] = $nama;
            $_SESSION['level'] = $level_user;
        }
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        query("INSERT INTO user (username, password, nama, alamat, hp, level) VALUES (?, ?, ?, ?, ?, ?)", [$username, $hash, $nama, $alamat, $hp, $level_user], "sssssi");
    }
    header("Location: user.php");
    exit;
}

$data = null;
if ($aksi === 'ubah' && $id > 0) {
    $res = query("SELECT * FROM user WHERE id_user = ?", [$id], "i");
    $data = $res ? mysqli_fetch_assoc($res) : null;
    if (!$data) { header("Location: user.php"); exit; }
}
?>

<?php if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 fw-bold"><?= $aksi==='ubah'?'Ubah':'Tambah' ?> User</div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= e($data['username'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <?= $aksi==='ubah'?'<span class="text-muted small">(kosongkan jika tidak diubah)</span>':'' ?></label>
                        <input type="password" name="password" class="form-control" <?= $aksi==='tambah'?'required':'' ?>>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= e($data['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level Hak Akses</label>
                        <select name="level" class="form-select">
                            <option value="1" <?= (($data['level'] ?? '') == 1) ? 'selected' : '' ?>>1 - Owner</option>
                            <option value="2" <?= (($data['level'] ?? '') == 2) ? 'selected' : '' ?>>2 - Kasir</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="hp" class="form-control" value="<?= e($data['hp'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= e($data['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="user.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Data User</h4>
    <a href="user.php?aksi=tambah" class="btn btn-primary">Tambah User</a>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th><th>Username</th><th>Nama</th><th>HP</th><th>Alamat</th><th>Level</th><th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $q = query("SELECT * FROM user ORDER BY id_user ASC");
                while ($u = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['nama']) ?></td>
                    <td><?= e($u['hp'] ?? '-') ?></td>
                    <td><?= e($u['alamat'] ?? '-') ?></td>
                    <td><span class="badge bg-<?= ((int)$u['level'] === 1) ? 'primary' : 'success' ?>"><?= ((int)$u['level'] === 1) ? 'Owner' : 'Kasir' ?></span></td>
                    <td>
                        <a href="user.php?aksi=ubah&id=<?= (int)$u['id_user'] ?>" class="btn btn-sm btn-warning">Ubah</a>
                        <?php if ((int)$u['id_user'] !== (int)$_SESSION['id_user']): ?>
                            <a href="user.php?aksi=hapus&id=<?= (int)$u['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user?')">Hapus</a>
                        <?php endif; ?>
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
