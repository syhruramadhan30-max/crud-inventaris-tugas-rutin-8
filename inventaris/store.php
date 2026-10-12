<?php

session_start();

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit;
}

// Mengambil data dari form
$nama_produk = trim($_POST['nama_produk'] ?? '');
$kategori_id = $_POST['kategori_id'] ?? '';
$supplier_id = $_POST['supplier_id'] ?? '';
$stok = $_POST['stok'] ?? '';
$harga = $_POST['harga'] ?? '';

// Validasi input
if (
    $nama_produk === '' ||
    $kategori_id === '' ||
    $supplier_id === '' ||
    $stok === '' ||
    $harga === ''
) {
    $_SESSION['error'] = 'Semua data produk wajib diisi.';
    header('Location: create.php');
    exit;
}

if (!is_numeric($kategori_id) || !is_numeric($supplier_id)) {
    $_SESSION['error'] = 'Kategori atau supplier tidak valid.';
    header('Location: create.php');
    exit;
}

if (!is_numeric($stok) || $stok < 0) {
    $_SESSION['error'] = 'Stok harus berupa angka dan tidak boleh negatif.';
    header('Location: create.php');
    exit;
}

if (!is_numeric($harga) || $harga < 0) {
    $_SESSION['error'] = 'Harga harus berupa angka dan tidak boleh negatif.';
    header('Location: create.php');
    exit;
}

try {

    // Prepared statement
    $sql = "
        INSERT INTO produk
        (nama_produk, kategori_id, supplier_id, stok, harga)
        VALUES
        (:nama_produk, :kategori_id, :supplier_id, :stok, :harga)
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':nama_produk' => $nama_produk,
        ':kategori_id' => $kategori_id,
        ':supplier_id' => $supplier_id,
        ':stok' => $stok,
        ':harga' => $harga
    ]);

    // Flash message berhasil
    $_SESSION['success'] = 'Produk berhasil ditambahkan.';

    // Redirect ke halaman utama
    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['error'] = 'Produk gagal ditambahkan.';
    header('Location: create.php');
    exit;
}