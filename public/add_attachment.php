<?php
// ========== public/add_attachment.php ==========
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

// ========== public/add_attachment.php ==========
require __DIR__ . '/../bootstrap.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Fetch context dari GET (atau POST nanti)
$dataId      = isset($_GET['data_id']) ? (int)$_GET['data_id'] : null;
$ownerFilter = trim($_GET['ownerFilter'] ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));

Logger::debug("add_attachment.php accessed", [
    'method'       => $_SERVER['REQUEST_METHOD'],
    'dataId_init'  => $dataId,
    'ownerFilter'  => $ownerFilter,
    'page'         => $page
]);

// Handle POST upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_id'])) {
    $dataId      = (int)$_POST['data_id'];
    $ownerFilter = trim($_POST['ownerFilter'] ?? '');
    $page        = max(1, (int)($_POST['page'] ?? 1));

    Logger::debug("Processing POST upload", [
        'dataId'      => $dataId,
        'ownerFilter' => $ownerFilter,
        'page'        => $page,
        'file_error'  => $_FILES['attachment']['error'] ?? 'none'
    ]);

    if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        Logger::error("Upload error", ['error_code' => $_FILES['attachment']['error']]);
        $_SESSION['info'] = ['status' => 'danger', 'message' => 'Gagal mengunggah berkas.'];
    } else {
        // Direktori upload
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            Logger::debug("Created upload directory", ['path' => $uploadDir]);
        }

        // Original name + extension
        $originalName = basename($_FILES['attachment']['name']);
        $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        Logger::debug("File extension extracted", ['originalName' => $originalName, 'ext' => $ext]);

        // Validasi ekstensi
        $allowedExt = ['jpg','jpeg','png','webp','doc','docx','pdf','mp4'];
        if (!in_array($ext, $allowedExt)) {
            Logger::warning("Invalid extension", ['ext' => $ext]);
            $_SESSION['info'] = [
                'status'  => 'danger',
                'message' => 'Tipe berkas tidak diizinkan.'
            ];
            header("Location: view_data.php?owner={$ownerFilter}&page={$page}");
            exit;
        }

        // Cek MIME
        $finfo       = new finfo(FILEINFO_MIME_TYPE);
        $mime        = $finfo->file($_FILES['attachment']['tmp_name']);
        $allowedMime = [
            'image/jpeg','image/png','image/webp',
            'application/pdf',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'video/mp4'
        ];
        Logger::debug("Detected MIME type", ['mime' => $mime]);

        if (!in_array($mime, $allowedMime)) {
            Logger::warning("Invalid MIME type", ['mime' => $mime]);
            $_SESSION['info'] = [
                'status'  => 'danger',
                'message' => 'MIME berkas tidak diizinkan.'
            ];
            header("Location: view_data.php?owner={$ownerFilter}&page={$page}");
            exit;
        }

        // Generate nama aman & target path
        $safeName   = uniqid('att_') . '.' . $ext;
        $targetPath = $uploadDir . $safeName;
        Logger::debug("Moving uploaded file", [
            'tmp_name'   => $_FILES['attachment']['tmp_name'],
            'targetPath' => $targetPath
        ]);

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
            $url = "/uploads/{$safeName}";
            Logger::info("File moved successfully", [
                'dataId'   => $dataId,
                'origName' => $originalName,
                'safeName' => $safeName,
                'url'      => $url
            ]);

            // Simpan record attachment
            require __DIR__ . '/../src/DataAttachment.php';
            $da = new DataAttachment($db);
            $da->add($dataId, $originalName, $url);
            Logger::info("DataAttachment::add() executed", ['dataId' => $dataId]);

            // UPDATE lastupdatedate
            $upd = $db->getConnection()->prepare("
                UPDATE data_inventory
                   SET lastupdatedate = NOW()
                 WHERE id = :id
            ");
            $upd->execute([':id' => $dataId]);
            Logger::info("Updated lastupdatedate", ['dataId' => $dataId]);

            $_SESSION['info'] = ['status' => 'success', 'message' => 'Attachment berhasil ditambahkan.'];
        } else {
            Logger::error("move_uploaded_file() failed", ['tmp_name' => $_FILES['attachment']['tmp_name']]);
            $_SESSION['info'] = ['status' => 'danger', 'message' => 'Gagal memindahkan berkas.'];
        }
    }

    header("Location: view_data.php?owner={$ownerFilter}&page={$page}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Attachment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex">
     <?php include 'nav_admin.php'; ?>
      <div id="content" class="flex-grow-1 p-4">
  <h1 class="mb-4">Tambah Attachment untuk Data #<?= htmlspecialchars($dataId) ?></h1>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="data_id" value="<?= htmlspecialchars($dataId) ?>">
    <input type="hidden" name="ownerFilter" value="<?= htmlspecialchars($ownerFilter) ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
    <div class="mb-3">
      <label for="attachment" class="form-label">Pilih berkas (gambar/pdf/doc)</label>
    
    <input type="file" name="attachment" id="attachment" class="form-control"
       accept=".jpg,.jpeg,.png,.webp,.doc,.docx,.pdf,.mp4"
       required>



    </div>
    <button type="submit" class="btn btn-primary">Unggah</button>
    <a href="view_data.php?owner=<?= urlencode($ownerFilter) ?>&page=<?= $page ?>" class="btn btn-secondary">Batal</a>
  </form>
</body>
</html>

