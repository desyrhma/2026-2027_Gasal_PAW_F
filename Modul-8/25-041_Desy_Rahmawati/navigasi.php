<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$level = (int)$_SESSION['level'];
$nama_user = $_SESSION['nama'] ?? $_SESSION['username'];
$halaman = $halaman ?? '';

function proteksi_owner() {
    if (!isset($_SESSION['level']) || (int)$_SESSION['level'] !== 1) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul ?? 'Sistem Penjualan') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Sistem Penjualan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= $halaman=='home'?'active fw-bold':'' ?>" href="index.php">Home</a></li>
                <?php if ($level === 1): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $halaman=='master'?'active fw-bold':'' ?>" href="#" data-bs-toggle="dropdown">Data Master</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="barang.php">Data Barang</a></li>
                        <li><a class="dropdown-item" href="supplier.php">Data Supplier</a></li>
                        <li><a class="dropdown-item" href="pelanggan.php">Data Pelanggan</a></li>
                        <li><a class="dropdown-item" href="user.php">Data User</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link <?= $halaman=='transaksi'?'active fw-bold':'' ?>" href="transaksi.php">Transaksi</a></li>
                <li class="nav-item"><a class="nav-link <?= $halaman=='laporan'?'active fw-bold':'' ?>" href="laporan.php">Laporan</a></li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-semibold" href="#" data-bs-toggle="dropdown"><?= e($nama_user) ?></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">Level: <?= $level===1?'Owner':'Kasir' ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-5">
