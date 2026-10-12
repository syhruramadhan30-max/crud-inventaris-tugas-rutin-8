<?php

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

$sql = "
    SELECT
        produk.nama_produk,
        kategori.nama_kategori,
        supplier.nama_supplier,
        produk.stok,
        produk.harga
    FROM produk
    INNER JOIN kategori
        ON produk.kategori_id = kategori.id
    INNER JOIN supplier
        ON produk.supplier_id = supplier.id
    ORDER BY produk.id ASC
";

$stmt = $db->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="laporan_inventaris.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Nama Produk',
    'Kategori',
    'Supplier',
    'Stok',
    'Harga'
]);

foreach ($data as $row) {

    fputcsv($output, [
        $row['nama_produk'],
        $row['nama_kategori'],
        $row['nama_supplier'],
        $row['stok'],
        $row['harga']
    ]);

}

fclose($output);
exit;