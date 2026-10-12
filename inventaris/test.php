<?php

require_once 'config/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Koneksi database berhasil!";
    
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}