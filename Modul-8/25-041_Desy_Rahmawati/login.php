<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
require_once "koneksi.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    $q = query("SELECT * FROM user WHERE username = ? LIMIT 1", [$user], "s");
    $d = $q ? mysqli_fetch_assoc($q) : null;

    if ($d && (password_verify($pass, $d['password']) || $pass === $d['password'])) {
        if ($pass === $d['password']) {
            query("UPDATE user SET password = ? WHERE id_user = ?", [password_hash($pass, PASSWORD_DEFAULT), $d['id_user']], "si");
        }
        session_regenerate_id(true);
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = (int)$d['id_user'];
        $_SESSION['username'] = $d['username'];
        $_SESSION['nama'] = $d['nama'];
        $_SESSION['level'] = (int)$d['level'];
        header("Location: index.php");
        exit;
    }
    $error = "Username atau password salah";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
<div style="width: 100%; max-width: 380px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center py-3">
            <h5 class="mb-0 fw-bold">Sistem Penjualan</h5>
            <small>Silakan masuk ke akun Anda</small>
        </div>
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
