<?php
session_start();

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

// 1. Ambil data statistik ringkasan inventaris
$statQuery = $db->query("
    SELECT 
        COUNT(*) AS total_produk,
        COALESCE(SUM(stok), 0) AS total_stok,
        COALESCE(SUM(stok * harga), 0) AS total_nilai,
        COALESCE(SUM(CASE WHEN stok <= 5 THEN 1 ELSE 0 END), 0) AS stok_kritis
    FROM produk
");
$stats = $statQuery->fetch(PDO::FETCH_ASSOC);

// 2. Filter Pencarian
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT
        produk.id,
        produk.nama_produk,
        kategori.nama_kategori,
        supplier.nama_supplier,
        produk.stok,
        produk.harga
    FROM produk
    INNER JOIN kategori ON produk.kategori_id = kategori.id
    INNER JOIN supplier ON produk.supplier_id = supplier.id
";

$params = [];
if ($search !== '') {
    $sql .= " WHERE produk.nama_produk LIKE :search 
              OR kategori.nama_kategori LIKE :search 
              OR supplier.nama_supplier LIKE :search";
    $params[':search'] = "%{$search}%";
}

$sql .= " ORDER BY produk.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventaris Pro</title>

    <!-- Google Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- Top Navigation Bar -->
    <header class="topbar">
        <div class="brand-section">
            <div class="brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
            <div>
                <h1 class="brand-title">Inventaris<span>Pro</span></h1>
                <p class="brand-tagline">Sistem Manajemen & Kontrol Stok Produk</p>
            </div>
        </div>

        <div class="topbar-actions">
            <a href="export.php" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Export CSV
            </a>
            <a href="create.php" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Produk
            </a>
        </div>
    </header>

    <!-- Notifikasi Alert -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Statistik Ringkasan (KPI Cards) -->
    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Jenis Produk</p>
                <h3 class="stat-value"><?= number_format((int)$stats['total_produk'], 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-emerald">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <p class="stat-label">Estimasi Nilai Aset</p>
                <h3 class="stat-value">Rp <?= number_format((float)$stats['total_nilai'], 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-indigo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div>
                <p class="stat-label">Total Stok Unit</p>
                <h3 class="stat-value"><?= number_format((int)$stats['total_stok'], 0, ',', '.') ?> <span class="stat-unit">Item</span></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon <?= $stats['stok_kritis'] > 0 ? 'stat-rose' : 'stat-slate' ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <p class="stat-label">Stok Kritis (≤ 5)</p>
                <h3 class="stat-value <?= $stats['stok_kritis'] > 0 ? 'text-danger' : '' ?>">
                    <?= number_format((int)$stats['stok_kritis'], 0, ',', '.') ?>
                </h3>
            </div>
        </div>
    </section>

    <!-- Main Card: Table & Filter -->
    <main class="main-card">
        
        <!-- Filter Toolbar -->
        <div class="table-toolbar">
            <form method="GET" action="index.php" class="search-form">
                <div class="input-with-icon">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        type="text"
                        name="search"
                        placeholder="Cari nama produk, kategori, atau supplier..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>
                <button type="submit" class="btn btn-dark">Cari</button>
                <?php if ($search !== ''): ?>
                    <a href="index.php" class="btn btn-light">Reset</a>
                <?php endif; ?>
            </form>

            <div class="filter-meta">
                Menampilkan <strong><?= count($produk) ?></strong> produk
            </div>
        </div>

        <!-- Tabel Inventaris -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th style="text-align: center;">Status & Stok</th>
                        <th>Harga Satuan</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produk) > 0): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($produk as $item): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="product-cell">
                                        <span class="product-name"><?= htmlspecialchars($item['nama_produk']) ?></span>
                                        <span class="product-id">SKU-#<?= str_pad((string)$item['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-kategori"><?= htmlspecialchars($item['nama_kategori']) ?></span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                                        <span><?= htmlspecialchars($item['nama_supplier']) ?></span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($item['stok'] <= 5): ?>
                                        <span class="stock-pill pill-danger" title="Stok hampir habis!">
                                            <span class="dot pulse"></span>
                                            <?= htmlspecialchars((string)$item['stok']) ?> unit
                                        </span>
                                    <?php else: ?>
                                        <span class="stock-pill pill-success">
                                            <span class="dot"></span>
                                            <?= htmlspecialchars((string)$item['stok']) ?> unit
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="price-cell">
                                    Rp <?= number_format((float)$item['harga'], 0, ',', '.') ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="actions-group">
                                        <a href="edit.php?id=<?= htmlspecialchars((string)$item['id']) ?>" class="action-btn btn-edit" title="Edit Data">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                            Edit
                                        </a>
                                        <a 
                                            href="delete.php?id=<?= htmlspecialchars((string)$item['id']) ?>" 
                                            class="action-btn btn-delete" 
                                            title="Hapus Produk"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus <?= htmlspecialchars(addslashes($item['nama_produk'])) ?>?')"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-icon">
                                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                </div>
                                <h4>Data produk tidak ditemukan</h4>
                                <p>Coba gunakan kata kunci pencarian lain atau klik tombol reset.</p>
                                <?php if ($search !== ''): ?>
                                    <a href="index.php" class="btn btn-secondary">Tampilkan Semua Produk</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>