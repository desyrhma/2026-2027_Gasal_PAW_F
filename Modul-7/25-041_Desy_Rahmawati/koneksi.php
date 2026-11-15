<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "penjualan";
$port= 3307;

$koneksi = mysqli_connect($host, $user, $pass, $db,$port);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

function format_tanggal($tgl)
{
    return date("d/m/Y", strtotime($tgl));
}

function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ",", ".");
}
?>