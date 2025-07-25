<?php
// public/edit_rawdata.php
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

// Mulai logging
Logger::debug('--- Start edit_rawdata.php ---');

$id          = (int)($_POST['id'] ?? 0);
$owner       = trim($_POST['owner'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$email       = trim($_POST['email'] ?? '');
$rawdata     = trim($_POST['rawdata'] ?? '');
$description = trim($_POST['description'] ?? '');

// Log input yang diterima
Logger::debug('Input received', [
    'id'          => $id,
    'owner'       => $owner,
    'phone'       => $phone,
    'email'       => $email,
    'rawdata'     => substr($rawdata, 0, 100) . (strlen($rawdata) > 100 ? '…' : ''),
    'description' => substr($description, 0, 100) . (strlen($description) > 100 ? '…' : '')
]);

// Buat timestamp Jakarta
try {
    $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $lastupdatedate = $date->format('Y-m-d H:i:s');
    Logger::debug("Generated Jakarta timestamp: {$lastupdatedate}");
} catch (Exception $e) {
    Logger::error('Failed to create Jakarta timestamp: ' . $e->getMessage());
    // fallback ke UTC
    $lastupdatedate = (new DateTime())->format('Y-m-d H:i:s');
    Logger::debug("Fallback UTC timestamp: {$lastupdatedate}");
}

$sql = "UPDATE data_inventory
        SET owner          = :owner,
            phone          = :phone,
            email          = :email,
            rawdata        = :rawdata,
            description    = :description,
            lastupdatedate = :lastupdatedate,
            ispinecone= 0 ,
            metadata_pinecone =''
        WHERE id = :id";

Logger::debug('Preparing SQL statement', ['sql' => $sql]);

try {
    $stmt = $db->getConnection()->prepare($sql);
    $stmt->execute([
        ':owner'          => $owner,
        ':phone'          => $phone,
        ':email'          => $email,
        ':rawdata'        => $rawdata,
        ':description'    => $description,
        ':lastupdatedate' => $lastupdatedate,
        ':id'             => $id,
    ]);
    Logger::debug("Execute succeeded for id {$id}");
    
    $_SESSION['info'] = [
        'status'  => 'success',
        'message' => 'Data berhasil diperbarui.'
    ];
} catch (PDOException $e) {
    Logger::error('Database update failed: ' . $e->getMessage(), [
        'code' => $e->getCode(),
        'id'   => $id
    ]);
    $_SESSION['info'] = [
        'status'  => 'danger',
        'message' => 'Terjadi kesalahan saat memperbarui data.'
    ];
}

// Redirect kembali ke halaman view
$ownerFilter = urlencode($_POST['ownerFilter'] ?? '');
$page        = (int)($_POST['page'] ?? 1);
Logger::debug("Redirecting back to view_data.php?owner={$ownerFilter}&page={$page}");

header("Location: view_data.php?owner={$ownerFilter}&page={$page}");
exit;
