<?php

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

// Mengambil data kategori
$stmtKategori = $db->prepare("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$stmtKategori->execute();
$kategori = $stmtKategori->fetchAll();

// Mengambil data supplier
$stmtSupplier = $db->prepare("SELECT * FROM supplier ORDER BY nama_supplier ASC");
$stmtSupplier->execute();
$supplier = $stmtSupplier->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>
</head>

<body>

    <h1>Tambah Produk</h1>

    <form action="store.php" method="POST">

        <p>
            <label>Nama Produk</label><br>
            <input
                type="text"
                name="nama_produk"
                required
            >
        </p>

        <p>
            <label>Kategori</label><br>

            <select name="kategori_id" required>

                <option value="">
                    -- Pilih Kategori --
                </option>

                <?php foreach ($kategori as $item): ?>

                    <option value="<?= htmlspecialchars($item['id']) ?>">
                        <?= htmlspecialchars($item['nama_kategori']) ?>
                    </option>

                <?php endforeach; ?>

            </select>
        </p>

        <p>
            <label>Supplier</label><br>

            <select name="supplier_id" required>

                <option value="">
                    -- Pilih Supplier --
                </option>

                <?php foreach ($supplier as $item): ?>

                    <option value="<?= htmlspecialchars($item['id']) ?>">
                        <?= htmlspecialchars($item['nama_supplier']) ?>
                    </option>

                <?php endforeach; ?>

            </select>
        </p>

        <p>
            <label>Stok</label><br>

            <input
                type="number"
                name="stok"
                min="0"
                required
            >
        </p>

        <p>
            <label>Harga</label><br>

            <input
                type="number"
                name="harga"
                min="0"
                required
            >
        </p>

        <p>
            <button type="submit">
                Simpan Produk
            </button>

            <a href="index.php">
                Kembali
            </a>
        </p>

    </form>

</body>

</html>