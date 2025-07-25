<?php
// public/update_followup.php
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
    // --- 1. Ambil & validasi input ---
    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $followup = trim($_POST['followup'] ?? '');

    if ($id <= 0) {
        throw new \InvalidArgumentException('ID tidak valid.');
    }

    Logger::debug("update_followup.php - Updating followup for ID {$id}");

    // --- 2. Update ke database ---
    $sql = "UPDATE transaksi_query
            SET followup = :followup
            WHERE id = :id";
    $stmt = $db->getConnection()->prepare($sql);
    $stmt->bindValue(':followup', $followup, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    Logger::debug("update_followup.php - Followup updated for ID {$id}");

    // --- 3. Siapkan notifikasi sukses ---
    $_SESSION['info'] = [
        'status'  => 'success',
        'message' => 'Followup berhasil disimpan.'
    ];

} catch (\Exception $e) {
    // Log error dan siapkan notifikasi gagal
    Logger::error("update_followup.php - Error: " . $e->getMessage());

    $_SESSION['info'] = [
        'status'  => 'danger',
        'message' => 'Gagal menyimpan followup: ' . $e->getMessage()
    ];
}

// Redirect kembali ke daftar
header('Location: view_transaksi_query.php');
exit;
