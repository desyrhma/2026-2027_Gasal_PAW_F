<?php
include 'koneksi.php';

// Mengambil tanggal transaksi pertama dan terakhir
$default = mysqli_query($koneksi, "
    SELECT MIN(waktu_transaksi) AS awal,
           MAX(waktu_transaksi) AS akhir
    FROM transaksi
");

$d = mysqli_fetch_assoc($default);

$mulai = $_GET['tanggal_mulai'] ?? ($d['awal'] ?? '');
$selesai = $_GET['tanggal_selesai'] ?? ($d['akhir'] ?? '');

$rekap = [];
$labels = [];
$data_total = [];

$pelanggan_unik = 0;
$total_pendapatan = 0;
$adaData = false;
$error = "";

// Proses laporan
if (isset($_GET['tampilkan'])) {

    if ($mulai == "" || $selesai == "") {
        $error = "Tanggal mulai dan tanggal selesai wajib diisi!";
    } elseif ($mulai > $selesai) {
        $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai!";
    } else {

        // Rekap penjualan berdasarkan tanggal
        $sql = "SELECT DATE(waktu_transaksi) AS tanggal,
                       SUM(total) AS total_pendapatan
                FROM transaksi
                WHERE waktu_transaksi >= '$mulai 00:00:00'
                AND waktu_transaksi <= '$selesai 23:59:59'
                GROUP BY DATE(waktu_transaksi)
                ORDER BY tanggal ASC";

        $hasil = mysqli_query($koneksi, $sql);

        while ($row = mysqli_fetch_assoc($hasil)) {
            $rekap[] = $row;
            $labels[] = $row['tanggal'];
            $data_total[] = (float)$row['total_pendapatan'];

            $total_pendapatan += $row['total_pendapatan'];
        }

        // Menghitung pelanggan unik
        $sql2 = "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan
                 FROM transaksi
                 WHERE waktu_transaksi >= '$mulai 00:00:00'
                 AND waktu_transaksi <= '$selesai 23:59:59'";

        $hasil2 = mysqli_query($koneksi, $sql2);
        $ringkas = mysqli_fetch_assoc($hasil2);

        $pelanggan_unik = $ringkas['jumlah_pelanggan'] ?? 0;

        if (count($rekap) > 0) {
            $adaData = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Harian</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 20px;
        }

        h1 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 15px;
        }

        .periode {
            text-align: center;
            color: #666;
            margin-bottom: 15px;
        }

        .filter {
            padding: 15px;
            background: #F5F5F5;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .filter label {
            margin-right: 5px;
        }

        .filter input[type="date"] {
            padding: 8px;
        }

        .btn {
            padding: 8px 15px;
            background: #191970;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #1c5db5;
        }

        .btn-print {
            background: #B22222;
        }

        .btn-excel {
            background: #1e7e34;
            margin-left: 10px;
        }

        .grafik {
            width: 600px;
            height: 300px;
            margin-top: 20px;
        }

        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #F5F5F5;
        }

        .ringkasan {
            margin-top: 20px;
            padding: 12px;
            background:#F5F5F5;
            border-radius: 4px;
            
        }

        .error {
            background: #fdecea;
            color: #b3261e;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info {
            background: #e8f4fd;
            padding: 10px;
            margin-bottom: 15px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .container {
                border: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <h1>Laporan Penjualan Harian</h1>

    <p class="periode">
        Periode:
        <?= htmlspecialchars($mulai) ?>
        s/d
        <?= htmlspecialchars($selesai) ?>
    </p>

    <div class="filter no-print">

        <form method="GET" action="">

            <label>Tanggal Mulai</label>

            <input type="date"
                   name="tanggal_mulai"
                   value="<?= htmlspecialchars($mulai) ?>"
                   required>

            <label style="margin-left:15px;">
                Tanggal Selesai
            </label>

            <input type="date"
                   name="tanggal_selesai"
                   value="<?= htmlspecialchars($selesai) ?>"
                   required>

            <button type="submit"
                    name="tampilkan"
                    value="1"
                    class="btn"
                    style="margin-left:15px;">
                Tampilkan
            </button>

        </form>

    </div>


    <?php if ($error != ""): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php elseif (isset($_GET['tampilkan']) && !$adaData): ?>

        <div class="info">
            Tidak ada data transaksi pada rentang tanggal ini.
        </div>

    <?php endif; ?>


    <?php if ($adaData): ?>

        <!-- TOMBOL CETAK DAN EXCEL -->
        <div class="no-print">

            <button onclick="window.print()"
                    class="btn btn-print">
                Cetak PDF
            </button>

            <a href="export.php?tanggal_mulai=<?= urlencode($mulai) ?>&tanggal_selesai=<?= urlencode($selesai) ?>"
               class="btn btn-excel">
                Export Excel
            </a>

        </div>

        <div class="grafik">
            <canvas id="myChart"></canvas>
        </div>

        <h3>Rekapitulasi Penjualan Harian</h3>

        <table>

            <tr>
                <th>No</th>
                <th>Total Pendapatan</th>
                <th>Tanggal Transaksi</th>
            </tr>

            <?php $no = 1; foreach ($rekap as $row): ?>

            <tr>
                <td><?= $no++ ?></td>

                <td>
                    Rp. <?= number_format(
                        $row['total_pendapatan'],
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['tanggal']) ?>
                </td>
            </tr>

            <?php endforeach; ?>

        </table>


        <div class="ringkasan">

            <b>Jumlah Pelanggan:</b>
            <?= $pelanggan_unik ?> Orang

            |

            <b>Jumlah Pendapatan:</b>
            Rp. <?= number_format(
                $total_pendapatan,
                0,
                ',',
                '.'
            ) ?>

        </div>

        <script>

            const ctx = document.getElementById('myChart').getContext('2d');

            new Chart(ctx, {

                type: 'bar',

                data: {
                    labels: <?= json_encode($labels) ?>,

                    datasets: [{

                        label: 'Total Penjualan',

                        data: <?= json_encode($data_total) ?>,

                        backgroundColor: '#FA8072',

                        borderColor: '#ffffff',

                        borderWidth: 1

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {
                            beginAtZero: true
                        }

                    }

                }

            });

        </script>

    <?php endif; ?>

</div>

</body>
</html>