<?php
$conn = mysqli_connect("localhost", "root", "", "penjualan", 3307);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

function query($sql, $param = [], $tipe = "") {
    global $conn;
    if (empty($param)) return mysqli_query($conn, $sql);
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return $res !== false ? $res : mysqli_stmt_affected_rows($stmt);
}

function rupiah($n) { return "Rp " . number_format($n, 0, ",", "."); }
function tgl($d) { return date("d/m/Y", strtotime($d)); }
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
