<?php
// public/delete_attachment.php
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
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Ambil data dari POST
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$dataId = isset($_POST['data_id']) ? (int)$_POST['data_id'] : 0;
$ownerFilter = trim($_POST['ownerFilter'] ?? '');
$page = max(1, (int)($_POST['page'] ?? 1));

if ($id && $dataId) {
    // Hapus record attachment
    $sqlDel = "DELETE FROM data_attachment WHERE id = :id";
    $stmtDel = $db->getConnection()->prepare($sqlDel);
    $stmtDel->execute([':id' => $id]);
    
    // Optional: Anda dapat menghapus file fisik jika diperlukan
    // $url = $db->getConnection()->prepare("SELECT url_attachment FROM data_attachment WHERE id = :id");
    // ... fetch dan unlink
    
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'Lampiran berhasil dihapus.'
    ];
} else {
    $_SESSION['info'] = [
        'status' => 'danger',
        'message' => 'Gagal menghapus lampiran.'
    ];
}

// Redirect kembali ke view_attachment
$query = http_build_query([
    'data_id' => $dataId,
    'ownerFilter' => $ownerFilter,
    'page' => $page
]);
header('Location: view_attachment.php?' . $query);
exit;

?>