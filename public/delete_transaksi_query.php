<?php
// public/delete_transaksi_query.php
/*
 * 🤖 Aplikasi Brooker AI / Calo AI Palugada
 * (Apa elo mau, gw ada)
 * Dibuat oleh: Kukuh TW
 *
 * 📧 Email     : kukuhtw@gmail.com
 * 📱 WhatsApp  : 628129893706
 * 📷 Instagram : @kukuhtw
 * 🐦 X/Twitter : @kukuhtw
 * 👍 Facebook  : https://www.facebook.com/kukuhtw
 * 💼 LinkedIn  : https://id.linkedin.com/in/kukuhtw
*/
require __DIR__ . '/../bootstrap.php';
session_start();

// Pastikan user admin
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

try {
    // Ambil dan validasi ID
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new \InvalidArgumentException('ID tidak valid.');
    }

    Logger::debug("delete_transaksi_query.php - Deleting record ID {$id}");

    // Hapus record
    $sql = "DELETE FROM transaksi_query WHERE id = :id";
    $stmt = $db->getConnection()->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    Logger::debug("delete_transaksi_query.php - Record ID {$id} deleted");

    // Notifikasi sukses
    $_SESSION['info'] = [
        'status'  => 'success',
        'message' => 'Data berhasil dihapus.'
    ];

} catch (\Exception $e) {
    // Log dan notifikasi error
    Logger::error("delete_transaksi_query.php - Error deleting ID {$id}: " . $e->getMessage());

    $_SESSION['info'] = [
        'status'  => 'danger',
        'message' => 'Gagal menghapus data: ' . $e->getMessage()
    ];
}

// Redirect kembali ke daftar
header('Location: view_transaksi_query.php');
exit;
