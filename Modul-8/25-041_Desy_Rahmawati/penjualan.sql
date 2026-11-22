CREATE TABLE `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `telp` varchar(12) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`id`, `nama`, `telp`, `alamat`) VALUES
(1, 'PT Mebel Jaya', '081234567890', 'Jakarta'),
(2, 'CV Furniture Makmur', '082345678901', 'Bandung'),
(3, 'UD Kayu Indah', '083456789012', 'Surabaya');

CREATE TABLE `barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(10) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `harga`, `stok`, `supplier_id`) VALUES
(1, 'BRG001', 'Meja Belajar', 750000, 20, 1),
(2, 'BRG002', 'Kursi Kayu', 350000, 30, 1),
(3, 'BRG003', 'Lemari Pakaian', 1200000, 10, 2),
(4, 'BRG004', 'Rak Buku', 500000, 15, 2),
(5, 'BRG005', 'Sofa Minimalis', 2500000, 8, 3);

CREATE TABLE `pelanggan` (
  `id` varchar(20) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `telp` varchar(12) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pelanggan` (`id`, `nama`, `jenis_kelamin`, `telp`, `alamat`) VALUES
('PLG001', 'Andi Saputra', 'L', '081111111111', 'Jakarta'),
('PLG002', 'Siti Aminah', 'P', '082222222222', 'Bandung'),
('PLG003', 'Budi Santoso', 'L', '083333333333', 'Surabaya'),
('PLG004', 'Dewi Lestari', 'P', '084444444444', 'Bekasi'),
('PLG005', 'Rina Oktavia', 'P', '085555555555', 'Depok');

CREATE TABLE `user` (
  `id_user` tinyint NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `hp` varchar(20) DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `alamat`, `hp`, `level`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'Jakarta', '081234567890', 1),
(2, 'kasir', 'kasir123', 'Petugas Kasir', 'Bandung', '082345678901', 2);

CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `waktu_transaksi` date DEFAULT NULL,
  `keterangan` text,
  `total` int DEFAULT NULL,
  `pelanggan_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pelanggan_id` (`pelanggan_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi` (`id`, `waktu_transaksi`, `keterangan`, `total`, `pelanggan_id`) VALUES
(1, '2026-11-01', 'Pembelian meja dan kursi', 1450000, 'PLG001'),
(2, '2026-11-02', 'Pembelian rak dan kursi', 1350000, 'PLG002'),
(3, '2026-11-03', 'Pembelian lemari dan rak', 1700000, 'PLG003'),
(4, '2026-11-04', 'Pembelian sofa dan kursi', 3200000, 'PLG001'),
(5, '2026-11-05', 'Pembelian meja dan rak', 1250000, 'PLG004'),
(6, '2026-11-06', 'Pembelian kursi dan rak', 1900000, 'PLG005'),
(7, '2026-11-07', 'Pembelian lemari dan kursi', 1550000, 'PLG002'),
(8, '2026-11-08', 'Pembelian sofa dan meja', 3250000, 'PLG003'),
(9, '2026-11-09', 'Pembelian rak dan kursi', 2200000, 'PLG004'),
(10, '2026-11-10', 'Pembelian meja dan lemari', 2700000, 'PLG005');

CREATE TABLE `transaksi_detail` (
  `transaksi_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `harga` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  PRIMARY KEY (`transaksi_id`,`barang_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`),
  CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_detail` (`transaksi_id`, `barang_id`, `harga`, `qty`) VALUES
(1, 1, 750000, 1),
(1, 2, 350000, 2),
(2, 2, 350000, 1),
(2, 4, 500000, 2),
(3, 3, 1200000, 1),
(3, 4, 500000, 1),
(4, 2, 350000, 2),
(4, 5, 2500000, 1),
(5, 1, 750000, 1),
(5, 4, 500000, 1),
(6, 2, 350000, 4),
(6, 4, 500000, 1),
(7, 2, 350000, 1),
(7, 3, 1200000, 1),
(8, 1, 750000, 1),
(8, 5, 2500000, 1),
(9, 2, 350000, 2),
(9, 4, 500000, 3),
(10, 1, 750000, 2),
(10, 3, 1200000, 1);

CREATE TABLE `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `waktu_bayar` datetime DEFAULT NULL,
  `total` int DEFAULT NULL,
  `metode` enum('TUNAI','TRANSFER','EDC') DEFAULT NULL,
  `transaksi_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_id` (`transaksi_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pembayaran` (`id`, `waktu_bayar`, `total`, `metode`, `transaksi_id`) VALUES
(1, '2026-11-01 09:15:00', 1450000, 'TUNAI', 1),
(2, '2026-11-02 10:30:00', 1350000, 'TRANSFER', 2),
(3, '2026-11-03 11:00:00', 1700000, 'EDC', 3),
(4, '2026-11-04 13:20:00', 3200000, 'TRANSFER', 4),
(5, '2026-11-05 14:10:00', 1250000, 'TUNAI', 5),
(6, '2026-11-06 09:45:00', 1900000, 'EDC', 6),
(7, '2026-11-07 10:15:00', 1550000, 'TRANSFER', 7),
(8, '2026-11-08 12:30:00', 3250000, 'TUNAI', 8),
(9, '2026-11-09 15:00:00', 2200000, 'TRANSFER', 9),
(10, '2026-11-10 16:20:00', 2700000, 'EDC', 10);
