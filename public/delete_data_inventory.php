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
    $_SESSION['info'] = ['status'=>'danger','message'=>'ID tidak valid.'];
    header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
    exit;
}

// 3. Hapus dari database MySQL
$conn = $db->getConnection();
$stmt = $conn->prepare("DELETE FROM data_inventory WHERE id = :id");
$stmt->execute([':id' => $id]);
Logger::info("DataInventory ID {$id} dihapus dari database.");

// 4. Hapus vector di Pinecone
//    Pastikan di bootstrap.php atau environment sudah tersedia:
//      - PINECONE_API_KEY
//      - PINECONE_INDEX_NAME
//      - PINECONE_ENVIRONMENT
//      - PINECONE_NAMESPACE (jika Anda pakai namespace khusus)
$apiKey      = getenv('PINECONE_API_KEY');
$indexName   = getenv('PINECONE_INDEX_NAME');
$environment = getenv('PINECONE_ENVIRONMENT');
$namespace   = getenv('PINECONE_NAMESPACE') ?: ''; 

$url = "https://{$indexName}.pinecone.io/vectors/delete";
$payload = [
    'ids'       => [(string)$id],
    'namespace' => $namespace,
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Api-Key: ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
]);

$response = curl_exec($ch);
if ($response === false) {
    Logger::error("Gagal menghapus vector Pinecone ID {$id}: " . curl_error($ch));
} else {
    Logger::info("Pinecone delete response for ID {$id}: {$response}");
}
curl_close($ch);

// 5. Redirect dengan notifikasi
$_SESSION['info'] = [
    'status'  => 'success',
    'message' => "Data ID {$id} berhasil dihapus."
];
header("Location: view_data.php?owner=" . urlencode($ownerFilter) . "&page={$page}");
exit;
