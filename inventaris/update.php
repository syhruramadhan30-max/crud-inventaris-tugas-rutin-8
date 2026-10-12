<?php

session_start();

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

// Pastikan data berasal dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$id = $_POST['id'] ?? '';
$nama_produk = trim($_POST['nama_produk'] ?? '');
$kategori_id = $_POST['kategori_id'] ?? '';
$supplier_id = $_POST['supplier_id'] ?? '';
$stok = $_POST['stok'] ?? '';
$harga = $_POST['harga'] ?? '';

// Validasi
if (
    $id === '' ||
    $nama_produk === '' ||
    $kategori_id === '' ||
    $supplier_id === '' ||
    $stok === '' ||
    $harga === ''
) {
    $_SESSION['error'] = 'Semua data produk wajib diisi.';
    header('Location: index.php');
    exit;
}

if (!is_numeric($id)) {
    $_SESSION['error'] = 'ID produk tidak valid.';
    header('Location: index.php');
    exit;
}

if (!is_numeric($kategori_id) || !is_numeric($supplier_id)) {
    $_SESSION['error'] = 'Kategori atau supplier tidak valid.';
    header('Location: index.php');
    exit;
}

if (!is_numeric($stok) || $stok < 0) {
    $_SESSION['error'] = 'Stok tidak valid.';
    header('Location: index.php');
    exit;
}

if (!is_numeric($harga) || $harga < 0) {
    $_SESSION['error'] = 'Harga tidak valid.';
    header('Location: index.php');
    exit;
}

try {

    // Prepared statement untuk UPDATE
    $sql = "
        UPDATE produk
        SET
            nama_produk = :nama_produk,
            kategori_id = :kategori_id,
            supplier_id = :supplier_id,
            stok = :stok,
            harga = :harga
        WHERE id = :id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':nama_produk' => $nama_produk,
        ':kategori_id' => $kategori_id,
        ':supplier_id' => $supplier_id,
        ':stok' => $stok,
        ':harga' => $harga,
        ':id' => $id
    ]);

    $_SESSION['success'] = 'Produk berhasil diperbarui.';

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['error'] = 'Produk gagal diperbarui.';

    header('Location: index.php');
    exit;
}