<?php
// public/delete_data_inventory.php

require __DIR__ . '/../bootstrap.php';
session_start();

// 1. Autentikasi
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// 2. Ambil parameter
$id          = $_POST['id']          ?? null;
$ownerFilter = $_POST['ownerFilter'] ?? '';
$page        = (int) ($_POST['page']  ?? 1);

if (!$id) {
    $_SESSION['info'] = ['status' => 'danger', 'message' => 'ID tidak valid.'];
    header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
    exit;
}

// 3. Ambil settings dari database
$conn = $db->getConnection();
try {
    $keys = ['OPEN_AI_KEY', 'PINECONE_API_KEY', 'PINECONE_INDEX_NAME', 'PINECONE_NAMESPACE'];
    $in   = str_repeat('?,', count($keys) - 1) . '?';
    $stmt = $conn->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ($in)");
    $stmt->execute($keys);
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $pineconeApiKey    = $settings['PINECONE_API_KEY']    ?? '';
    $pineconeIndexName = $settings['PINECONE_INDEX_NAME'] ?? '';
    $namespace         = $settings['PINECONE_NAMESPACE']  ?? '';
} catch (Exception $e) {
    Logger::error("Gagal mengambil konfigurasi Pinecone: " . $e->getMessage());
    $_SESSION['info'] = ['status' => 'danger', 'message' => 'Gagal mengambil konfigurasi Pinecone.'];
    header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
    exit;
}

// 4. Hapus vector dari Pinecone
$url = "https://{$pineconeIndexName}.pinecone.io/vectors/delete";
$payload = [
    'ids'       => [(string)$id],
    'namespace' => $namespace,
];

Logger::debug("Mengirim permintaan hapus ke Pinecone: " . json_encode($payload));

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Api-Key: ' . $pineconeApiKey,
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode >= 400) {
    Logger::error("Gagal menghapus vector Pinecone ID {$id}. HTTP {$httpCode}, Error: {$error}, Response: {$response}");
    $_SESSION['info'] = ['status' => 'danger', 'message' => "Gagal menghapus data ID {$id} dari Pinecone."];
    header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
    exit;
}

Logger::info("Vector ID {$id} berhasil dihapus dari Pinecone. Response: {$response}");

// 5. Hapus dari MySQL setelah Pinecone berhasil
$stmt = $conn->prepare("DELETE FROM data_inventory WHERE id = :id");
$stmt->execute([':id' => $id]);
Logger::info("DataInventory ID {$id} dihapus dari database.");

// 6. Redirect dengan notifikasi sukses
$_SESSION['info'] = ['status' => 'success', 'message' => "Data ID {$id} berhasil dihapus dari Pinecone dan MySQL."];
header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
exit;
