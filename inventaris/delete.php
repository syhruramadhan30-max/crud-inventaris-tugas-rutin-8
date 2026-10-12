<?php

session_start();

require_once 'config/Database.php';

$db = Database::getInstance()->getConnection();

$id = $_GET['id'] ?? '';

if ($id === '' || !is_numeric($id)) {
    $_SESSION['error'] = 'ID produk tidak valid.';
    header('Location: index.php');
    exit;
}

try {

    $sql = "
        DELETE FROM produk
        WHERE id = :id
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);
    $logSql = "
    INSERT INTO log_aktivitas
    (aktivitas, produk_id)
    VALUES (:aktivitas, :produk_id)
";

$logStmt = $db->prepare($logSql);

$logStmt->execute([
    ':aktivitas' => 'Menghapus produk',
    ':produk_id' => $id
]);

    $_SESSION['success'] = 'Produk berhasil dihapus.';

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['error'] = 'Produk gagal dihapus.';

    header('Location: index.php');
    exit;
}